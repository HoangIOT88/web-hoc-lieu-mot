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
