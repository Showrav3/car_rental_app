<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'admin')
        {
            return redirect()->route('show.admin.login');
        }
        
        return $next($request);
    }
}
