<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class DashboardController extends Controller
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
     * Show the appropriate dashboard based on user role.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isContentUser()) {
            return $this->contentUserDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    protected function adminDashboard()
    {
        // Tổng hợp dữ liệu thống kê cho trang admin
        $totalUsers = \App\Models\User::count();
        $totalCourses = \App\Models\Course::count();
        $totalRegistrations = \App\Models\CourseRegistration::count();
        
        return view('admin.dashboard', compact('totalUsers', 'totalCourses', 'totalRegistrations'));
    }

    /**
     * Show the content user dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    protected function contentUserDashboard()
    {
        // Lấy các khóa học do content user quản lý
        $managedCourses = Auth::user()->managedCourses()->count();
        $pendingApprovals = \App\Models\UserApproval::where('status', 'PENDING')->count();
        
        return view('content_user.dashboard', compact('managedCourses', 'pendingApprovals'));
    }

    /**
     * Show the regular user dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    protected function userDashboard()
    {
        // Lấy khóa học mà user đã đăng ký và các khóa học khuyến nghị
        $registeredCourses = Auth::user()->registeredCourses;
        $availableCourses = Course::whereNotIn('id', Auth::user()->registeredCourses()->pluck('courses.id'))->take(5)->get();
        
        return view('user.dashboard', compact('registeredCourses', 'availableCourses'));
    }
} 