<?php

use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\Auth\TeacherLoginController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\TeacherController;
use App\Http\Controllers\Employee\StudentController;
use App\Http\Controllers\Employee\SubjectController;
use App\Http\Controllers\Employee\AcademicYearController;
use App\Http\Controllers\Employee\ClassController;
use App\Http\Controllers\Employee\StudentClassController;
use App\Http\Controllers\Employee\SettingController;
use App\Http\Controllers\Employee\ArticleController;
use App\Http\Controllers\Employee\EventController;
use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\GradeController;
use App\Http\Controllers\Employee\ScheduleController;
use App\Http\Controllers\Employee\PaymentController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**
 * ============================================================================
 * AUTHENTICATION ROUTES
 * ============================================================================
 * 
 * Three separate login systems for different user types:
 * - Employee (Admin/Staff)
 * - Teacher
 * - Student
 */

// Employee Authentication Routes
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('/login', [EmployeeLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [EmployeeLoginController::class, 'authenticate'])->name('authenticate');
    Route::get('/logout', [EmployeeLoginController::class, 'logout'])->name('logout');

    // Employee Dashboard & CRUD
    Route::middleware('web')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');

        // Employee Management
        Route::resource('employees', EmployeeController::class);
        Route::get('/articles/{articleId}/tags', [ArticleController::class, 'indexTag'])->name('articles.tag');
        Route::get('/articles/{articleId}/tags/create', [ArticleController::class, 'createTag'])->name('articles.create-tag');
        Route::post('/articles/{articleId}/tags', [ArticleController::class, 'storeTag'])->name('articles.store-tag');
        Route::delete('/articles/{articleId}/tags/{tagId}', [ArticleController::class, 'destroyTag'])->name('articles.destroy-tag');

        // Teacher Management
        Route::resource('teachers', TeacherController::class);

        // Student Management
        Route::resource('students', StudentController::class);

        // Subject Management
Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
// CREATE
Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
// STORE
Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
// EDIT
Route::get('/subjects/{id}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
// UPDATE
Route::put('/subjects/{id}', [SubjectController::class, 'update'])->name('subjects.update');
// DELETE
Route::delete('/subjects/{id}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

       
        // Academic Year Management
        Route::resource('academic-years', AcademicYearController::class);
        Route::get('/academic', [AcademicYearController::class, 'indexacademic'])->name('academic.index-academic');
        Route::get('/academic/create-academic', [AcademicYearController::class, 'create'])
        ->name('academic.create');
        Route::post('/academic', [AcademicYearController::class, 'store'])->name('academic.store-academic');
        Route::get('/academic/{id}/edit-academic', [AcademicYearController::class, 'edit'])->name('academic.edit');
        Route::put('/academic/{id}', [AcademicYearController::class, 'update'])->name('academic.update-academic');
        Route::delete('/academic/{id}', [AcademicYearController::class, 'destroy'])->name('academic.destroy-academic');
        // Class Management


        Route::resource('classes', ClassController::class);
        Route::resource('student-classes', StudentClassController::class);

        // Student Class Management
        Route::prefix('employee')->name('employee.')->group(function () {
            Route::get('/student-classes', [StudentClassController::class, 'index'])
                ->name('student_classes.index');

            Route::get('/student-classes/create', [StudentClassController::class, 'create'])
                ->name('student_classes.create');

            Route::post('/student-classes', [StudentClassController::class, 'store'])
                ->name('student_classes.store');

           Route::get('/student-classes/{id}/edit', [StudentClassController::class, 'edit'])
    ->name('student-classes.edit');
Route::put('/student-classes/{id}', [StudentClassController::class, 'update'])
    ->name('employee.student-classes.update');

        });

        // Settings Management (academic + Detail)



        // Settings Management (Header + Detail)
        Route::get('/settings', [SettingController::class, 'indexHeader'])->name('settings.index-header');
        Route::get('/settings/create-header', [SettingController::class, 'createHeader'])->name('settings.create-header');
        Route::post('/settings', [SettingController::class, 'storeHeader'])->name('settings.store-header');
        Route::get('/settings/{id}/edit-header', [SettingController::class, 'editHeader'])->name('settings.edit-header');
        Route::put('/settings/{id}', [SettingController::class, 'updateHeader'])->name('settings.update-header');
        Route::delete('/settings/{id}', [SettingController::class, 'destroyHeader'])->name('settings.destroy-header');

        // Detail Settings Management
        Route::prefix('employee')->group(function () {
            Route::get('/settings/{headerId}/detail', [SettingController::class, 'indexDetail'])
                ->name('settings.detail');
        });

        Route::get('/settings/{headerId}/detail/create', [SettingController::class, 'createDetail'])->name('settings.create-detail');
        Route::post('/settings/{headerId}/detail', [SettingController::class, 'storeDetail'])->name('settings.store-detail');
        Route::get('/settings/{headerId}/detail/{detailId}/edit', [SettingController::class, 'editDetail'])->name('settings.edit-detail');
        Route::put('/settings/{headerId}/detail/{detailId}', [SettingController::class, 'updateDetail'])->name('settings.update-detail');
        Route::delete('/settings/{headerId}/detail/{detailId}', [SettingController::class, 'destroyDetail'])->name('settings.destroy-detail');

        // Article Management
        Route::resource('articles', ArticleController::class);

        // Article Tag Management (Dedicated Routes)
        Route::get('/tag-articles', [ArticleController::class, 'indexTag'])->name('tag-articles.index');
        Route::get('/tag-articles/create', [ArticleController::class, 'createTag'])->name('tag-articles.create');
        Route::post('/tag-articles', [ArticleController::class, 'storeTag'])->name('tag-articles.store');
        Route::get('/tag-articles/{tagId}/edit', [ArticleController::class, 'editTag'])->name('tag-articles.edit');
        Route::put('/tag-articles/{tagId}', [ArticleController::class, 'updateTag'])->name('tag-articles.update');
        Route::delete('/tag-articles/{tagId}', [ArticleController::class, 'destroyTag'])->name('tag-articles.destroy');

        // Article Tag Management (Nested Routes)
        Route::get('/articles/{articleId}/tags', [ArticleController::class, 'indexTag'])->name('articles.tag');
        Route::get('/articles/{articleId}/tags/create', [ArticleController::class, 'createTag'])->name('articles.create-tag');
        Route::post('/articles/{articleId}/tags', [ArticleController::class, 'storeTag'])->name('articles.store-tag');
        Route::delete('/articles/{articleId}/tags/{tagId}', [ArticleController::class, 'destroyTag'])->name('articles.destroy-tag');

        // Event Management
        Route::resource('events', EventController::class);
// Event Tag Management per event
Route::get('/tag-events/{eventId}', [EventController::class, 'createTag'])->name('employee.events.tag');
Route::post('/tag-events/{eventId}', [EventController::class, 'storeTag'])->name('employee.events.tag.store');
Route::delete('/tag-events/{eventId}/{tagId}', [EventController::class, 'destroyTag'])->name('employee.events.tag.destroy');

        // Event Tag Management (Dedicated Routes)
        Route::get('/tag-events', [EventController::class, 'indexTag'])->name('tag-events.index');
        Route::get('/tag-events/create', [EventController::class, 'createTag'])->name('tag-events.create');
        Route::post('/tag-events', [EventController::class, 'storeTag'])->name('tag-events.store');
        Route::get('/tag-events/{tagId}/edit', [EventController::class, 'editTag'])->name('tag-events.edit');
        Route::put('/tag-events/{tagId}', [EventController::class, 'updateTag'])->name('tag-events.update');
        Route::delete('/tag-events/{tagId}', [EventController::class, 'destroyTag'])->name('tag-events.destroy');

        // Attendance Management (Bulk Input by Class)
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/class/{classId}/date/{date}', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
        Route::get('/api/attendance/students/{classId}', [AttendanceController::class, 'getStudentsByClass'])->name('api.attendance.students');

        // Grade Management (Bulk Input by Class)
        Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
        Route::get('/grades/create', [GradeController::class, 'create'])->name('grades.create');
        Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');
        Route::get('/grades/{id}/edit', [GradeController::class, 'edit'])->name('grades.edit');
        Route::put('/grades/{id}', [GradeController::class, 'update'])->name('grades.update');
        Route::delete('/grades/{id}', [GradeController::class, 'destroy'])->name('grades.destroy');
        Route::get('/api/grades/students/{classId}', [GradeController::class, 'getStudentsByClass'])->name('api.grades.students');

        // Schedule Management
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');


        // Payment Management (with Installments)
        Route::resource('payments', PaymentController::class);
        Route::get('/payments/{paymentId}/installments/create', [PaymentController::class, 'createInstallment'])->name('payments.create-installment');
        Route::post('/payments/{paymentId}/installments', [PaymentController::class, 'storeInstallment'])->name('payments.store-installment');
        Route::delete('/payments/{paymentId}/installments/{installmentId}', [PaymentController::class, 'destroyInstallment'])->name('payments.destroy-installment');
    });
});

// Teacher Authentication Routes
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/login', [TeacherLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [TeacherLoginController::class, 'authenticate'])->name('authenticate');
    Route::get('/logout', [TeacherLoginController::class, 'logout'])->name('logout');

    // Teacher Dashboard
    Route::middleware('web')->group(function () {
        Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');
    });
});

// Student Authentication Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/login', [StudentLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StudentLoginController::class, 'authenticate'])->name('authenticate');
    Route::get('/logout', [StudentLoginController::class, 'logout'])->name('logout');

    // Student Dashboard
    Route::middleware('web')->group(function () {
        Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
        Route::get('/profile', [StudentDashboard::class, 'profile'])->name('profile');
        Route::post('/profile/update', [StudentDashboard::class, 'updateProfile'])->name('profile.update');
    });
});
