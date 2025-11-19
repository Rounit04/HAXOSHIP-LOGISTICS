<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationSetting;

class AdminAuthController extends Controller
{
    /**
     * Entry point for /admin route; redirect based on authentication status.
     */
    public function entry()
    {
        if (session()->has('admin_logged_in') && session('admin_logged_in') === true) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.login');
    }

    /**
     * Show the admin login form
     */
    public function showLoginForm()
    {
        // If already logged in as admin, redirect to dashboard
        if (session()->has('admin_logged_in') && session('admin_logged_in') === true) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // For demo purposes, we'll use email/password
        // You can change this to use ID if needed
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // Try to find user by email
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Check if user has permission to access admin panel (super admin or has view_dashboard permission)
            if (!$user->is_admin && !$user->hasPermission('view_dashboard')) {
                return back()->withErrors([
                    'email' => 'You do not have permission to access the admin panel.',
                ])->withInput($request->only('email'));
            }
            
            // Set admin session
            session([
                'admin_logged_in' => true,
                'admin_user_id' => $user->id,
                'admin_user_name' => $user->name,
                'admin_user_email' => $user->email,
            ]);
            session()->save();

            // Create notification for admin login (only if enabled)
            if (NotificationSetting::isEnabled('user_login')) {
                Notification::create([
                    'type' => 'user_login',
                    'title' => 'Admin Login',
                    'message' => $user->name . ' (Admin) logged in to the admin panel',
                    'data' => [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                    ],
                ]);
            }

            return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials. Please try again.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        session()->forget(['admin_logged_in', 'admin_user_id', 'admin_user_name', 'admin_user_email']);
        session()->save();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }
}
