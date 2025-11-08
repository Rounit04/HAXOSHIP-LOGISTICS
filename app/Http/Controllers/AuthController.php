<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Create notification for user login (only if enabled)
            if (NotificationSetting::isEnabled('user_login')) {
                $user = Auth::user();
                Notification::create([
                    'type' => 'user_login',
                    'title' => 'User Login',
                    'message' => $user->name . ' logged in to the system',
                    'data' => [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                    ],
                ]);
            }
            
            return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials. Please try again.',
        ])->withInput($request->only('email'));
    }

    /**
     * Show register form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        // Create notification for new user registration (only if enabled)
        if (NotificationSetting::isEnabled('user_login')) {
            Notification::create([
                'type' => 'user_login',
                'title' => 'New User Registration',
                'message' => 'New user ' . $user->name . ' registered and logged in',
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ],
            ]);
        }

        return redirect('/dashboard')->with('success', 'Account created successfully!');
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')
                ->scopes(['profile', 'email'])
                ->redirect();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google authentication is not configured. Please contact administrator.');
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            // Check if Google credentials are configured
            if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
                return redirect('/login')->with('error', 'Google authentication is not configured. Please contact administrator.');
            }

            $googleUser = Socialite::driver('google')->user();
            
            if (!$googleUser || !$googleUser->getEmail()) {
                return redirect('/login')->with('error', 'Unable to retrieve Google account information.');
            }

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getEmail(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)), // Random password for OAuth users
                    'email_verified_at' => now(),
                ]);
                
                // Create notification for new user registration via Google (only if enabled)
                if (NotificationSetting::isEnabled('user_login')) {
                    Notification::create([
                        'type' => 'user_login',
                        'title' => 'New User Registration (Google)',
                        'message' => 'New user ' . $user->name . ' registered via Google OAuth',
                        'data' => [
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                            'user_email' => $user->email,
                        ],
                    ]);
                }
            } else {
                // Create notification for user login via Google (only if enabled)
                if (NotificationSetting::isEnabled('user_login')) {
                    Notification::create([
                        'type' => 'user_login',
                        'title' => 'User Login (Google)',
                        'message' => $user->name . ' logged in via Google OAuth',
                        'data' => [
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                            'user_email' => $user->email,
                        ],
                    ]);
                }
            }

            Auth::login($user, true);

            return redirect('/dashboard')->with('success', 'Logged in successfully with Google!');
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Unable to login with Google. Please check your Google OAuth configuration.');
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
