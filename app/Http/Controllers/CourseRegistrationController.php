<?php

namespace App\Http\Controllers;

use App\Models\Course;
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
        $courses = Auth::user()->registeredCourses;
        
        return view('course-registrations.index', compact('courses'));
    }
    
    /**
     * Register the user for a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function register(Course $course)
    {
        // Check if already registered
        $alreadyRegistered = Auth::user()->registeredCourses()
            ->where('courses.id', $course->id)
            ->exists();
        
        if ($alreadyRegistered) {
            return redirect()->route('courses.show', $course)
                ->with('warning', 'Bạn đã đăng ký khóa học này rồi.');
        }
        
        // Register the user for the course
        Auth::user()->registeredCourses()->attach($course->id, [
            'registered_at' => now()
        ]);
        
        return redirect()->route('courses.show', $course)
            ->with('success', 'Đăng ký khóa học thành công.');
    }
    
    /**
     * Unregister the user from a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function unregister(Course $course)
    {
        // Check if registered
        $isRegistered = Auth::user()->registeredCourses()
            ->where('courses.id', $course->id)
            ->exists();
        
        if (!$isRegistered) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Bạn chưa đăng ký khóa học này.');
        }
        
        // Unregister the user from the course
        Auth::user()->registeredCourses()->detach($course->id);
        
        return redirect()->route('courses.index')
            ->with('success', 'Hủy đăng ký khóa học thành công.');
    }
} 