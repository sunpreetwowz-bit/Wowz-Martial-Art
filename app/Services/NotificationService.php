<?php

namespace App\Services;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\BeltTest;
use App\Models\BeltTestApplication;
use App\Models\BeltTestResult;
use App\Models\Certificate;
use App\Models\CompetitionForm;
use App\Models\CompetitionFormAssignment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AcademyAlert;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send a database (+ optional email) alert to one or more users.
     *
     * @param  User|Collection|array  $users
     * @param  array<string, mixed>  $meta
     */
    public function send(
        User|Collection|array $users,
        string $title,
        string $message,
        ?string $actionUrl = null,
        string $level = 'info',
        array $meta = [],
    ) {
        // Always work with a simple list of User models
        if ($users instanceof User) {
            $recipients = collect([$users]);
        } elseif ($users instanceof Collection) {
            $recipients = $users;
        } else {
            $recipients = collect($users);
        }

        $recipients = $recipients
            ->filter(fn ($user) => $user instanceof User)
            ->unique('id')
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        $sendNow = function () use ($recipients, $title, $message, $actionUrl, $level, $meta) {
            Notification::send(
                $recipients,
                new AcademyAlert($title, $message, $actionUrl, $level, $meta)
            );
        };

        // Wait until DB transaction finishes, so failed rollsbacks do not notify
        if (DB::transactionLevel() > 0) {
            DB::afterCommit($sendNow);
        } else {
            $sendNow();
        }
    }

    public function notifyAdmins(string $title, string $message, ?string $actionUrl = null, array $meta = [])
    {
        $admins = User::query()
            ->where('role', UserRole::Admin)
            ->where('status', AccountStatus::Active)
            ->get();

        $this->send($admins, $title, $message, $actionUrl, 'info', $meta);
    }

    public function beltTestAssigned(Student $student, BeltTest $beltTest)
    {
        if (! $student->user) {
            return;
        }

        $this->send(
            $student->user,
            'Belt test assigned',
            "You have been assigned to \"{$beltTest->title}\" on {$beltTest->test_date->format('d M Y')}.",
            route('student.belt-tests.show', $beltTest, false),
            'info',
            ['event' => 'belt_test_assigned', 'belt_test_id' => $beltTest->id],
        );
    }

    public function applicationSubmitted(BeltTestApplication $application)
    {
        $application->loadMissing(['student.user', 'beltTest']);

        $this->notifyAdmins(
            'Belt test application submitted',
            "{$application->student->user->name} applied for \"{$application->beltTest->title}\".",
            route('admin.belt-test-applications.show', $application, false),
            ['event' => 'application_submitted', 'application_id' => $application->id],
        );

        if ($application->student->user) {
            $this->send(
                $application->student->user,
                'Application received',
                "Your application for \"{$application->beltTest->title}\" was submitted successfully.",
                route('student.applications.show', $application, false),
                'success',
                ['event' => 'application_submitted', 'application_id' => $application->id],
            );
        }
    }

    public function applicationReviewed(BeltTestApplication $application)
    {
        $application->loadMissing(['student.user', 'beltTest']);

        if (! $application->student->user) {
            return;
        }

        $status = $application->status?->label() ?? 'updated';

        $this->send(
            $application->student->user,
            'Application '.$status,
            "Your application for \"{$application->beltTest->title}\" is now {$status}.",
            route('student.applications.show', $application, false),
            $application->status?->value === 'rejected' ? 'warning' : 'info',
            ['event' => 'application_reviewed', 'application_id' => $application->id, 'status' => $application->status?->value],
        );
    }

    public function paymentVerified(Payment $payment)
    {
        $payment->loadMissing(['student.user', 'application.beltTest']);

        if (! $payment->student?->user) {
            return;
        }

        $testTitle = $payment->application?->beltTest?->title ?? 'your belt test';

        $this->send(
            $payment->student->user,
            'Payment verified',
            "Your payment for \"{$testTitle}\" has been verified.",
            $payment->application
                ? route('student.applications.show', $payment->application, false)
                : null,
            'success',
            ['event' => 'payment_verified', 'payment_id' => $payment->id],
        );
    }

    public function paymentFailed(Payment $payment)
    {
        $payment->loadMissing(['student.user', 'application.beltTest']);

        if (! $payment->student?->user) {
            return;
        }

        $testTitle = $payment->application?->beltTest?->title ?? 'your belt test';

        $this->send(
            $payment->student->user,
            'Payment failed',
            "Your online payment for \"{$testTitle}\" could not be verified. Contact the academy if you were charged.",
            $payment->application
                ? route('student.applications.show', $payment->application, false)
                : null,
            'danger',
            ['event' => 'payment_failed', 'payment_id' => $payment->id],
        );
    }

    public function resultRecorded(BeltTestResult $result)
    {
        $result->loadMissing(['student.user', 'beltTest']);

        if (! $result->student?->user) {
            return;
        }

        $outcome = $result->outcome?->label() ?? 'recorded';

        $this->send(
            $result->student->user,
            'Belt test result: '.$outcome,
            "Your result for \"{$result->beltTest?->title}\" is {$outcome}.",
            route('student.applications.index', false),
            $result->isPassed() ? 'success' : 'warning',
            ['event' => 'result_recorded', 'result_id' => $result->id, 'outcome' => $result->outcome?->value],
        );
    }

    public function certificateIssued(Certificate $certificate)
    {
        $certificate->loadMissing(['student.user']);

        if (! $certificate->student?->user) {
            return;
        }

        $this->send(
            $certificate->student->user,
            'Certificate issued',
            "Your certificate for {$certificate->belt_name_snapshot} ({$certificate->certificate_number}) is ready.",
            route('student.certificates.show', $certificate, false),
            'success',
            ['event' => 'certificate_issued', 'certificate_id' => $certificate->id],
        );
    }

    public function certificateRevoked(Certificate $certificate)
    {
        $certificate->loadMissing(['student.user']);

        if (! $certificate->student?->user) {
            return;
        }

        $this->send(
            $certificate->student->user,
            'Certificate revoked',
            "Certificate {$certificate->certificate_number} has been revoked.",
            route('student.certificates.show', $certificate, false),
            'warning',
            ['event' => 'certificate_revoked', 'certificate_id' => $certificate->id],
        );
    }

    public function competitionAssigned(Student $student, CompetitionForm $form)
    {
        if (! $student->user) {
            return;
        }

        $deadline = $form->deadline_at
            ? ' Deadline: '.$form->deadline_at->format('d M Y H:i').'.'
            : '';

        $this->send(
            $student->user,
            'Competition form assigned',
            "\"{$form->title}\" has been assigned to you.{$deadline}",
            route('student.competition-forms.show', $form, false),
            'info',
            ['event' => 'competition_assigned', 'competition_form_id' => $form->id],
        );
    }

    public function competitionResponded(CompetitionFormAssignment $assignment)
    {
        $assignment->loadMissing(['competitionForm', 'student.user']);

        $form = $assignment->competitionForm;
        $studentName = $assignment->student?->user?->name ?? 'A student';

        if (! $form) {
            return;
        }

        $this->notifyAdmins(
            'Competition form response marked',
            "{$studentName} marked \"{$form->title}\" as responded.",
            route('admin.competition-forms.show', $form, false),
            ['event' => 'competition_responded', 'competition_form_id' => $form->id, 'student_id' => $assignment->student_id],
        );
    }

    public function beltTestDeadlineApproaching(Student $student, BeltTest $beltTest, string $closesAtLabel)
    {
        if (! $student->user || $this->alreadyNotified($student->user, 'belt_test_deadline', 'belt_test_id', $beltTest->id)) {
            return;
        }

        $this->send(
            $student->user,
            'Application deadline approaching',
            "Applications for \"{$beltTest->title}\" close at {$closesAtLabel}.",
            route('student.belt-tests.show', $beltTest, false),
            'warning',
            ['event' => 'belt_test_deadline', 'belt_test_id' => $beltTest->id],
        );
    }

    public function competitionDeadlineApproaching(Student $student, CompetitionForm $form)
    {
        if (! $student->user || $this->alreadyNotified($student->user, 'competition_deadline', 'competition_form_id', $form->id)) {
            return;
        }

        $this->send(
            $student->user,
            'Competition deadline approaching',
            "\"{$form->title}\" is due by {$form->deadline_at->format('d M Y H:i')}.",
            route('student.competition-forms.show', $form, false),
            'warning',
            ['event' => 'competition_deadline', 'competition_form_id' => $form->id],
        );
    }

    protected function alreadyNotified(User $user, string $event, string $metaKey, int|string $metaValue)
    {
        return $user->notifications()
            ->where('type', AcademyAlert::class)
            ->where('created_at', '>=', now()->subDay())
            ->where('data->meta->event', $event)
            ->where("data->meta->{$metaKey}", $metaValue)
            ->exists();
    }
}
