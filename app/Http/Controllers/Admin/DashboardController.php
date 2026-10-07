<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\ApplicationStatus;
use App\Enums\ContactStatus;
use App\Enums\PaymentStatus;
use App\Enums\TestResultOutcome;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\BeltTest;
use App\Models\BeltTestApplication;
use App\Models\BeltTestResult;
use App\Models\Certificate;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Support\AdminDashboardStats;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = Cache::remember(AdminDashboardStats::CACHE_KEY, now()->addMinutes(2), function () {
            return [
                'total_students' => Student::query()->count(),
                'active_students' => Student::query()->where('status', AccountStatus::Active)->count(),
                'upcoming_belt_tests' => BeltTest::query()
                    ->whereDate('test_date', '>=', now()->toDateString())
                    ->count(),
                'pending_applications' => BeltTestApplication::query()
                    ->whereIn('status', [
                        ApplicationStatus::Submitted,
                        ApplicationStatus::PaymentPending,
                        ApplicationStatus::UnderReview,
                    ])
                    ->count(),
                'passed_tests' => BeltTestResult::query()
                    ->where('outcome', TestResultOutcome::Passed)
                    ->count(),
                'failed_tests' => BeltTestResult::query()
                    ->where('outcome', TestResultOutcome::Failed)
                    ->count(),
                'total_certificates' => Certificate::query()->count(),
                'pending_payments' => Payment::query()
                    ->where('status', PaymentStatus::Pending)
                    ->count(),
                'unread_contacts' => Contact::query()
                    ->where('status', ContactStatus::Unread)
                    ->count(),
                'upcoming_events' => Event::query()
                    ->whereDate('start_date', '>=', now()->toDateString())
                    ->count(),
                'admin_users' => User::query()->where('role', UserRole::Admin)->count(),
            ];
        });

        $queues = [
            'applications' => BeltTestApplication::query()
                ->with(['student.user', 'beltTest'])
                ->whereIn('status', [
                    ApplicationStatus::Submitted,
                    ApplicationStatus::PaymentPending,
                    ApplicationStatus::UnderReview,
                ])
                ->latest('submitted_at')
                ->limit(5)
                ->get(),
            'payments' => Payment::query()
                ->with(['student.user', 'application.beltTest'])
                ->where('status', PaymentStatus::Pending)
                ->latest()
                ->limit(5)
                ->get(),
            'contacts' => Contact::query()
                ->where('status', ContactStatus::Unread)
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'queues' => $queues,
        ]);
    }
}
