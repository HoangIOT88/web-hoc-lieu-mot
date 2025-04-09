<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

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

// Broadcasting auth route - được bảo vệ bởi middleware auth
Route::post('/broadcasting/auth', function () {
    // Thêm debug chi tiết
    \Log::debug('Broadcasting auth request [Detailed]', [
        'user' => auth()->check() ? auth()->id() : 'not authenticated',
        'user_name' => auth()->check() ? auth()->user()->name : 'none',
        'user_role' => auth()->check() ? auth()->user()->role : 'none',
        'channel_name' => request('channel_name'),
        'socket_id' => request('socket_id'),
        'csrf_token' => request()->hasHeader('X-CSRF-TOKEN') ? 'present' : 'missing',
        'request_path' => request()->path(),
        'request_method' => request()->method(),
        'request_headers' => collect(request()->headers->all())->only(['x-csrf-token', 'cookie', 'accept', 'content-type']),
        'request_ip' => request()->ip(),
        'session_has_auth' => session()->has('auth'),
    ]);
    
    if (!auth()->check()) {
        \Log::error('Broadcasting auth failed: User not authenticated');
        return response('Unauthorized', 403);
    }
    
    return Broadcast::auth(request());
})->middleware(['web', 'auth']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

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

// Content User Routes
Route::prefix('content-user')->name('content_user.')->middleware(['auth', 'content.user'])->group(function () {
    // Managed Courses
    Route::get('courses', [App\Http\Controllers\ContentUser\CourseManagementController::class, 'index'])->name('courses.index');
    Route::get('courses/create', [App\Http\Controllers\ContentUser\CourseManagementController::class, 'create'])->name('courses.create');
    Route::post('courses', [App\Http\Controllers\ContentUser\CourseManagementController::class, 'store'])->name('courses.store');
    Route::get('courses/{course}/edit', [App\Http\Controllers\ContentUser\CourseManagementController::class, 'edit'])->name('courses.edit');
    Route::put('courses/{course}', [App\Http\Controllers\ContentUser\CourseManagementController::class, 'update'])->name('courses.update');
    Route::delete('courses/{course}', [App\Http\Controllers\ContentUser\CourseManagementController::class, 'destroy'])->name('courses.destroy');
    
    // User Approvals
    Route::get('approvals', [App\Http\Controllers\ContentUser\UserApprovalController::class, 'index'])->name('approvals.index');
    Route::get('approvals/history', [App\Http\Controllers\ContentUser\UserApprovalController::class, 'history'])->name('approvals.history');
    Route::get('approvals/{approval}', [App\Http\Controllers\ContentUser\UserApprovalController::class, 'show'])->name('approvals.show');
});

// Chat Group Routes
Route::resource('chat-groups', App\Http\Controllers\ChatGroupController::class);
Route::get('chat-groups/{chatGroup}/members/add', [App\Http\Controllers\ChatGroupController::class, 'addMembersForm'])->name('chat-groups.members.form');
Route::post('chat-groups/{chatGroup}/members', [App\Http\Controllers\ChatGroupController::class, 'addMembers'])->name('chat-groups.members.add');
Route::delete('chat-groups/{chatGroup}/members/{userId}', [App\Http\Controllers\ChatGroupController::class, 'removeMember'])->name('chat-groups.members.remove');
Route::post('chat-groups/{chatGroup}/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
Route::get('chat-groups/{chatGroup}/messages', [App\Http\Controllers\MessageController::class, 'getMessages'])->name('messages.get');
Route::delete('messages/{message}', [App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');

// User Approval Routes
Route::get('user-approvals', [App\Http\Controllers\UserApprovalController::class, 'index'])->name('user-approvals.index');
Route::get('user-approvals/{approval}', [App\Http\Controllers\UserApprovalController::class, 'show'])->name('user-approvals.show');
Route::put('user-approvals/{approval}/approve', [App\Http\Controllers\UserApprovalController::class, 'approve'])->name('user-approvals.approve');
Route::put('user-approvals/{approval}/reject', [App\Http\Controllers\UserApprovalController::class, 'reject'])->name('user-approvals.reject');

// Course Registration Routes
Route::get('my-courses', [App\Http\Controllers\CourseRegistrationController::class, 'index'])->name('course-registrations.index');
Route::post('courses/{course}/register', [App\Http\Controllers\CourseRegistrationController::class, 'register'])->name('course-registrations.register');
Route::delete('courses/{course}/unregister', [App\Http\Controllers\CourseRegistrationController::class, 'unregister'])->name('course-registrations.unregister');

// Lecture Routes
Route::resource('courses.lectures', App\Http\Controllers\LectureController::class);
// Simple lectures route without course dependency
Route::get('lectures', [App\Http\Controllers\LectureController::class, 'indexAll'])->name('lectures.index');

// Simple exercises route without course dependency
Route::get('exercises', [App\Http\Controllers\ExerciseController::class, 'indexAll'])->name('exercises.index');
Route::get('exercises/create', [App\Http\Controllers\ExerciseController::class, 'create'])->name('exercises.create');
Route::post('exercises', [App\Http\Controllers\ExerciseController::class, 'store'])->name('exercises.store');
Route::get('exercises/{exercise}/edit', [App\Http\Controllers\ExerciseController::class, 'edit'])->name('exercises.edit');
Route::put('exercises/{exercise}', [App\Http\Controllers\ExerciseController::class, 'update'])->name('exercises.update');
Route::delete('exercises/{exercise}', [App\Http\Controllers\ExerciseController::class, 'destroy'])->name('exercises.destroy');

// Exercise Routes
Route::get('courses/{course}/exercises', [App\Http\Controllers\ExerciseController::class, 'index'])->name('courses.exercises');
Route::get('exercises/{exercise}', [App\Http\Controllers\ExerciseController::class, 'show'])->name('exercises.show');
Route::post('exercises/{exercise}/submit', [App\Http\Controllers\ExerciseController::class, 'submit'])->name('exercises.submit');

// Exercise Submission Routes
Route::get('exercise-submissions', [App\Http\Controllers\ExerciseSubmissionController::class, 'index'])->name('exercise-submissions.index');
Route::get('exercise-submissions/all', [App\Http\Controllers\ExerciseSubmissionController::class, 'all'])->name('exercise-submissions.all');
Route::get('exercise-submissions/{submission}', [App\Http\Controllers\ExerciseSubmissionController::class, 'show'])->name('exercise-submissions.show');
Route::get('exercise-submissions/{submission}/grade', [App\Http\Controllers\ExerciseSubmissionController::class, 'showGradeForm'])->name('exercise-submissions.grade-form');
Route::post('exercise-submissions/{submission}/grade', [App\Http\Controllers\ExerciseSubmissionController::class, 'grade'])->name('exercise-submissions.grade');

// Chat Messages Routes
Route::middleware('auth')->group(function () {
    Route::post('/chat-groups/{chatGroup}/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::post('/chat-groups/{chatGroup}/typing', [App\Http\Controllers\MessageController::class, 'typing'])->name('chat.typing');
    Route::post('/chat-groups/{chatGroup}/stop-typing', [App\Http\Controllers\MessageController::class, 'stopTyping'])->name('chat.stop-typing');
});

// Thêm route test đơn giản hóa để debug Pusher broadcast authentication
Route::get('/test-broadcast-auth', function() {
    return view('test-broadcast-auth');
});

// Route test cho Pusher authentication
Route::post('/broadcasting/auth', function () {
    \Log::debug('BROADCAST AUTH REQUEST', [
        'user' => auth()->check() ? auth()->id() : 'guest',
        'channel_name' => request()->input('channel_name'),
        'socket_id' => request()->input('socket_id'),
        'has_csrf' => request()->hasHeader('X-CSRF-TOKEN'),
        'all_headers' => collect(request()->headers->all())->only(['x-csrf-token', 'accept', 'content-type'])
    ]);
    
    if (auth()->guest()) {
        \Log::debug('Auth failed: Not authenticated');
        return response('Unauthorized', 401);
    }
    
    // Cấp quyền cho tất cả các request để test
    $channelName = request()->input('channel_name');
    
    if (!$channelName) {
        return response('Missing channel name', 400);
    }
    
    // Sử dụng raw response để cung cấp xác thực trực tiếp từ các tham số
    $socketId = request()->input('socket_id');
    $key = config('broadcasting.connections.pusher.key');
    $secret = config('broadcasting.connections.pusher.secret');
    
    // Tạo chuỗi signature 
    $signature = hash_hmac('sha256', $socketId . ':' . $channelName, $secret);
    
    // Cung cấp response xác thực
    return response()->json([
        'auth' => $key . ':' . $signature
    ]);
});
