<?php

namespace App\Http\Controllers;

use App\Models\User;
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
     * Display a listing of the users needing approval.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check if user is content user or admin
        if (!Auth::user()->isContentUser() && !Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
        }
        
        // Get all users with pending approval
        $pendingUsers = User::whereHas('userApproval', function($query) {
            $query->where('status', UserApproval::STATUS_PENDING);
        })->orWhereDoesntHave('userApproval')
          ->where('role', 'user')
          ->get();
        
        return view('user-approvals.index', compact('pendingUsers'));
    }
    
    /**
     * Approve a user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, User $user)
    {
        // Validate the request
        $request->validate([
            'comment' => 'nullable|string',
        ]);
        
        // Check if user already has an approval record
        $approval = $user->userApproval;
        
        if ($approval) {
            // Update existing approval
            $approval->update([
                'content_user_id' => Auth::id(),
                'status' => UserApproval::STATUS_APPROVED,
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        } else {
            // Create new approval
            $approval = UserApproval::create([
                'user_id' => $user->id,
                'content_user_id' => Auth::id(),
                'status' => UserApproval::STATUS_APPROVED,
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        }
        
        return redirect()->route('user-approvals.index')
            ->with('success', 'Người dùng đã được duyệt thành công.');
    }
    
    /**
     * Reject a user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, User $user)
    {
        // Validate the request
        $request->validate([
            'comment' => 'required|string',
        ]);
        
        // Check if user already has an approval record
        $approval = $user->userApproval;
        
        if ($approval) {
            // Update existing approval
            $approval->update([
                'content_user_id' => Auth::id(),
                'status' => UserApproval::STATUS_REJECTED,
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        } else {
            // Create new approval
            $approval = UserApproval::create([
                'user_id' => $user->id,
                'content_user_id' => Auth::id(),
                'status' => UserApproval::STATUS_REJECTED,
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        }
        
        return redirect()->route('user-approvals.index')
            ->with('success', 'Người dùng đã bị từ chối.');
    }
} 