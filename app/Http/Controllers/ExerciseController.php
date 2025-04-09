<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Models\Course;
use App\Models\UserExerciseAnswer;
use Illuminate\Support\Facades\Auth;

class ExerciseController extends Controller
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
     * Display exercises for a specific course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function index(Course $course)
    {
        // Check if user is registered for the course
        $isRegistered = Auth::user()->registeredCourses()->where('courses.id', $course->id)->exists();
        
        if (!$isRegistered && !Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('courses.show', $course)->with('error', 'You must be registered for this course to view exercises.');
        }
        
        $exercises = $course->exercises;
        
        return view('exercises.index', compact('exercises', 'course'));
    }
    
    /**
     * Display all exercises across all courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAll()
    {
        // Determine visibility based on user role
        if (Auth::user()->isAdmin() || Auth::user()->isContentUser()) {
            // Admins and content users can see all exercises
            $exercises = Exercise::with('course')->latest()->paginate(10);
        } else {
            // Regular users can only see exercises from courses they're registered for
            $userCourseIds = Auth::user()->registeredCourses()->pluck('courses.id');
            $exercises = Exercise::whereIn('course_id', $userCourseIds)
                ->with('course')
                ->latest()
                ->paginate(10);
        }
        
        return view('exercises.all', compact('exercises'));
    }

    /**
     * Show the form for creating a new exercise.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check if user has permission to create exercises
        if (!Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('exercises.index')->with('error', 'Bạn không có quyền tạo bài tập.');
        }
        
        // Get courses that the user has permission to add exercises to
        if (Auth::user()->isAdmin()) {
            $courses = Course::all();
        } else {
            $courses = Course::where('content_user_id', Auth::id())->get();
        }
        
        if ($courses->isEmpty()) {
            return redirect()->route('exercises.index')
                ->with('error', 'Bạn cần tạo khóa học trước khi thêm bài tập.');
        }
        
        return view('exercises.create', compact('courses'));
    }
    
    /**
     * Store a newly created exercise in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if user has permission to create exercises
        if (!Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('exercises.index')->with('error', 'Bạn không có quyền tạo bài tập.');
        }
        
        // Validate request
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'deadline' => 'nullable|date',
        ]);
        
        // Check if user has permission for the specified course
        $course = Course::findOrFail($request->course_id);
        
        if (!Auth::user()->isAdmin() && $course->content_user_id !== Auth::id()) {
            return redirect()->route('exercises.index')
                ->with('error', 'Bạn không có quyền tạo bài tập cho khóa học này.');
        }
        
        // Create exercise
        $exercise = Exercise::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'content' => $request->content,
            'deadline' => $request->deadline,
        ]);
        
        return redirect()->route('courses.exercises', $course)
            ->with('success', 'Bài tập đã được tạo thành công.');
    }
    
    /**
     * Show the exercise details and submission form.
     *
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function show(Exercise $exercise)
    {
        $course = $exercise->course;
        
        // Check if user is registered for the course
        $isRegistered = Auth::user()->registeredCourses()->where('courses.id', $course->id)->exists();
        
        if (!$isRegistered && !Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('courses.show', $course)->with('error', 'You must be registered for this course to view exercises.');
        }
        
        // Get the user's submission for this exercise if it exists
        $submission = UserExerciseAnswer::where('exercise_id', $exercise->id)
            ->where('user_id', Auth::id())
            ->first();
        
        return view('exercises.show', compact('exercise', 'course', 'submission'));
    }
    
    /**
     * Show the form for editing the specified exercise.
     *
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function edit(Exercise $exercise)
    {
        $course = $exercise->course;
        
        // Check if user has permission to edit the exercise
        if (!Auth::user()->isAdmin() && $course->content_user_id !== Auth::id()) {
            return redirect()->route('exercises.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa bài tập này.');
        }
        
        // Get courses that the user has permission to add exercises to
        if (Auth::user()->isAdmin()) {
            $courses = Course::all();
        } else {
            $courses = Course::where('content_user_id', Auth::id())->get();
        }
        
        return view('exercises.edit', compact('exercise', 'courses'));
    }
    
    /**
     * Update the specified exercise in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Exercise $exercise)
    {
        $course = $exercise->course;
        
        // Check if user has permission to edit the exercise
        if (!Auth::user()->isAdmin() && $course->content_user_id !== Auth::id()) {
            return redirect()->route('exercises.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa bài tập này.');
        }
        
        // Validate request
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'deadline' => 'nullable|date',
        ]);
        
        // Check if new course_id belongs to the user
        $newCourse = Course::findOrFail($request->course_id);
        
        if (!Auth::user()->isAdmin() && $newCourse->content_user_id !== Auth::id()) {
            return redirect()->route('exercises.edit', $exercise)
                ->with('error', 'Bạn không có quyền chuyển bài tập sang khóa học này.');
        }
        
        // Update exercise
        $exercise->update([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'content' => $request->content,
            'deadline' => $request->deadline,
        ]);
        
        return redirect()->route('courses.exercises', $newCourse)
            ->with('success', 'Bài tập đã được cập nhật thành công.');
    }
    
    /**
     * Remove the specified exercise from storage.
     *
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exercise $exercise)
    {
        $course = $exercise->course;
        
        // Check if user has permission to delete the exercise
        if (!Auth::user()->isAdmin() && $course->content_user_id !== Auth::id()) {
            return redirect()->route('exercises.index')
                ->with('error', 'Bạn không có quyền xóa bài tập này.');
        }
        
        // Delete the exercise
        $exercise->delete();
        
        return redirect()->route('courses.exercises', $course)
            ->with('success', 'Bài tập đã được xóa thành công.');
    }
    
    /**
     * Submit an exercise.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request, Exercise $exercise)
    {
        $course = $exercise->course;
        
        // Check if user is registered for the course
        $isRegistered = Auth::user()->registeredCourses()->where('courses.id', $course->id)->exists();
        
        if (!$isRegistered) {
            return redirect()->route('courses.show', $course)->with('error', 'You must be registered for this course to submit exercises.');
        }
        
        // Validate the submission
        $request->validate([
            'answer_content' => 'required|string',
        ]);
        
        // Check if submission already exists
        $existingSubmission = UserExerciseAnswer::where('exercise_id', $exercise->id)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($existingSubmission) {
            // Update existing submission
            $existingSubmission->update([
                'answer_content' => $request->answer_content,
                'submitted_at' => now(),
                'is_correct' => null,
                'feedback' => null,
                'graded_at' => null,
            ]);
            
            return redirect()->route('exercises.show', $exercise)->with('success', 'Your submission has been updated.');
        } else {
            // Create new submission
            UserExerciseAnswer::create([
                'exercise_id' => $exercise->id,
                'user_id' => Auth::id(),
                'answer_content' => $request->answer_content,
                'submitted_at' => now(),
            ]);
            
            return redirect()->route('exercises.show', $exercise)->with('success', 'Your submission has been received.');
        }
    }
}
