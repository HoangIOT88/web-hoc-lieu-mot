<?php

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

Route::get('/', function () {
    return redirect()->route('courses.index');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Courses Routes
Route::resource('courses', App\Http\Controllers\CourseController::class);

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // User Management
    Route::resource('users', App\Http\Controllers\Admin\UserManagementController::class);
    
    // Reports
    Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{report}', [App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
    Route::get('reports/{report}/download', [App\Http\Controllers\Admin\ReportController::class, 'download'])->name('reports.download');
    Route::post('reports/user-stats', [App\Http\Controllers\Admin\ReportController::class, 'generateUserStats'])->name('reports.user-stats');
    Route::post('reports/course-stats', [App\Http\Controllers\Admin\ReportController::class, 'generateCourseStats'])->name('reports.course-stats');
    Route::post('reports/activity-stats', [App\Http\Controllers\Admin\ReportController::class, 'generateActivityStats'])->name('reports.activity-stats');
});

// Chat Group Routes
Route::resource('chat-groups', App\Http\Controllers\ChatGroupController::class);
Route::post('chat-groups/{chatGroup}/members', [App\Http\Controllers\ChatGroupController::class, 'addMembers'])->name('chat-groups.members.add');
Route::delete('chat-groups/{chatGroup}/members/{userId}', [App\Http\Controllers\ChatGroupController::class, 'removeMember'])->name('chat-groups.members.remove');
Route::post('chat-groups/{chatGroup}/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
Route::get('chat-groups/{chatGroup}/messages', [App\Http\Controllers\MessageController::class, 'getMessages'])->name('messages.get');
Route::delete('messages/{message}', [App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');

// User Approval Routes
Route::get('user-approvals', [App\Http\Controllers\UserApprovalController::class, 'index'])->name('user-approvals.index');
Route::put('user-approvals/{user}/approve', [App\Http\Controllers\UserApprovalController::class, 'approve'])->name('user-approvals.approve');
Route::put('user-approvals/{user}/reject', [App\Http\Controllers\UserApprovalController::class, 'reject'])->name('user-approvals.reject');

// Course Registration Routes
Route::get('my-courses', [App\Http\Controllers\CourseRegistrationController::class, 'index'])->name('course-registrations.index');
Route::post('courses/{course}/register', [App\Http\Controllers\CourseRegistrationController::class, 'register'])->name('course-registrations.register');
Route::delete('courses/{course}/unregister', [App\Http\Controllers\CourseRegistrationController::class, 'unregister'])->name('course-registrations.unregister');

// Lecture Routes
Route::resource('courses.lectures', App\Http\Controllers\LectureController::class);

// Exercise Routes
Route::get('courses/{course}/exercises', [App\Http\Controllers\ExerciseController::class, 'index'])->name('courses.exercises');
Route::get('exercises/{exercise}', [App\Http\Controllers\ExerciseController::class, 'show'])->name('exercises.show');
Route::post('exercises/{exercise}/submit', [App\Http\Controllers\ExerciseController::class, 'submit'])->name('exercises.submit');
