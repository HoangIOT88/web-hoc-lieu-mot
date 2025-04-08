<?php

namespace App\Http\Controllers\ContentUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Exercise;
use App\Models\ExerciseSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('content.user');
    }
    
    /**
     * Display a listing of the courses managed by the content user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Auth::user()->managedCourses()->paginate(10);
        return view('content_user.courses.index', compact('courses'));
    }
    
    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('content_user.courses.create');
    }
    
    /**
     * Store a newly created course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|string|max:255',
        ]);
        
        $course = Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
            'content_user_id' => Auth::id(),
        ]);
        
        return redirect()->route('content.courses.index')->with('success', 'Course created successfully.');
    }
    
    /**
     * Display the specified course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        $this->checkCourseOwnership($course);
        
        $lectures = $course->lectures;
        $exercises = $course->exercises;
        
        return view('content_user.courses.show', compact('course', 'lectures', 'exercises'));
    }
    
    /**
     * Show the form for editing the specified course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        $this->checkCourseOwnership($course);
        
        return view('content_user.courses.edit', compact('course'));
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
        $this->checkCourseOwnership($course);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|string|max:255',
        ]);
        
        $course->update([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
        ]);
        
        return redirect()->route('content.courses.index')->with('success', 'Course updated successfully.');
    }
    
    /**
     * Remove the specified course from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $this->checkCourseOwnership($course);
        
        $course->delete();
        
        return redirect()->route('content.courses.index')->with('success', 'Course deleted successfully.');
    }
    
    /**
     * Show the form for creating a new lecture.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function createLecture(Course $course)
    {
        $this->checkCourseOwnership($course);
        
        return view('content_user.lectures.create', compact('course'));
    }
    
    /**
     * Store a newly created lecture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function storeLecture(Request $request, Course $course)
    {
        $this->checkCourseOwnership($course);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lecture_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip|max:10240',
        ]);
        
        $data = [
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'uploaded_at' => now(),
        ];
        
        // Handle file upload
        if ($request->hasFile('lecture_file')) {
            $file = $request->file('lecture_file');
            $filename = Str::slug($request->title) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('lectures', $filename, 'public');
            $data['file_url'] = $path;
        }
        
        Lecture::create($data);
        
        return redirect()->route('content.courses.show', $course)->with('success', 'Lecture created successfully.');
    }
    
    /**
     * Show the form for editing a lecture.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @return \Illuminate\Http\Response
     */
    public function editLecture(Course $course, Lecture $lecture)
    {
        $this->checkCourseOwnership($course);
        
        if ($lecture->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('content_user.lectures.edit', compact('course', 'lecture'));
    }
    
    /**
     * Update the specified lecture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @return \Illuminate\Http\Response
     */
    public function updateLecture(Request $request, Course $course, Lecture $lecture)
    {
        $this->checkCourseOwnership($course);
        
        if ($lecture->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lecture_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip|max:10240',
        ]);
        
        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];
        
        // Handle file upload
        if ($request->hasFile('lecture_file')) {
            // Delete old file if exists
            if ($lecture->file_url) {
                Storage::delete('public/' . $lecture->file_url);
            }
            
            $file = $request->file('lecture_file');
            $filename = Str::slug($request->title) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('lectures', $filename, 'public');
            $data['file_url'] = $path;
        }
        
        $lecture->update($data);
        
        return redirect()->route('content.courses.show', $course)->with('success', 'Lecture updated successfully.');
    }
    
    /**
     * Remove the specified lecture from storage.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @return \Illuminate\Http\Response
     */
    public function destroyLecture(Course $course, Lecture $lecture)
    {
        $this->checkCourseOwnership($course);
        
        if ($lecture->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete file if exists
        if ($lecture->file_url) {
            Storage::delete('public/' . $lecture->file_url);
        }
        
        $lecture->delete();
        
        return redirect()->route('content.courses.show', $course)->with('success', 'Lecture deleted successfully.');
    }
    
    /**
     * Show the form for creating a new exercise.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function createExercise(Course $course)
    {
        $this->checkCourseOwnership($course);
        
        return view('content_user.exercises.create', compact('course'));
    }
    
    /**
     * Store a newly created exercise in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function storeExercise(Request $request, Course $course)
    {
        $this->checkCourseOwnership($course);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'deadline' => 'nullable|date|after:today',
        ]);
        
        Exercise::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'content' => $request->content,
            'deadline' => $request->deadline,
        ]);
        
        return redirect()->route('content.courses.show', $course)->with('success', 'Exercise created successfully.');
    }
    
    /**
     * Show the form for editing an exercise.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function editExercise(Course $course, Exercise $exercise)
    {
        $this->checkCourseOwnership($course);
        
        if ($exercise->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('content_user.exercises.edit', compact('course', 'exercise'));
    }
    
    /**
     * Update the specified exercise in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function updateExercise(Request $request, Course $course, Exercise $exercise)
    {
        $this->checkCourseOwnership($course);
        
        if ($exercise->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'deadline' => 'nullable|date',
        ]);
        
        $exercise->update([
            'title' => $request->title,
            'content' => $request->content,
            'deadline' => $request->deadline,
        ]);
        
        return redirect()->route('content.courses.show', $course)->with('success', 'Exercise updated successfully.');
    }
    
    /**
     * Remove the specified exercise from storage.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function destroyExercise(Course $course, Exercise $exercise)
    {
        $this->checkCourseOwnership($course);
        
        if ($exercise->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $exercise->delete();
        
        return redirect()->route('content.courses.show', $course)->with('success', 'Exercise deleted successfully.');
    }
    
    /**
     * View exercise submissions.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function viewSubmissions(Course $course, Exercise $exercise)
    {
        $this->checkCourseOwnership($course);
        
        if ($exercise->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $submissions = $exercise->submissions()->with('user')->paginate(10);
        
        return view('content_user.exercises.submissions', compact('course', 'exercise', 'submissions'));
    }
    
    /**
     * Grade an exercise submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Exercise  $exercise
     * @param  \App\Models\ExerciseSubmission  $submission
     * @return \Illuminate\Http\Response
     */
    public function gradeSubmission(Request $request, Course $course, Exercise $exercise, ExerciseSubmission $submission)
    {
        $this->checkCourseOwnership($course);
        
        if ($exercise->course_id !== $course->id || $submission->exercise_id !== $exercise->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);
        
        $submission->update([
            'score' => $request->score,
            'graded_at' => now(),
        ]);
        
        return redirect()->route('content.courses.exercises.submissions', [$course, $exercise])
            ->with('success', 'Submission graded successfully.');
    }
    
    /**
     * Check if the authenticated user owns the course.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    private function checkCourseOwnership(Course $course)
    {
        if ($course->content_user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
