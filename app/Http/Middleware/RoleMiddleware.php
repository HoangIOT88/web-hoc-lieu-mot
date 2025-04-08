<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            // Check if user has the role
            $checkMethod = 'is' . ucfirst($role);
            
            // Handle 'content_user' special case
            if ($role === 'content_user' && method_exists($request->user(), 'isContentUser')) {
                if ($request->user()->isContentUser()) {
                    return $next($request);
                }
                continue;
            }
            
            // Handle normal case
            if (method_exists($request->user(), $checkMethod)) {
                if ($request->user()->{$checkMethod}()) {
                    return $next($request);
                }
            }
        }

        return redirect()->route('home')
            ->with('error', 'Bạn không có quyền truy cập trang này.');
    }
} 