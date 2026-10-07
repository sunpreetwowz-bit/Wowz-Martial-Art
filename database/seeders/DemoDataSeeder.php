<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\ApplicationStatus;
use App\Enums\AssignmentStatus;
use App\Enums\BeltHistorySource;
use App\Enums\BeltTestStatus;
use App\Enums\CompetitionResponseStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\BeltTestApplication;
use App\Models\BeltTestAssignment;
use App\Models\CompetitionForm;
use App\Models\CompetitionFormAssignment;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Student;
use App\Models\StudentBeltHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', UserRole::Admin)->first();
        $white = Belt::query()->where('rank_order', 1)->first();
        $yellow = Belt::query()->where('rank_order', 2)->first();
        $service = Service::query()->where('slug', 'taekwondo')->first()
            ?? Service::query()->first();

        if (! $admin || ! $white || ! $yellow) {
            $this->command?->warn('DemoDataSeeder skipped: run AdminUserSeeder and BeltSeeder first.');

            return;
        }

        $students = collect([
            ['name' => 'Ram Sharma', 'email' => 'ram@student.test', 'code' => 'WMA-DEMO-0001'],
            ['name' => 'Aman Verma', 'email' => 'aman@student.test', 'code' => 'WMA-DEMO-0002'],
            ['name' => 'Rahul Mehta', 'email' => 'rahul@student.test', 'code' => 'WMA-DEMO-0003'],
        ])->map(function (array $row) use ($white, $service, $admin) {
            $user = User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::Student,
                    'status' => AccountStatus::Active,
                    'email_verified_at' => now(),
                ]
            );

            $student = Student::query()->updateOrCreate(
                ['student_code' => $row['code']],
                [
                    'user_id' => $user->id,
                    'phone' => '98'.fake()->numerify('########'),
                    'joining_date' => now()->subMonths(3)->toDateString(),
                    'current_belt_id' => $white->id,
                    'primary_service_id' => $service?->id,
                    'status' => AccountStatus::Active,
                    'notes' => 'Demo student seeded for local testing.',
                ]
            );

            if (! $student->beltHistories()->exists()) {
                StudentBeltHistory::query()->create([
                    'student_id' => $student->id,
                    'from_belt_id' => null,
                    'to_belt_id' => $white->id,
                    'source' => BeltHistorySource::Initial,
                    'notes' => 'Initial belt assignment (demo seed).',
                    'created_by' => $admin->id,
                ]);
            }

            return $student->fresh('user');
        });

        $test = BeltTest::query()->updateOrCreate(
            ['slug' => 'demo-yellow-belt-grading'],
            [
                'title' => 'Demo Yellow Belt Grading',
                'target_belt_id' => $yellow->id,
                'test_date' => now()->addDays(5)->toDateString(),
                'start_time' => '17:00:00',
                'end_time' => '19:00:00',
                'application_opens_at' => now()->subDay(),
                'application_closes_at' => null,
                'fee_amount' => 500,
                'currency' => 'INR',
                'instructions' => 'Bring uniform, student ID, and arrive 30 minutes early.',
                'status' => BeltTestStatus::Open,
                'created_by' => $admin->id,
            ]
        );

        foreach ($students as $student) {
            BeltTestAssignment::query()->updateOrCreate(
                [
                    'belt_test_id' => $test->id,
                    'student_id' => $student->id,
                ],
                [
                    'status' => AssignmentStatus::Assigned,
                    'assigned_by' => $admin->id,
                    'assigned_at' => now(),
                    'revoked_at' => null,
                ]
            );
        }

        $first = $students->first();
        $application = BeltTestApplication::query()->firstOrCreate(
            [
                'belt_test_id' => $test->id,
                'student_id' => $first->id,
            ],
            [
                'current_belt_id' => $white->id,
                'target_belt_id' => $yellow->id,
                'form_data' => ['acknowledge' => true, 'seeded' => true],
                'status' => ApplicationStatus::PaymentPending,
                'submitted_at' => now()->subHours(2),
            ]
        );

        Payment::query()->firstOrCreate(
            ['idempotency_key' => 'demo-payment-'.$application->id],
            [
                'uuid' => (string) Str::uuid(),
                'belt_test_application_id' => $application->id,
                'student_id' => $first->id,
                'amount' => 500,
                'currency' => 'INR',
                'method' => PaymentMethod::Cash,
                'status' => PaymentStatus::Pending,
                'gateway' => 'cash',
                'notes' => 'Demo cash payment awaiting admin verification.',
            ]
        );

        $pdfPath = 'competition-forms/demo-punjab-championship.pdf';
        if (! Storage::disk('local')->exists($pdfPath)) {
            Storage::disk('local')->put(
                $pdfPath,
                "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n"
            );
        }

        $form = CompetitionForm::query()->updateOrCreate(
            ['slug' => 'demo-punjab-state-championship'],
            [
                'title' => 'Punjab State Taekwondo Championship',
                'description' => 'Demo competition registration form. Download, complete, and submit to the academy office.',
                'pdf_path' => $pdfPath,
                'deadline_at' => now()->addDays(10),
                'status' => AccountStatus::Active,
                'created_by' => $admin->id,
            ]
        );

        foreach ($students->take(2) as $student) {
            CompetitionFormAssignment::query()->updateOrCreate(
                [
                    'competition_form_id' => $form->id,
                    'student_id' => $student->id,
                ],
                [
                    'status' => AssignmentStatus::Assigned,
                    'response_status' => CompetitionResponseStatus::Pending,
                    'assigned_by' => $admin->id,
                    'assigned_at' => now(),
                ]
            );
        }

        $this->command?->info('Demo data ready.');
        $this->command?->table(
            ['Account', 'Email', 'Password'],
            [
                ['Admin', env('ADMIN_EMAIL', 'admin@wowzmartialart.test'), env('ADMIN_PASSWORD', 'password')],
                ['Student (Ram)', 'ram@student.test', 'password'],
                ['Student (Aman)', 'aman@student.test', 'password'],
                ['Student (Rahul)', 'rahul@student.test', 'password'],
            ]
        );
    }
}
