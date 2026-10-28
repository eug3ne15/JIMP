<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HR\CourseController as HRCourseController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\InternController as HRInternController;
use App\Http\Controllers\HR\SupervisorController as HRSupervisorController;
use App\Http\Controllers\HR\UniversityController as HRUniversityController;
use App\Http\Controllers\Intern\CourseApplicationController;
use App\Http\Controllers\Intern\DashboardController as InternDashboardController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\EvaluationController as SupervisorEvaluationController;
use App\Models\Course;
use App\Http\Controllers\Auth\OtpController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $departments = \App\Models\Department::with(['roles' => fn ($q) => $q->where('status', 'open')->orderBy('name')])->orderBy('name')->get();
    $courses = Course::query()->with(['university', 'department'])->open()->orderBy('name')->get();

    return view('welcome', compact('courses', 'departments'));
});

Route::get('/register', function () {
    return view('register');
});
Route::post('/register', [AuthController::class, 'register'])
    ->name('register');

Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login');

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::resource('users', AdminUserController::class)->except(['show', 'edit']);
    });

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');

Route::middleware(['auth', 'intern'])->group(function () {
    Route::get('/intern/dashboard', InternDashboardController::class)->name('dashboard.intern');
    Route::get('/intern/departments/{department}/apply', [CourseApplicationController::class, 'applyDepartment'])->name('intern.departments.apply');
    Route::get('/intern/courses/{course}/apply', [CourseApplicationController::class, 'create'])->name('intern.courses.apply');
    Route::post('/intern/courses/{course}/apply', [CourseApplicationController::class, 'store'])->name('intern.courses.apply.store');
    Route::post('/intern/institution', [InternDashboardController::class, 'updateInstitution'])
        ->name('intern.institution.update');
    Route::get('/intern/applications/{application}/recommendation', [InternDashboardController::class, 'downloadRecommendation'])
        ->name('intern.recommendation.download');
    Route::get('/intern/applications/{application}/document', [InternDashboardController::class, 'downloadDocument'])
        ->name('intern.applications.document');
});

Route::middleware(['auth', 'supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', SupervisorDashboardController::class)->name('dashboard');
    Route::post('/applications/{application}/evaluation', [SupervisorEvaluationController::class, 'store'])
        ->name('applications.evaluation.store');
    Route::get('/applications/{application}/documents/{type}', [SupervisorEvaluationController::class, 'downloadDocument'])
        ->name('applications.document');
    Route::get('/applications/{application}/evaluation/download', [SupervisorEvaluationController::class, 'downloadEvaluation'])
        ->name('applications.evaluation.download');
});

Route::middleware(['auth', 'hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', HRDashboardController::class)->name('dashboard');
    Route::post('courses/{course}/fields-of-study', [HRCourseController::class, 'addFieldOfStudy'])->name('courses.fields.add');
    Route::delete('courses/{course}/fields-of-study', [HRCourseController::class, 'removeFieldOfStudy'])->name('courses.fields.remove');
    Route::resource('courses', HRCourseController::class);
    Route::resource('supervisors', HRSupervisorController::class)->except('show');
    Route::patch('universities/{university}/approve', [HRUniversityController::class, 'approve'])->name('universities.approve');
    Route::resource('universities', HRUniversityController::class)->except('show');
    Route::get('interns/{intern}/download', [HRInternController::class, 'downloadDocument'])->name('intern.download');
    Route::get('interns/{intern}/evaluation', [HRInternController::class, 'downloadEvaluation'])->name('intern.evaluation.download');
    Route::post('interns/{intern}/recommendation', [HRInternController::class, 'issueRecommendation'])->name('intern.recommendation.issue');
    Route::resource('interns', HRInternController::class)->names('intern'); // to produce hr.intern.index etc.
});

Route::post('/logout', function () {
    auth()->logout();

    return redirect()->route('login');
})->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('/login/otp', fn () => redirect()->route('login'))->name('otp.request.form');

    Route::get('/login/otp/verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify.form');
    Route::post('/login/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/login/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
});