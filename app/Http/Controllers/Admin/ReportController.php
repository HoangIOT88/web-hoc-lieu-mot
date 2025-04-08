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
use Illuminate\Support\Facades\Storage;

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

        // Tạo file báo cáo trước
        $filename = 'reports/user_stats_' . now()->format('Y-m-d_His') . '.csv';
        $filePath = storage_path('app/public/' . $filename);
        
        // Đảm bảo thư mục tồn tại
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Tạo file CSV đơn giản
        $file = fopen($filePath, 'w');
        
        // Header
        fputcsv($file, ['Report Type', 'User Statistics']);
        fputcsv($file, ['Generated At', now()->format('Y-m-d H:i:s')]);
        fputcsv($file, ['Period', $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
        fputcsv($file, []);
        
        // User growth
        fputcsv($file, ['User Growth']);
        fputcsv($file, ['Date', 'Number of New Users']);
        foreach ($userGrowth as $growth) {
            fputcsv($file, [$growth->date, $growth->count]);
        }
        fputcsv($file, []);
        
        // Users by role
        fputcsv($file, ['Users by Role']);
        fputcsv($file, ['Role', 'Count']);
        foreach ($usersByRole as $role) {
            fputcsv($file, [$role->role, $role->count]);
        }
        
        fclose($file);
        
        // Create report record với file đã tạo
        $report = AdminReport::create([
            'generated_by_admin_id' => Auth::id(),
            'report_type' => 'USER_STATS',
            'file_url' => 'public/' . $filename,
            'generated_at' => now(),
        ]);
        
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
        
        // Tạo file báo cáo
        $filename = 'reports/course_stats_' . now()->format('Y-m-d_His') . '.csv';
        $filePath = storage_path('app/public/' . $filename);
        
        // Đảm bảo thư mục tồn tại
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Tạo file CSV đơn giản
        $file = fopen($filePath, 'w');
        
        // Header
        fputcsv($file, ['Report Type', 'Course Statistics']);
        fputcsv($file, ['Generated At', now()->format('Y-m-d H:i:s')]);
        fputcsv($file, ['Period', $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
        fputcsv($file, []);
        
        // Course growth
        fputcsv($file, ['Course Creation']);
        fputcsv($file, ['Date', 'Number of New Courses']);
        foreach ($courseGrowth as $growth) {
            fputcsv($file, [$growth->date, $growth->count]);
        }
        fputcsv($file, []);
        
        // Popular courses
        fputcsv($file, ['Most Popular Courses']);
        fputcsv($file, ['Course ID', 'Course Name', 'Registrations']);
        foreach ($popularCourses as $course) {
            fputcsv($file, [$course->id, $course->name, $course->registration_count]);
        }
        
        fclose($file);
        
        // Create report record with file URL
        $report = AdminReport::create([
            'generated_by_admin_id' => Auth::id(),
            'report_type' => 'COURSE_STATS',
            'file_url' => 'public/' . $filename,
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
        
        // Tạo file báo cáo
        $filename = 'reports/activity_stats_' . now()->format('Y-m-d_His') . '.csv';
        $filePath = storage_path('app/public/' . $filename);
        
        // Đảm bảo thư mục tồn tại
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Tạo file CSV đơn giản
        $file = fopen($filePath, 'w');
        
        // Header
        fputcsv($file, ['Report Type', 'Activity Statistics']);
        fputcsv($file, ['Generated At', now()->format('Y-m-d H:i:s')]);
        fputcsv($file, ['Period', $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
        fputcsv($file, []);
        
        // Submissions
        fputcsv($file, ['Exercise Submissions']);
        fputcsv($file, ['Date', 'Number of Submissions']);
        foreach ($submissionStats as $stat) {
            fputcsv($file, [$stat->date, $stat->count]);
        }
        
        fclose($file);
        
        // Create report record with file URL
        $report = AdminReport::create([
            'generated_by_admin_id' => Auth::id(),
            'report_type' => 'ACTIVITY_STATS',
            'file_url' => 'public/' . $filename,
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
