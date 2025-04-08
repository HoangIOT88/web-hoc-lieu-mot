<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\UserApproval;
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
        $this->middleware('regular.user');
    }
    
    /**
     * Display a listing of available courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function availableCourses()
    {
        $registeredCourseIds = Auth::user()->registeredCourses()->pluck('courses.id');
        $availableCourses = Course::whereNotIn('id', $registeredCourseIds)->paginate(10);
        
        return view('user.courses.available', compact('availableCourses'));
    }
    
    /**
     * Display a listing of user's registered courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function registeredCourses()
    {
        $registeredCourses = Auth::user()->registeredCourses()->paginate(10);
        
        return view('user.courses.registered', compact('registeredCourses'));
    }
    
    /**
     * Register user for a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function register(Course $course)
    {
        $isRegistered = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->exists();
            
        if ($isRegistered) {
            return redirect()->route('user.courses.available')
                ->with('info', 'You are already registered for this course.');
        }
        
        // Check if user has been approved by the course's content user
        $isApproved = UserApproval::where('user_id', Auth::id())
            ->where('content_user_id', $course->content_user_id)
            ->where('status', 'APPROVED')
            ->exists();
            
        // If not already approved, create an approval request
        if (!$isApproved) {
            // Check if there's already a pending request
            $pendingApproval = UserApproval::where('user_id', Auth::id())
                ->where('status', 'PENDING')
                ->exists();
                
            if (!$pendingApproval) {
                UserApproval::create([
                    'user_id' => Auth::id(),
                    'status' => 'PENDING',
                ]);
                
                return redirect()->route('user.courses.available')
                    ->with('info', 'Your registration request has been submitted and is pending approval.');
            } else {
                return redirect()->route('user.courses.available')
                    ->with('info', 'You already have a pending approval request. Please wait for approval.');
            }
        }
        
        // If approved, register for the course
        CourseRegistration::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'registered_at' => now(),
        ]);
        
        return redirect()->route('user.courses.registered')
            ->with('success', 'You have successfully registered for the course.');
    }
    
    /**
     * Unregister from a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function unregister(Course $course)
    {
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();
            
        if (!$registration) {
            return redirect()->route('user.courses.registered')
                ->with('error', 'You are not registered for this course.');
        }
        
        $registration->delete();
        
        return redirect()->route('user.courses.registered')
            ->with('success', 'You have successfully unregistered from the course.');
    }
    
    /**
     * Show course details.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        $isRegistered = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->exists();
            
        return view('user.courses.show', compact('course', 'isRegistered'));
    }
} 