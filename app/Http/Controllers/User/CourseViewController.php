<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Exercise;
use App\Models\UserExerciseAnswer;
use Illuminate\Support\Facades\Auth;

class CourseViewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('regular.user');
        $this->middleware('course.registered');
    }
    
    /**
     * Display the course overview.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function overview(Course $course)
    {
        $lectures = $course->lectures()->orderBy('order')->get();
        $progress = $this->calculateProgress($course);
        
        return view('user.course.overview', compact('course', 'lectures', 'progress'));
    }
    
    /**
     * Display a lecture.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @return \Illuminate\Http\Response
     */
    public function viewLecture(Course $course, Lecture $lecture)
    {
        // Ensure the lecture belongs to the course
        if ($lecture->course_id !== $course->id) {
            abort(404);
        }
        
        // Get next and previous lectures for navigation
        $nextLecture = Lecture::where('course_id', $course->id)
            ->where('order', '>', $lecture->order)
            ->orderBy('order')
            ->first();
            
        $prevLecture = Lecture::where('course_id', $course->id)
            ->where('order', '<', $lecture->order)
            ->orderBy('order', 'desc')
            ->first();
        
        // Get exercises for this lecture
        $exercises = $lecture->exercises()->orderBy('order')->get();
        
        return view('user.course.lecture', compact('course', 'lecture', 'exercises', 'nextLecture', 'prevLecture'));
    }
    
    /**
     * Display an exercise.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function viewExercise(Course $course, Lecture $lecture, Exercise $exercise)
    {
        // Ensure the exercise belongs to the lecture and the lecture belongs to the course
        if ($exercise->lecture_id !== $lecture->id || $lecture->course_id !== $course->id) {
            abort(404);
        }
        
        // Get the user's previous answer, if any
        $userAnswer = UserExerciseAnswer::where('user_id', Auth::id())
            ->where('exercise_id', $exercise->id)
            ->first();
        
        // Get next and previous exercises for navigation
        $nextExercise = Exercise::where('lecture_id', $lecture->id)
            ->where('order', '>', $exercise->order)
            ->orderBy('order')
            ->first();
            
        $prevExercise = Exercise::where('lecture_id', $lecture->id)
            ->where('order', '<', $exercise->order)
            ->orderBy('order', 'desc')
            ->first();
        
        return view('user.course.exercise', compact('course', 'lecture', 'exercise', 'userAnswer', 'nextExercise', 'prevExercise'));
    }
    
    /**
     * Submit an answer for an exercise.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lecture  $lecture
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function submitExerciseAnswer(Request $request, Course $course, Lecture $lecture, Exercise $exercise)
    {
        // Validate the request
        $request->validate([
            'answer' => 'required|string',
        ]);
        
        // Check if the user has already submitted an answer
        $existingAnswer = UserExerciseAnswer::where('user_id', Auth::id())
            ->where('exercise_id', $exercise->id)
            ->first();
            
        if ($existingAnswer) {
            // Update existing answer
            $existingAnswer->update([
                'answer' => $request->answer,
                'updated_at' => now(),
            ]);
        } else {
            // Create new answer
            UserExerciseAnswer::create([
                'user_id' => Auth::id(),
                'exercise_id' => $exercise->id,
                'answer' => $request->answer,
            ]);
        }
        
        return redirect()->back()->with('success', 'Your answer has been submitted successfully.');
    }
    
    /**
     * Calculate the user's progress in the course.
     *
     * @param  \App\Models\Course  $course
     * @return array
     */
    private function calculateProgress(Course $course)
    {
        $totalExercises = Exercise::whereHas('lecture', function($query) use ($course) {
            $query->where('course_id', $course->id);
        })->count();
        
        $completedExercises = UserExerciseAnswer::where('user_id', Auth::id())
            ->whereHas('exercise', function($query) use ($course) {
                $query->whereHas('lecture', function($q) use ($course) {
                    $q->where('course_id', $course->id);
                });
            })
            ->count();
        
        $percentage = $totalExercises > 0 ? round(($completedExercises / $totalExercises) * 100) : 0;
        
        return [
            'total' => $totalExercises,
            'completed' => $completedExercises,
            'percentage' => $percentage
        ];
    }
} 