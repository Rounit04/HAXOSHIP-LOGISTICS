<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Haxo Shipping</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            overflow: hidden;
        }
        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }
        .left-panel {
            background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .right-panel {
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(255, 117, 15, 0.15);
            overflow: hidden;
        }
        .login-btn {
            background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
            transition: all 0.3s ease;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 117, 15, 0.35);
        }
        .google-btn {
            background: white;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .google-btn:hover {
            border-color: #FF750F;
            box-shadow: 0 4px 12px rgba(255, 117, 15, 0.15);
        }
        .input-field:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
        }
        .pattern-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.05;
            background-image: 
                radial-gradient(circle at 20% 50%, white 1px, transparent 1px),
                radial-gradient(circle at 80% 80%, white 1px, transparent 1px),
                radial-gradient(circle at 40% 20%, white 1px, transparent 1px);
            background-size: 50px 50px, 80px 80px, 60px 60px;
        }
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }
            .left-panel {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel - Branding -->
        <div class="left-panel relative flex items-center justify-center text-white">
            <div class="pattern-bg"></div>
            <div class="relative z-10 text-center px-12">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full logo-circle mb-8" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
                    <span class="text-5xl font-bold text-white">H</span>
                </div>
                <h1 class="text-5xl font-bold mb-4">HaxoShipping</h1>
                <p class="text-xl text-orange-100 mb-8">Professional Shipping Solutions</p>
                <div class="space-y-4 text-left max-w-md mx-auto">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-filter blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Secure Access</h3>
                            <p class="text-orange-100 text-sm">Enterprise-grade security</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-filter blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Fast & Reliable</h3>
                            <p class="text-orange-100 text-sm">Quick access to your account</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-filter blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Privacy Protected</h3>
                            <p class="text-orange-100 text-sm">Your data is safe with us</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="right-panel">
            <div class="login-card">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-400 p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full logo-circle mb-4" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
                        <span class="text-3xl font-bold text-white">H</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Welcome Back</h2>
                    <p class="text-orange-100 text-sm">Sign in to your account</p>
                </div>

                <!-- Form Content -->
                <div class="p-8">
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Google Login Button -->
                    <a href="{{ route('auth.google') }}" class="google-btn w-full py-3 px-4 rounded-lg flex items-center justify-center gap-3 mb-4 font-semibold text-gray-700" onclick="return checkGoogleConfig(event)">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Continue with Google</span>
                    </a>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with email</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                        @csrf

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required 
                                autofocus
                                class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 transition-colors"
                                placeholder="Enter your email address"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 transition-colors"
                                placeholder="Enter your password"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                            <a href="#" class="text-sm text-orange-600 hover:text-orange-700 font-medium transition-colors">
                                Forgot Password?
                            </a>
</div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="login-btn w-full py-3.5 text-white font-semibold rounded-lg text-base"
                        >
                            Sign In
                        </button>
                    </form>

                    <!-- Register Link -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-orange-600 hover:text-orange-700 font-semibold transition-colors">
                                Sign up
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-center">
        <p class="text-sm text-gray-500">
            © {{ date('Y') }} HaxoShipping. All rights reserved.
        </p>
    </div>

    <script>
        function checkGoogleConfig(event) {
            // Check if Google OAuth is configured
            // If not configured, show a helpful message
            const hasError = {{ session('error') ? 'true' : 'false' }};
            if (hasError && '{{ session('error') }}'.includes('not configured')) {
                event.preventDefault();
                alert('Google authentication is not configured. Please use email/password login or contact the administrator to set up Google OAuth.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>