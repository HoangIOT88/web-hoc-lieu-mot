<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegularUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isUser()) {
            return $next($request);
        }
        
        if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isContentUser())) {
            return redirect()->route('dashboard')->with('error', 'Trang này chỉ dành cho người dùng thông thường.');
        }
        
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để truy cập trang này.');
    }
}
