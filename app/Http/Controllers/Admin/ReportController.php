<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\ExerciseSubmission;
use App\Models\AdminReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    /**
     * Display the report dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalRegistrations = CourseRegistration::count();
        
        // User statistics by role
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get();
        
        // Users registered in the last 30 days
        $newUsers = User::where('created_at', '>=', now()->subDays(30))
            ->count();
        
        // Course registrations by month (last 6 months)
        $registrationsByMonth = CourseRegistration::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Previous reports
        $reports = AdminReport::where('generated_by_admin_id', Auth::id())
            ->latest('generated_at')
            ->take(10)
            ->get();
        
        return view('admin.reports.index', compact(
            'totalUsers',
            'totalCourses',
            'totalRegistrations',
            'usersByRole',
            'newUsers',
            'registrationsByMonth',
            'reports'
        ));
    }
    
    /**
     * Generate user statistics report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateUserStats(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(6);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        
        // User growth over time
        $userGrowth = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Users by role
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('role')
            ->get();
        
        // Create report record
        $report = AdminReport::create([
            'generated_by_admin_id' => Auth::id(),
            'report_type' => 'USER_STATS',
            'file_url' => null, // Will be updated after file generation
            'generated_at' => now(),
        ]);
        
        // Generate PDF/Excel file (implementation would depend on the package you use)
        // For this example, we'll just redirect back with the report ID
        
        return redirect()->route('admin.reports.show', $report)
            ->with('success', 'User statistics report generated successfully.');
    }
    
    /**
     * Generate course statistics report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateCourseStats(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(6);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        
        // Course creation over time
        $courseGrowth = Course::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Most popular courses by registration
        $popularCourses = Course::select('courses.id', 'courses.name', DB::raw('count(course_registrations.id) as registration_count'))
            ->leftJoin('course_registrations', 'courses.id', '=', 'course_registrations.course_id')
            ->whereBetween('course_registrations.created_at', [$startDate, $endDate])
            ->groupBy('courses.id', 'courses.name')
            ->orderByDesc('registration_count')
            ->take(10)
            ->get();
        
        // Create report record
        $report = AdminReport::create([
            'generated_by_admin_id' => Auth::id(),
            'report_type' => 'COURSE_STATS',
            'file_url' => null, // Will be updated after file generation
            'generated_at' => now(),
        ]);
        
        return redirect()->route('admin.reports.show', $report)
            ->with('success', 'Course statistics report generated successfully.');
    }
    
    /**
     * Generate activity statistics report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateActivityStats(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(6);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        
        // Exercise submissions over time
        $submissionStats = ExerciseSubmission::select(
                DB::raw('DATE(submitted_at) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Create report record
        $report = AdminReport::create([
            'generated_by_admin_id' => Auth::id(),
            'report_type' => 'ACTIVITY_STATS',
            'file_url' => null, // Will be updated after file generation
            'generated_at' => now(),
        ]);
        
        return redirect()->route('admin.reports.show', $report)
            ->with('success', 'Activity statistics report generated successfully.');
    }
    
    /**
     * Display a specific report.
     *
     * @param  \App\Models\AdminReport  $report
     * @return \Illuminate\Http\Response
     */
    public function show(AdminReport $report)
    {
        return view('admin.reports.show', compact('report'));
    }
    
    /**
     * Download a report file.
     *
     * @param  \App\Models\AdminReport  $report
     * @return \Illuminate\Http\Response
     */
    public function download(AdminReport $report)
    {
        // Check if the report has a file
        if (!$report->file_url) {
            return redirect()->route('admin.reports.index')
                ->with('error', 'This report does not have a downloadable file.');
        }
        
        // Return file download response
        return response()->download(storage_path('app/' . $report->file_url));
    }
}
