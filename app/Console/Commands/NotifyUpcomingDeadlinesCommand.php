<?php

namespace App\Console\Commands;

use App\Enums\AccountStatus;
use App\Enums\AssignmentStatus;
use App\Enums\BeltTestStatus;
use App\Models\BeltTest;
use App\Models\CompetitionForm;
use App\Models\Student;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class NotifyUpcomingDeadlinesCommand extends Command
{
    protected $signature = 'academy:notify-deadlines';

    protected $description = 'Notify students about approaching belt-test application and competition deadlines';

    public function handle(NotificationService $notifications): int
    {
        $hours = (int) config('academy.notifications.deadline_hours', 24);
        $until = now()->addHours($hours);
        $notified = 0;

        BeltTest::query()
            ->whereIn('status', [BeltTestStatus::Open, BeltTestStatus::Scheduled])
            ->with(['assignments' => fn ($q) => $q->where('status', AssignmentStatus::Assigned)])
            ->get()
            ->each(function (BeltTest $test) use ($notifications, $until, &$notified) {
                $closesAt = $test->effectiveApplicationClosesAt();
                if ($closesAt->isPast() || $closesAt->gt($until)) {
                    return;
                }

                $students = Student::query()
                    ->with('user')
                    ->whereIn('id', $test->assignments->pluck('student_id'))
                    ->get();

                foreach ($students as $student) {
                    $notifications->beltTestDeadlineApproaching(
                        $student,
                        $test,
                        $closesAt->format('d M Y H:i')
                    );
                    $notified++;
                }
            });

        CompetitionForm::query()
            ->where('status', AccountStatus::Active)
            ->whereNotNull('deadline_at')
            ->whereBetween('deadline_at', [now(), $until])
            ->with(['assignments' => fn ($q) => $q->where('status', AssignmentStatus::Assigned)->with('student.user')])
            ->get()
            ->each(function (CompetitionForm $form) use ($notifications, &$notified) {
                foreach ($form->assignments as $assignment) {
                    if (! $assignment->student) {
                        continue;
                    }
                    $notifications->competitionDeadlineApproaching($assignment->student, $form);
                    $notified++;
                }
            });

        $this->info("Processed deadline notifications ({$notified} candidate sends; duplicates skipped).");

        return self::SUCCESS;
    }
}
