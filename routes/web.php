<?php

use App\Http\Controllers\Admin\AboutSectionController;
use App\Http\Controllers\Admin\AchievementController as AdminAchievementController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BeltController;
use App\Http\Controllers\Admin\BlackBeltController as AdminBlackBeltController;
use App\Http\Controllers\Admin\BeltTestApplicationController;
use App\Http\Controllers\Admin\BeltTestController as AdminBeltTestController;
use App\Http\Controllers\Admin\BeltTestResultController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\CompetitionFormController as AdminCompetitionFormController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\ApplicationController as StudentApplicationController;
use App\Http\Controllers\Student\BeltTestController as StudentBeltTestController;
use App\Http\Controllers\Student\CertificateController as StudentCertificateController;
use App\Http\Controllers\Student\CompetitionFormController as StudentCompetitionFormController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\NotificationController as StudentNotificationController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\AchievementController;
use App\Http\Controllers\Web\BlackBeltController;
use App\Http\Controllers\Web\CertificateVerificationController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\GalleryController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ServiceController;
use App\Support\HomeRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/about/team', [AboutController::class, 'team'])->name('about.team');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
Route::get('/black-belts', [BlackBeltController::class, 'index'])->name('black-belts.index');
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::post('/payments/callback', PaymentCallbackController::class)
    ->middleware('throttle:30,1')
    ->name('payments.callback');

Route::get('/verify/certificate', [CertificateVerificationController::class, 'form'])
    ->name('certificates.verify.form');
Route::post('/verify/certificate', [CertificateVerificationController::class, 'lookup'])
    ->middleware('throttle:20,1')
    ->name('certificates.verify.lookup');
Route::get('/verify/certificate/{certificateNumber}', [CertificateVerificationController::class, 'show'])
    ->name('certificates.verify');

Route::get('/dashboard', function () {
    return redirect()->to(HomeRoute::for(auth()->user()));
})->middleware(['auth', 'active'])->name('dashboard');

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::resource('belts', BeltController::class)->except(['show']);
        Route::post('students/{student}/activate', [StudentController::class, 'activate'])->name('students.activate');
        Route::post('students/{student}/deactivate', [StudentController::class, 'deactivate'])->name('students.deactivate');
        Route::resource('students', StudentController::class);

        Route::resource('services', AdminServiceController::class)->except(['show']);
        Route::resource('testimonials', TestimonialController::class)->except(['show']);
        Route::resource('team-members', TeamMemberController::class)->except(['show']);
        Route::resource('black-belts', AdminBlackBeltController::class)->except(['show']);
        Route::get('about', [AboutSectionController::class, 'index'])->name('about.index');
        Route::get('about/{about}/edit', [AboutSectionController::class, 'edit'])->name('about.edit');
        Route::put('about/{about}', [AboutSectionController::class, 'update'])->name('about.update');

        Route::get('gallery', [AdminGalleryController::class, 'index'])->name('gallery.index');
        Route::post('gallery/categories', [AdminGalleryController::class, 'storeCategory'])->name('gallery.categories.store');
        Route::delete('gallery/categories/{category}', [AdminGalleryController::class, 'destroyCategory'])->name('gallery.categories.destroy');
        Route::post('gallery/images', [AdminGalleryController::class, 'storeImage'])->name('gallery.images.store');
        Route::delete('gallery/images/{image}', [AdminGalleryController::class, 'destroyImage'])->name('gallery.images.destroy');

        Route::resource('events', AdminEventController::class)->except(['show']);
        Route::resource('achievements', AdminAchievementController::class)->except(['show']);

        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
        Route::post('contacts/{contact}/unread', [AdminContactController::class, 'markUnread'])->name('contacts.unread');
        Route::post('contacts/{contact}/archive', [AdminContactController::class, 'archive'])->name('contacts.archive');
        Route::delete('contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

        Route::resource('belt-tests', AdminBeltTestController::class);
        Route::get('belt-test-applications', [BeltTestApplicationController::class, 'index'])->name('belt-test-applications.index');
        Route::get('belt-test-applications/{application}', [BeltTestApplicationController::class, 'show'])->name('belt-test-applications.show');
        Route::post('belt-test-applications/{application}/review', [BeltTestApplicationController::class, 'review'])->name('belt-test-applications.review');

        Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');

        Route::resource('belt-test-results', BeltTestResultController::class)
            ->parameters(['belt-test-results' => 'result'])
            ->except(['edit', 'destroy']);

        Route::get('certificates', [AdminCertificateController::class, 'index'])->name('certificates.index');
        Route::get('certificates/{certificate}', [AdminCertificateController::class, 'show'])->name('certificates.show');
        Route::get('certificates/{certificate}/download', [AdminCertificateController::class, 'download'])
            ->name('certificates.download');
        Route::post('belt-test-results/{result}/certificate', [AdminCertificateController::class, 'issue'])
            ->name('certificates.issue');
        Route::post('certificates/{certificate}/revoke', [AdminCertificateController::class, 'revoke'])
            ->name('certificates.revoke');

        Route::resource('competition-forms', AdminCompetitionFormController::class);
        Route::get('competition-forms/{competition_form}/download', [AdminCompetitionFormController::class, 'download'])
            ->name('competition-forms.download');
        Route::post('competition-forms/{competition_form}/assignments/{assignment}', [AdminCompetitionFormController::class, 'updateAssignment'])
            ->name('competition-forms.assignments.update');

        Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/read-all', [AdminNotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::post('notifications/{notification}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/{audit_log}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });

Route::middleware(['auth', 'active', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', StudentDashboardController::class)->name('dashboard');
        Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile');

        Route::get('/belt-tests', [StudentBeltTestController::class, 'index'])->name('belt-tests.index');
        Route::get('/belt-tests/{beltTest}', [StudentBeltTestController::class, 'show'])->name('belt-tests.show');
        Route::get('/belt-tests/{beltTest}/apply', [StudentBeltTestController::class, 'applyForm'])->name('belt-tests.apply');
        Route::post('/belt-tests/{beltTest}/apply', [StudentBeltTestController::class, 'apply'])->name('belt-tests.apply.submit');

        Route::get('/applications', [StudentApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [StudentApplicationController::class, 'show'])->name('applications.show');

        Route::get('/certificates', [StudentCertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [StudentCertificateController::class, 'show'])->name('certificates.show');
        Route::get('/certificates/{certificate}/download', [StudentCertificateController::class, 'download'])->name('certificates.download');

        Route::get('/competition-forms', [StudentCompetitionFormController::class, 'index'])->name('competition-forms.index');
        Route::get('/competition-forms/{competitionForm}', [StudentCompetitionFormController::class, 'show'])->name('competition-forms.show');
        Route::get('/competition-forms/{competitionForm}/download', [StudentCompetitionFormController::class, 'download'])->name('competition-forms.download');
        Route::post('/competition-forms/{competitionForm}/responded', [StudentCompetitionFormController::class, 'markResponded'])->name('competition-forms.responded');

        Route::get('/payments/{payment}/checkout', [StudentPaymentController::class, 'checkout'])->name('payments.checkout');
        Route::post('/payments/{payment}/checkout', [StudentPaymentController::class, 'complete'])->name('payments.checkout.complete');

        Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [StudentNotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::post('/notifications/{notification}/read', [StudentNotificationController::class, 'markRead'])->name('notifications.read');
    });

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
