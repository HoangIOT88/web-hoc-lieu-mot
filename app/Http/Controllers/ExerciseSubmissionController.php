<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\ExerciseSubmission;
use App\Models\UserExerciseAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExerciseSubmissionController extends Controller
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
     * Display a listing of the current user's submissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $submissions = Auth::user()->exerciseSubmissions()
            ->with('exercise.course')
            ->latest()
            ->paginate(10);
            
        return view('exercise-submissions.index', compact('submissions'));
    }
    
    /**
     * Display a listing of all submissions (for admin and content users).
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        // Check if user has permission to view all submissions
        if (!Auth::user()->isAdmin() && !Auth::user()->isContentUser()) {
            return redirect()->route('exercise-submissions.index')
                ->with('error', 'Bạn không có quyền xem tất cả các bài tập đã nộp.');
        }
        
        $submissions = ExerciseSubmission::with(['user', 'exercise.course'])
            ->latest()
            ->paginate(20);
            
        return view('exercise-submissions.all', compact('submissions'));
    }
    
    /**
     * Display the specified submission.
     *
     * @param  \App\Models\ExerciseSubmission  $submission
     * @return \Illuminate\Http\Response
     */
    public function show(ExerciseSubmission $submission)
    {
        // Check if user has permission to view this submission
        $isContentUser = false;
        if ($submission->exercise && $submission->exercise->course) {
            $isContentUser = $submission->exercise->course->content_user_id === Auth::id();
        }
        
        if (!Auth::user()->isAdmin() && !$isContentUser && $submission->user_id !== Auth::id()) {
            return redirect()->route('exercise-submissions.index')
                ->with('error', 'Bạn không có quyền xem bài nộp này.');
        }
        
        return view('exercise-submissions.show', compact('submission'));
    }
    
    /**
     * Show the form for grading a submission.
     *
     * @param  \App\Models\ExerciseSubmission  $submission
     * @return \Illuminate\Http\Response
     */
    public function showGradeForm(ExerciseSubmission $submission)
    {
        // Check if user has permission to grade submissions
        $isContentUser = false;
        if ($submission->exercise && $submission->exercise->course) {
            $isContentUser = $submission->exercise->course->content_user_id === Auth::id();
        }
        
        if (!Auth::user()->isAdmin() && !$isContentUser) {
            return redirect()->route('exercise-submissions.show', $submission)
                ->with('error', 'Bạn không có quyền chấm điểm bài nộp này.');
        }
        
        return view('exercise-submissions.grade', compact('submission'));
    }
    
    /**
     * Update the specified submission's grade.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExerciseSubmission  $submission
     * @return \Illuminate\Http\Response
     */
    public function grade(Request $request, ExerciseSubmission $submission)
    {
        // Check if user has permission to grade submissions
        $isContentUser = false;
        if ($submission->exercise && $submission->exercise->course) {
            $isContentUser = $submission->exercise->course->content_user_id === Auth::id();
        }
        
        if (!Auth::user()->isAdmin() && !$isContentUser) {
            return redirect()->route('exercise-submissions.show', $submission)
                ->with('error', 'Bạn không có quyền chấm điểm bài nộp này.');
        }
        
        $request->validate([
            'score' => 'required|numeric|min:0|max:' . $submission->exercise->max_score,
            'feedback' => 'nullable|string|max:1000',
        ]);
        
        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'graded_at' => now(),
            'graded_by' => Auth::id(),
            'is_graded' => true,
        ]);
        
        return redirect()->route('exercise-submissions.show', $submission)
            ->with('success', 'Bài nộp đã được chấm điểm thành công.');
    }
} 