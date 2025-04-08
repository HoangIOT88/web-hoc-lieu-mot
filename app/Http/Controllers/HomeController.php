<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class HomeController extends Controller
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
     * Redirect to the dashboard controller
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Chuyển hướng đến DashboardController để tránh trùng lặp logic
        return redirect()->route('dashboard');
    }
    
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    private function adminDashboard()
    {
        return view('admin.dashboard');
    }
    
    /**
     * Show the content user dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    private function contentUserDashboard()
    {
        $managedCourses = Auth::user()->managedCourses()->paginate(10);
        return view('content_user.dashboard', compact('managedCourses'));
    }
    
    /**
     * Show the regular user dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    private function userDashboard()
    {
        $registeredCourses = Auth::user()->registeredCourses()->paginate(10);
        $availableCourses = Course::whereNotIn('id', Auth::user()->registeredCourses()->pluck('courses.id'))->paginate(10);
        
        return view('user.dashboard', compact('registeredCourses', 'availableCourses'));
    }
}
