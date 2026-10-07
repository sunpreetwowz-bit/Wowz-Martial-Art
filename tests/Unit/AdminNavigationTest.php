<?php

namespace Tests\Unit;

use App\Support\AdminNavigation;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    public function test_navigation_marks_existing_routes_available(): void
    {
        $items = AdminNavigation::items();

        $dashboard = collect($items)->firstWhere('label', 'Dashboard');

        $this->assertNotNull($dashboard);
        $this->assertTrue($dashboard['available']);
        $this->assertTrue(Route::has('admin.dashboard'));
    }

    public function test_website_module_routes_are_available(): void
    {
        $items = AdminNavigation::items();

        $website = collect($items)->firstWhere('label', 'Website');
        $services = collect($website['children'])->firstWhere('label', 'Services');

        $this->assertTrue($services['available']);
        $this->assertNotNull($services['url']);
    }

    public function test_student_module_routes_are_available(): void
    {
        $items = AdminNavigation::items();

        $students = collect($items)->firstWhere('label', 'Students');
        $list = collect($students['children'])->firstWhere('label', 'Student List');
        $belts = collect($students['children'])->firstWhere('label', 'Belts');

        $this->assertTrue($list['available']);
        $this->assertTrue($belts['available']);
    }

    public function test_belt_test_module_routes_are_available(): void
    {
        $items = AdminNavigation::items();

        $beltTests = collect($items)->firstWhere('label', 'Belt Tests');
        $allTests = collect($beltTests['children'])->firstWhere('label', 'All Tests');
        $applications = collect($beltTests['children'])->firstWhere('label', 'Applications');
        $results = collect($beltTests['children'])->firstWhere('label', 'Results');
        $payments = collect($beltTests['children'])->firstWhere('label', 'Payments');

        $this->assertTrue($allTests['available']);
        $this->assertTrue($applications['available']);
        $this->assertTrue($results['available']);
        $this->assertTrue($payments['available']);
    }

    public function test_certificate_and_competition_routes_are_available(): void
    {
        $items = AdminNavigation::items();

        $certificates = collect($items)->firstWhere('label', 'Certificates');
        $competitions = collect($items)->firstWhere('label', 'Competition Forms');

        $this->assertTrue($certificates['available']);
        $this->assertTrue($competitions['available']);
    }

    public function test_notifications_and_audit_routes_are_available(): void
    {
        $items = AdminNavigation::items();

        $notifications = collect($items)->firstWhere('label', 'Notifications');
        $audit = collect($items)->firstWhere('label', 'Audit Logs');

        $this->assertTrue($notifications['available']);
        $this->assertTrue($audit['available']);
    }
}
