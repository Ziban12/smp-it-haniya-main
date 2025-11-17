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
        
        // Teacher Management
        Route::resource('teachers', TeacherController::class);
        
        // Student Management
        Route::resource('students', StudentController::class);
        
        // Subject Management
        Route::resource('subjects', SubjectController::class);
        
        // Academic Year Management
        Route::resource('academic-years', AcademicYearController::class);
        
        // Class Management
        Route::resource('classes', ClassController::class);
        
        // Student Class Management
        Route::resource('student-classes', StudentClassController::class);
        
        // Settings Management (Header + Detail)
        Route::get('/settings', [SettingController::class, 'indexHeader'])->name('settings.index-header');
        Route::get('/settings/create-header', [SettingController::class, 'createHeader'])->name('settings.create-header');
        Route::post('/settings', [SettingController::class, 'storeHeader'])->name('settings.store-header');
        Route::get('/settings/{id}/edit-header', [SettingController::class, 'editHeader'])->name('settings.edit-header');
        Route::put('/settings/{id}', [SettingController::class, 'updateHeader'])->name('settings.update-header');
        Route::delete('/settings/{id}', [SettingController::class, 'destroyHeader'])->name('settings.destroy-header');
        
        // Detail Settings Management
        Route::get('/settings/{headerId}/detail', [SettingController::class, 'indexDetail'])->name('settings.detail');
        Route::get('/settings/{headerId}/detail/create', [SettingController::class, 'createDetail'])->name('settings.create-detail');
        Route::post('/settings/{headerId}/detail', [SettingController::class, 'storeDetail'])->name('settings.store-detail');
        Route::get('/settings/{headerId}/detail/{detailId}/edit', [SettingController::class, 'editDetail'])->name('settings.edit-detail');
        Route::put('/settings/{headerId}/detail/{detailId}', [SettingController::class, 'updateDetail'])->name('settings.update-detail');
        Route::delete('/settings/{headerId}/detail/{detailId}', [SettingController::class, 'destroyDetail'])->name('settings.destroy-detail');
        
        // Article Management
        Route::resource('articles', ArticleController::class);
        
        // Article Tag Management
        Route::get('/articles/{articleId}/tags', [ArticleController::class, 'indexTag'])->name('articles.tag');
        Route::get('/articles/{articleId}/tags/create', [ArticleController::class, 'createTag'])->name('articles.create-tag');
        Route::post('/articles/{articleId}/tags', [ArticleController::class, 'storeTag'])->name('articles.store-tag');
        Route::delete('/articles/{articleId}/tags/{tagId}', [ArticleController::class, 'destroyTag'])->name('articles.destroy-tag');
        
        // Event Management
        Route::resource('events', EventController::class);
        
        // Event Tag Management
        Route::get('/events/{eventId}/tags', [EventController::class, 'indexTag'])->name('events.tag');
        Route::get('/events/{eventId}/tags/create', [EventController::class, 'createTag'])->name('events.create-tag');
        Route::post('/events/{eventId}/tags', [EventController::class, 'storeTag'])->name('events.store-tag');
        Route::delete('/events/{eventId}/tags/{tagId}', [EventController::class, 'destroyTag'])->name('events.destroy-tag');
        
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
        Route::resource('schedules', ScheduleController::class);
        
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
