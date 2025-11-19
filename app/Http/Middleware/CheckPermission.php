<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        // Check if user is logged in
        if (!session()->has('admin_logged_in') || !session('admin_user_id')) {
            return redirect()->route('admin.login')->with('error', 'Please login to access this page.');
        }

        // Get the current user
        $user = User::find(session('admin_user_id'));
        
        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'User not found.');
        }

        // Super admin has all permissions
        if ($user->is_admin) {
            return $next($request);
        }

        // Check if user has the required permission
        if (!$user->hasPermission($permission)) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
