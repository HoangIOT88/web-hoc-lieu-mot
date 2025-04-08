<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }
    
    /**
     * Display a listing of the courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Course::with('contentUser')->latest()->paginate(10);
        return view('courses.index', compact('courses'));
    }
    
    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check if user is authorized to create courses
        if (!Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('courses.index')
                ->with('error', 'Bạn không có quyền tạo khóa học mới.');
        }
        
        return view('courses.create');
    }
    
    /**
     * Store a newly created course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if user is authorized to create courses
        if (!Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('courses.index')
                ->with('error', 'Bạn không có quyền tạo khóa học mới.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|string|max:50',
        ]);
        
        $course = Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
            'content_user_id' => Auth::id(),
        ]);
        
        return redirect()->route('courses.show', $course)
            ->with('success', 'Khóa học đã được tạo thành công.');
    }

    /**
     * Show the form for editing a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        // Check if user is authorized to edit this course
        if ((Auth::user()->isContentUser() && $course->content_user_id != Auth::id()) && !Auth::user()->isAdmin()) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Bạn không có quyền chỉnh sửa khóa học này.');
        }
        
        return view('courses.edit', compact('course'));
    }
    
    /**
     * Update the specified course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        // Check if user is authorized to edit this course
        if ((Auth::user()->isContentUser() && $course->content_user_id != Auth::id()) && !Auth::user()->isAdmin()) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Bạn không có quyền chỉnh sửa khóa học này.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|string|max:50',
        ]);
        
        $course->update([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
        ]);
        
        return redirect()->route('courses.show', $course)
            ->with('success', 'Khóa học đã được cập nhật thành công.');
    }
    
    /**
     * Remove the specified course from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        // Check if user is authorized to delete this course
        if ((Auth::user()->isContentUser() && $course->content_user_id != Auth::id()) && !Auth::user()->isAdmin()) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Bạn không có quyền xóa khóa học này.');
        }
        
        // Delete course
        $course->delete();
        
        return redirect()->route('courses.index')
            ->with('success', 'Khóa học đã được xóa thành công.');
    }
    
    /**
     * Display the specified course details.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        $isRegistered = false;
        
        if (Auth::check()) {
            $isRegistered = Auth::user()->registeredCourses()
                ->where('courses.id', $course->id)
                ->exists();
        }
        
        return view('courses.show', compact('course', 'isRegistered'));
    }
    
    /**
     * Register the current user for a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function register(Course $course)
    {
        // Check if already registered
        $isRegistered = Auth::user()->registeredCourses()
            ->where('courses.id', $course->id)
            ->exists();
            
        if ($isRegistered) {
            return redirect()->route('courses.show', $course)->with('info', 'You are already registered for this course.');
        }
        
        // Register user for the course
        Auth::user()->registeredCourses()->attach($course->id, ['registered_at' => now()]);
        
        return redirect()->route('courses.show', $course)->with('success', 'You have successfully registered for this course.');
    }
    
    /**
     * Display the user's registered courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function myCourses()
    {
        $registeredCourses = Auth::user()->registeredCourses()->paginate(10);
        return view('courses.my_courses', compact('registeredCourses'));
    }
}
