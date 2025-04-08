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
     * Show the application dashboard based on user role.
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
