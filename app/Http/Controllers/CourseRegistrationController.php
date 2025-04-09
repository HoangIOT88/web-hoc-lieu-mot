<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UserApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseRegistrationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the user's registered courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Lấy danh sách khóa học đã đăng ký (đã được duyệt)
        $courses = Auth::user()->registeredCourses;
        
        // Lấy danh sách khóa học đang chờ duyệt
        $pendingCourses = Course::whereHas('userApprovals', function($query) {
            $query->where('user_id', Auth::id())
                  ->where('status', UserApproval::STATUS_PENDING);
        })->get();
        
        return view('course-registrations.index', compact('courses', 'pendingCourses'));
    }
    
    /**
     * Register the user for a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function register(Course $course)
    {
        // Kiểm tra nếu đã đăng ký
        $alreadyRegistered = Auth::user()->registeredCourses()
            ->where('courses.id', $course->id)
            ->exists();
        
        if ($alreadyRegistered) {
            return redirect()->route('courses.show', $course)
                ->with('warning', 'Bạn đã đăng ký khóa học này rồi.');
        }
        
        // Kiểm tra nếu đã có yêu cầu chờ duyệt
        $pendingApproval = UserApproval::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', UserApproval::STATUS_PENDING)
            ->exists();
            
        if ($pendingApproval) {
            return redirect()->route('courses.show', $course)
                ->with('info', 'Yêu cầu đăng ký khóa học của bạn đang chờ được duyệt.');
        }
        
        // Tạo yêu cầu đăng ký chờ duyệt
        UserApproval::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'status' => UserApproval::STATUS_PENDING,
        ]);
        
        return redirect()->route('courses.show', $course)
            ->with('success', 'Yêu cầu đăng ký khóa học đã được gửi và đang chờ duyệt.');
    }
    
    /**
     * Unregister the user from a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function unregister(Course $course)
    {
        // Kiểm tra nếu đã đăng ký
        $isRegistered = Auth::user()->registeredCourses()
            ->where('courses.id', $course->id)
            ->exists();
        
        // Kiểm tra nếu đang có yêu cầu đăng ký
        $pendingApproval = UserApproval::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', UserApproval::STATUS_PENDING)
            ->first();
        
        if ($isRegistered) {
            // Hủy đăng ký khóa học
            Auth::user()->registeredCourses()->detach($course->id);
            return redirect()->route('courses.index')
                ->with('success', 'Hủy đăng ký khóa học thành công.');
        } else if ($pendingApproval) {
            // Hủy yêu cầu đăng ký đang chờ
            $pendingApproval->delete();
            return redirect()->route('courses.index')
                ->with('success', 'Đã hủy yêu cầu đăng ký khóa học.');
        } else {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Bạn chưa đăng ký khóa học này.');
        }
    }
} 