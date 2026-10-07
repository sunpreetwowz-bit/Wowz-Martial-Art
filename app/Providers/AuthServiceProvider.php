<?php

namespace App\Providers;

use App\Models\AboutSection;
use App\Models\Achievement;
use App\Models\AuditLog;
use App\Models\Belt;
use App\Models\BlackBelt;
use App\Models\BeltTest;
use App\Models\BeltTestApplication;
use App\Models\BeltTestResult;
use App\Models\Certificate;
use App\Models\CompetitionForm;
use App\Models\Contact;
use App\Models\Event;
use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Student;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Policies\AboutSectionPolicy;
use App\Policies\AchievementPolicy;
use App\Policies\AuditLogPolicy;
use App\Policies\BeltPolicy;
use App\Policies\BlackBeltPolicy;
use App\Policies\BeltTestApplicationPolicy;
use App\Policies\BeltTestPolicy;
use App\Policies\BeltTestResultPolicy;
use App\Policies\CertificatePolicy;
use App\Policies\CompetitionFormPolicy;
use App\Policies\ContactPolicy;
use App\Policies\EventPolicy;
use App\Policies\GalleryCategoryPolicy;
use App\Policies\GalleryImagePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ServicePolicy;
use App\Policies\StudentPolicy;
use App\Policies\TeamMemberPolicy;
use App\Policies\TestimonialPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Student::class => StudentPolicy::class,
        Belt::class => BeltPolicy::class,
        BeltTest::class => BeltTestPolicy::class,
        BeltTestApplication::class => BeltTestApplicationPolicy::class,
        BeltTestResult::class => BeltTestResultPolicy::class,
        Payment::class => PaymentPolicy::class,
        Certificate::class => CertificatePolicy::class,
        CompetitionForm::class => CompetitionFormPolicy::class,
        AuditLog::class => AuditLogPolicy::class,
        Contact::class => ContactPolicy::class,
        Service::class => ServicePolicy::class,
        TeamMember::class => TeamMemberPolicy::class,
        BlackBelt::class => BlackBeltPolicy::class,
        Testimonial::class => TestimonialPolicy::class,
        AboutSection::class => AboutSectionPolicy::class,
        GalleryCategory::class => GalleryCategoryPolicy::class,
        GalleryImage::class => GalleryImagePolicy::class,
        Event::class => EventPolicy::class,
        Achievement::class => AchievementPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-admin', fn ($user) => $user->isAdmin());
        Gate::define('access-student', fn ($user) => $user->isStudent());
    }
}
