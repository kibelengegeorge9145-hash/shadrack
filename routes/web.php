<?php

use App\Http\Controllers\AcademicReportController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Accountant\PaymentController;
use App\Http\Controllers\Accountant\ReceiptController;
use App\Http\Controllers\Accountant\ReportController;
use App\Http\Controllers\Admin\AccountantController;
use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventRsvpController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GradingController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReportQueueController;
use App\Http\Controllers\Admin\ResultController as AdminResultController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TimetableController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Parent\ChildController;
use App\Http\Controllers\PublicInteractionController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ResultController;
use App\Http\Controllers\Teacher\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/academics', [HomeController::class, 'academics'])->name('academics');
Route::get('/o-level', [HomeController::class, 'olevel'])->name('olevel');
Route::get('/a-level', [HomeController::class, 'alevel'])->name('alevel');
Route::get('/admissions', [HomeController::class, 'admissions'])->name('admissions');
Route::post('/admissions', [PublicInteractionController::class, 'admission'])->name('admissions.submit')->middleware('throttle:6,1');
Route::get('/news', [HomeController::class, 'news'])->name('news.index');
Route::get('/news/{news:slug}', [HomeController::class, 'newsShow'])->name('news.show');
Route::get('/events', [HomeController::class, 'events'])->name('events.index');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery.index');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/announcements', [HomeController::class, 'announcements'])->name('announcements');
Route::get('/admissions/status', [PublicInteractionController::class, 'admissionStatusForm'])->name('admissions.status-form');

Route::post('/contact', [PublicInteractionController::class, 'contact'])->name('contact.submit')->middleware('throttle:6,1');
Route::post('/subscribe', [PublicInteractionController::class, 'subscribe'])->name('subscribe')->middleware('throttle:4,1');
Route::post('/unsubscribe', [PublicInteractionController::class, 'unsubscribe'])->name('unsubscribe')->middleware('throttle:4,1');
Route::post('/admissions/status', [PublicInteractionController::class, 'admissionStatus'])->name('admissions.status')->middleware('throttle:10,1');
Route::post('/events/{event}/rsvp', [PublicInteractionController::class, 'rsvp'])->name('events.rsvp')->middleware('throttle:4,1');
Route::post('/news/{news:slug}/comments', [PublicInteractionController::class, 'comment'])->name('news.comments.store')->middleware('throttle:10,1');

/*
|--------------------------------------------------------------------------
| Guest Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated (any role)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/profile', [AccountController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/password/change', [AccountController::class, 'showChangePasswordForm'])->name('password.change');
        Route::put('/password/change', [AccountController::class, 'changePassword'])->name('password.change');
    });
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');

    Route::resource('students', StudentController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('parents', ParentController::class)->parameters(['parents' => 'parent']);
    Route::resource('accountants', AccountantController::class);
    Route::resource('classes', ClassController::class);
    Route::resource('subjects', SubjectController::class);

    Route::get('/grading', [GradingController::class, 'index'])->name('academics.grading');
    Route::post('/grading', [GradingController::class, 'store'])->name('grading.store');
    Route::get('/grading/divisions', [GradingController::class, 'divisions'])->name('grading.divisions');

    Route::get('/results', [AdminResultController::class, 'index'])->name('results.index');
    Route::get('/results/report-card/{student}/{term}', [AdminResultController::class, 'reportCard'])->name('results.report-card');

    Route::get('/reports/academic', [AcademicReportController::class, 'index'])->name('reports.academic');
    Route::get('/reports/academic/terms', [AcademicReportController::class, 'terms'])->name('reports.academic.terms');
    Route::get('/reports/academic/preview', [AcademicReportController::class, 'preview'])->name('reports.academic.preview');
    Route::get('/reports/academic/pdf', [AcademicReportController::class, 'pdf'])->name('reports.academic.pdf');
    Route::get('/reports/academic/excel', [AcademicReportController::class, 'exportExcel'])->name('reports.academic.excel');
    Route::get('/reports/academic/export', [AcademicReportController::class, 'exportCsv'])->name('reports.academic.export');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
    Route::post('/timetable', [TimetableController::class, 'store'])->name('timetable.store');

    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::post('/fees', [FeeController::class, 'store'])->name('fees.store');

    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

    Route::resource('admissions', AdmissionController::class)->except(['edit', 'update']);
    Route::patch('/admissions/{admission}/status', [AdmissionController::class, 'updateStatus'])
    ->name('admissions.update-status');
    Route::resource('announcements', AnnouncementController::class)->except(['show']);
    Route::resource('news', NewsController::class)->except(['show']);
    Route::resource('events', EventController::class)->except(['show']);
    Route::resource('gallery', GalleryController::class)->only(['index', 'store', 'destroy']);

    Route::resource('inquiries', InquiryController::class)->only(['index', 'show', 'destroy']);
    Route::resource('subscribers', SubscriberController::class)->only(['index', 'destroy']);
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('/events/{event}/rsvps', [EventRsvpController::class, 'index'])->name('events.rsvps');
    Route::delete('/events/{event}/rsvps/{rsvp}', [EventRsvpController::class, 'destroy'])->name('events.rsvps.destroy');

    Route::post('/reports/queue', [ReportQueueController::class, 'store'])->name('reports.queue');

    Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Student
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/reports', [AcademicReportController::class, 'myReports'])->name('reports.index');
    Route::get('/reports/{student}/{term}/{year}', [AcademicReportController::class, 'showStudentReport'])->name('reports.show');
    Route::get('/reports/{student}/{term}/{year}/pdf', [AcademicReportController::class, 'pdfStudentReport'])->name('reports.pdf');
    Route::get('/attendance', [App\Http\Controllers\Student\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/timetable', [App\Http\Controllers\Student\TimetableController::class, 'index'])->name('timetable.index');
    Route::get('/fees', [App\Http\Controllers\Student\FeeController::class, 'index'])->name('fees.index');
});

/*
|--------------------------------------------------------------------------
| Teacher
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/classes', [App\Http\Controllers\Teacher\ClassController::class, 'index'])->name('classes.index');
    Route::get('/results', [App\Http\Controllers\Teacher\ResultController::class, 'index'])->name('results.index');
    Route::post('/results', [App\Http\Controllers\Teacher\ResultController::class, 'store'])->name('results.store');
    Route::get('/attendance', [App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/reports/academic', [AcademicReportController::class, 'index'])->name('reports.academic');
    Route::get('/reports/academic/terms', [AcademicReportController::class, 'terms'])->name('reports.academic.terms');
    Route::get('/reports/academic/preview', [AcademicReportController::class, 'preview'])->name('reports.academic.preview');
    Route::get('/reports/academic/pdf', [AcademicReportController::class, 'pdf'])->name('reports.academic.pdf');
    Route::get('/reports/academic/excel', [AcademicReportController::class, 'exportExcel'])->name('reports.academic.excel');
    Route::get('/reports/academic/export', [AcademicReportController::class, 'exportCsv'])->name('reports.academic.export');
});

/*
|--------------------------------------------------------------------------
| Accountant
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:accountant'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Accountant\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/fees', [App\Http\Controllers\Accountant\FeeController::class, 'index'])->name('fees.index');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/{payment}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

/*
|--------------------------------------------------------------------------
| Parent
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Parent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/children', [ChildController::class, 'index'])->name('children.index');
    Route::get('/results', [App\Http\Controllers\Parent\ResultController::class, 'index'])->name('results.index');
    Route::get('/reports', [AcademicReportController::class, 'childReports'])->name('reports.index');
    Route::get('/reports/{student}/{term}/{year}', [AcademicReportController::class, 'showChildReport'])->name('reports.show');
    Route::get('/reports/{student}/{term}/{year}/pdf', [AcademicReportController::class, 'pdfChildReport'])->name('reports.pdf');
    Route::get('/fees', [App\Http\Controllers\Parent\FeeController::class, 'index'])->name('fees.index');
});
