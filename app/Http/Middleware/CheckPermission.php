<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $staff = Auth::guard('staff')->user();

        if (!$staff || !$staff->hasPermission($permission)) {
            abort(403, 'Unauthorized. You do not have permission to access this page.');
        }

        return $next($request);
    }
}