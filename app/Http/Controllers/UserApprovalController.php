<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\UserApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserApprovalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:content_user,admin')->except(['index']);
    }
    
    /**
     * Display a listing of the course registration requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Kiểm tra quyền truy cập
        if (!Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
        }
        
        if (Auth::user()->isAdmin()) {
            // Admin có thể xem tất cả các yêu cầu
            $pendingApprovals = UserApproval::with(['user', 'course'])
                ->where('status', UserApproval::STATUS_PENDING)
                ->latest()
                ->get();
        } else {
            // Content user chỉ xem các yêu cầu liên quan đến khóa học của họ
            $pendingApprovals = UserApproval::with(['user', 'course'])
                ->whereHas('course', function($query) {
                    $query->where('content_user_id', Auth::id());
                })
                ->where('status', UserApproval::STATUS_PENDING)
                ->latest()
                ->get();
        }
        
        return view('user-approvals.index', compact('pendingApprovals'));
    }
    
    /**
     * Display the detail of a course registration request.
     *
     * @param  \App\Models\UserApproval  $approval
     * @return \Illuminate\Http\Response
     */
    public function show(UserApproval $approval)
    {
        // Kiểm tra quyền truy cập
        if (!Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
        }
        
        // Kiểm tra xem content user có quyền xem approval này không
        $canView = Auth::user()->isAdmin() || 
                  (Auth::user()->isContentUser() && $approval->course->content_user_id == Auth::id());
        
        if (!$canView) {
            return redirect()->route('user-approvals.index')
                ->with('error', 'Bạn không có quyền xem chi tiết yêu cầu này.');
        }
        
        return view('user-approvals.show', compact('approval'));
    }
    
    /**
     * Approve a user registration request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserApproval  $approval
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, UserApproval $approval)
    {
        // Kiểm tra quyền - chỉ admin hoặc content user quản lý khóa học mới có thể duyệt
        $canApprove = Auth::user()->isAdmin() || 
                     (Auth::user()->isContentUser() && $approval->course->content_user_id == Auth::id());
        
        if (!$canApprove) {
            return redirect()->route('user-approvals.index')
                ->with('error', 'Bạn không có quyền duyệt yêu cầu này.');
        }
        
        // Validate request
        $request->validate([
            'comment' => 'nullable|string',
        ]);
        
        // Cập nhật trạng thái duyệt
        $approval->update([
            'content_user_id' => Auth::id(),
            'status' => UserApproval::STATUS_APPROVED,
            'comment' => $request->comment,
            'reviewed_at' => now(),
        ]);
        
        // Đăng ký người dùng vào khóa học
        $approval->user->registeredCourses()->attach($approval->course_id, [
            'registered_at' => now()
        ]);
        
        return redirect()->route('user-approvals.index')
            ->with('success', 'Đã duyệt yêu cầu đăng ký khóa học thành công.');
    }
    
    /**
     * Reject a user registration request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserApproval  $approval
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, UserApproval $approval)
    {
        // Kiểm tra quyền - chỉ admin hoặc content user quản lý khóa học mới có thể từ chối
        $canReject = Auth::user()->isAdmin() || 
                    (Auth::user()->isContentUser() && $approval->course->content_user_id == Auth::id());
        
        if (!$canReject) {
            return redirect()->route('user-approvals.index')
                ->with('error', 'Bạn không có quyền từ chối yêu cầu này.');
        }
        
        // Validate request
        $request->validate([
            'comment' => 'required|string',
        ]);
        
        // Cập nhật trạng thái từ chối
        $approval->update([
            'content_user_id' => Auth::id(),
            'status' => UserApproval::STATUS_REJECTED,
            'comment' => $request->comment,
            'reviewed_at' => now(),
        ]);
        
        return redirect()->route('user-approvals.index')
            ->with('success', 'Đã từ chối yêu cầu đăng ký khóa học.');
    }
} 