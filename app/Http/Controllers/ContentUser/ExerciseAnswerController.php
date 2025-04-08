<?php

namespace App\Http\Controllers\ContentUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Exercise;
use App\Models\UserExerciseAnswer;
use Illuminate\Support\Facades\Auth;

class ExerciseAnswerController extends Controller
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
     * Display a listing of user answers for review.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function index(Course $course)
    {
        // Check if the content user manages this course
        if ($course->content_user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('content.courses.index')
                ->with('error', 'You do not have permission to view answers for this course.');
        }
        
        // Get all ungraded answers for this course's exercises
        $ungradedAnswers = UserExerciseAnswer::whereNull('graded_at')
            ->whereHas('exercise', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->with(['user', 'exercise'])
            ->latest()
            ->paginate(20);
        
        return view('content_user.answers.index', compact('course', 'ungradedAnswers'));
    }
    
    /**
     * Display a specific answer for grading.
     *
     * @param  \App\Models\UserExerciseAnswer  $answer
     * @return \Illuminate\Http\Response
     */
    public function show(UserExerciseAnswer $answer)
    {
        $exercise = $answer->exercise;
        $course = $exercise->course;
        
        // Check if the content user manages this course
        if ($course->content_user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('content.courses.index')
                ->with('error', 'You do not have permission to view this answer.');
        }
        
        return view('content_user.answers.show', compact('answer', 'exercise', 'course'));
    }
    
    /**
     * Grade a user exercise answer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserExerciseAnswer  $answer
     * @return \Illuminate\Http\Response
     */
    public function grade(Request $request, UserExerciseAnswer $answer)
    {
        $exercise = $answer->exercise;
        $course = $exercise->course;
        
        // Check if the content user manages this course
        if ($course->content_user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('content.courses.index')
                ->with('error', 'You do not have permission to grade this answer.');
        }
        
        $request->validate([
            'is_correct' => 'required|boolean',
            'feedback' => 'nullable|string',
        ]);
        
        $answer->update([
            'is_correct' => $request->is_correct,
            'feedback' => $request->feedback,
            'graded_at' => now(),
        ]);
        
        return redirect()->route('content.courses.answers.index', $course)
            ->with('success', 'Answer graded successfully.');
    }
    
    /**
     * Display a listing of graded answers for a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function gradedAnswers(Course $course)
    {
        // Check if the content user manages this course
        if ($course->content_user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('content.courses.index')
                ->with('error', 'You do not have permission to view answers for this course.');
        }
        
        // Get all graded answers for this course's exercises
        $gradedAnswers = UserExerciseAnswer::whereNotNull('graded_at')
            ->whereHas('exercise', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->with(['user', 'exercise'])
            ->latest('graded_at')
            ->paginate(20);
        
        return view('content_user.answers.graded', compact('course', 'gradedAnswers'));
    }
    
    /**
     * Display answers for a specific exercise.
     *
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function exerciseAnswers(Exercise $exercise)
    {
        $course = $exercise->course;
        
        // Check if the content user manages this course
        if ($course->content_user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('content.courses.index')
                ->with('error', 'You do not have permission to view answers for this exercise.');
        }
        
        // Get all answers for this exercise
        $answers = UserExerciseAnswer::where('exercise_id', $exercise->id)
            ->with('user')
            ->latest()
            ->paginate(20);
        
        return view('content_user.answers.exercise', compact('exercise', 'course', 'answers'));
    }
} 