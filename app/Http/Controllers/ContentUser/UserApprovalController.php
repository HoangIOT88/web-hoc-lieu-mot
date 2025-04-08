<?php

namespace App\Http\Controllers\ContentUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserApproval;
use App\Models\User;
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
        $this->middleware('content.user');
    }
    
    /**
     * Display a listing of pending user approval requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pendingApprovals = UserApproval::where('status', 'PENDING')
            ->with('user')
            ->paginate(10);
            
        return view('content_user.approvals.index', compact('pendingApprovals'));
    }
    
    /**
     * Display a listing of approved and rejected user approval requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        $approvalHistory = UserApproval::where('content_user_id', Auth::id())
            ->whereIn('status', ['APPROVED', 'REJECTED'])
            ->with('user')
            ->latest('reviewed_at')
            ->paginate(10);
            
        return view('content_user.approvals.history', compact('approvalHistory'));
    }
    
    /**
     * Show the specified user approval request.
     *
     * @param  \App\Models\UserApproval  $approval
     * @return \Illuminate\Http\Response
     */
    public function show(UserApproval $approval)
    {
        if ($approval->status !== 'PENDING' && $approval->content_user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $user = $approval->user;
        
        return view('content_user.approvals.show', compact('approval', 'user'));
    }
    
    /**
     * Approve a user approval request.
     *
     * @param  \App\Models\UserApproval  $approval
     * @return \Illuminate\Http\Response
     */
    public function approve(UserApproval $approval)
    {
        if ($approval->status !== 'PENDING') {
            return redirect()->route('content.approvals.index')
                ->with('error', 'This request has already been processed.');
        }
        
        $approval->update([
            'status' => 'APPROVED',
            'content_user_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);
        
        return redirect()->route('content.approvals.index')
            ->with('success', 'User has been approved successfully.');
    }
    
    /**
     * Reject a user approval request.
     *
     * @param  \App\Models\UserApproval  $approval
     * @return \Illuminate\Http\Response
     */
    public function reject(UserApproval $approval)
    {
        if ($approval->status !== 'PENDING') {
            return redirect()->route('content.approvals.index')
                ->with('error', 'This request has already been processed.');
        }
        
        $approval->update([
            'status' => 'REJECTED',
            'content_user_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);
        
        return redirect()->route('content.approvals.index')
            ->with('success', 'User request has been rejected.');
    }
}
