<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lecture;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LectureController extends Controller
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
     * Display a listing of the lectures for a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function index(Course $course)
    {
        // Check if user has permission to view lectures
        $isContentUser = $course->content_user_id === Auth::id();
        $isRegistered = Auth::user()->registeredCourses()->where('courses.id', $course->id)->exists();
        $isAdmin = Auth::user()->isAdmin();
        
        if (!$isContentUser && !$isRegistered && !$isAdmin) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Bạn không có quyền xem bài giảng của khóa học này.');
        }
        
        $lectures = $course->lectures;
        
        return view('lectures.index', compact('course', 'lectures'));
    }
    
    /**
     * Show the form for creating a new lecture.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function create(Course $course)
    {
        // Check if user has permission to create lectures
        $isContentUser = $course->content_user_id === Auth::id();
        $isAdmin = Auth::user()->isAdmin();
        
        if (!$isContentUser && !$isAdmin) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Bạn không có quyền tạo bài giảng cho khóa học này.');
        }
        
        return view('lectures.create', compact('course'));
    }
    
    /**
     * Store a newly created lecture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Course $course)
    {
        // Check if user has permission to create lectures
        $isContentUser = $course->content_user_id === Auth::id();
        $isAdmin = Auth::user()->isAdmin();
        
        if (!$isContentUser && !$isAdmin) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Bạn không có quyền tạo bài giảng cho khóa học này.');
        }
        
        // Validate request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,mp4,zip|max:50000',
        ]);
        
        // Store file
        $filePath = $request->file('file')->store('lectures', 'public');
        
        // Create lecture
        $lecture = $course->lectures()->create([
            'title' => $request->title,
            'description' => $request->description,
            'file_url' => $filePath,
            'uploaded_at' => now(),
        ]);
        
        return redirect()->route('lectures.index', $course)
            ->with('success', 'Bài giảng đã được tạo thành công.');
    }
    
    /**
     * Display the specified lecture.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course, Lecture $lecture)
    {
        // Check if lecture belongs to the course
        if ($lecture->course_id !== $course->id) {
            return redirect()->route('lectures.index', $course)
                ->with('error', 'Bài giảng không thuộc khóa học này.');
        }
        
        // Check if user has permission to view lectures
        $isContentUser = $course->content_user_id === Auth::id();
        $isRegistered = Auth::user()->registeredCourses()->where('courses.id', $course->id)->exists();
        $isAdmin = Auth::user()->isAdmin();
        
        if (!$isContentUser && !$isRegistered && !$isAdmin) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Bạn không có quyền xem bài giảng của khóa học này.');
        }
        
        return view('lectures.show', compact('course', 'lecture'));
    }
    
    /**
     * Show the form for editing the specified lecture.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course, Lecture $lecture)
    {
        // Check if lecture belongs to the course
        if ($lecture->course_id !== $course->id) {
            return redirect()->route('lectures.index', $course)
                ->with('error', 'Bài giảng không thuộc khóa học này.');
        }
        
        // Check if user has permission to edit lectures
        $isContentUser = $course->content_user_id === Auth::id();
        $isAdmin = Auth::user()->isAdmin();
        
        if (!$isContentUser && !$isAdmin) {
            return redirect()->route('lectures.index', $course)
                ->with('error', 'Bạn không có quyền sửa bài giảng của khóa học này.');
        }
        
        return view('lectures.edit', compact('course', 'lecture'));
    }
    
    /**
     * Update the specified lecture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course, Lecture $lecture)
    {
        // Check if lecture belongs to the course
        if ($lecture->course_id !== $course->id) {
            return redirect()->route('lectures.index', $course)
                ->with('error', 'Bài giảng không thuộc khóa học này.');
        }
        
        // Check if user has permission to edit lectures
        $isContentUser = $course->content_user_id === Auth::id();
        $isAdmin = Auth::user()->isAdmin();
        
        if (!$isContentUser && !$isAdmin) {
            return redirect()->route('lectures.index', $course)
                ->with('error', 'Bạn không có quyền sửa bài giảng của khóa học này.');
        }
        
        // Validate request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,mp4,zip|max:50000',
        ]);
        
        // Update lecture data
        $lecture->title = $request->title;
        $lecture->description = $request->description;
        
        // Update file if provided
        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public')->delete($lecture->file_url);
            
            // Store new file
            $filePath = $request->file('file')->store('lectures', 'public');
            $lecture->file_url = $filePath;
            $lecture->uploaded_at = now();
        }
        
        $lecture->save();
        
        return redirect()->route('lectures.index', $course)
            ->with('success', 'Bài giảng đã được cập nhật thành công.');
    }
    
    /**
     * Remove the specified lecture from storage.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course, Lecture $lecture)
    {
        // Check if lecture belongs to the course
        if ($lecture->course_id !== $course->id) {
            return redirect()->route('lectures.index', $course)
                ->with('error', 'Bài giảng không thuộc khóa học này.');
        }
        
        // Check if user has permission to delete lectures
        $isContentUser = $course->content_user_id === Auth::id();
        $isAdmin = Auth::user()->isAdmin();
        
        if (!$isContentUser && !$isAdmin) {
            return redirect()->route('lectures.index', $course)
                ->with('error', 'Bạn không có quyền xóa bài giảng của khóa học này.');
        }
        
        // Delete file
        Storage::disk('public')->delete($lecture->file_url);
        
        // Delete lecture
        $lecture->delete();
        
        return redirect()->route('lectures.index', $course)
            ->with('success', 'Bài giảng đã được xóa thành công.');
    }
    
    /**
     * Display a listing of all lectures.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAll()
    {
        // Check user role
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin sees all lectures
            $lectures = Lecture::with('course')->latest('uploaded_at')->paginate(10);
        } elseif ($user->isContentUser()) {
            // Content user sees lectures of courses they manage
            $courseIds = $user->managedCourses()->pluck('id');
            $lectures = Lecture::whereIn('course_id', $courseIds)
                ->with('course')
                ->latest('uploaded_at')
                ->paginate(10);
        } else {
            // Regular user sees lectures of courses they're registered for
            $courseIds = $user->registeredCourses()->pluck('courses.id');
            $lectures = Lecture::whereIn('course_id', $courseIds)
                ->with('course')
                ->latest('uploaded_at')
                ->paginate(10);
        }
        
        return view('lectures.all', compact('lectures'));
    }
}
