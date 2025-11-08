<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - HaxoShipping</title>
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
        .input-field:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
        }
        .logo-circle {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full logo-circle mb-8">
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
                            <p class="text-orange-100 text-sm">Enterprise-grade security for your admin panel</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-filter blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Manage Everything</h3>
                            <p class="text-orange-100 text-sm">Complete control over your shipping operations</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-filter blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Real-time Analytics</h3>
                            <p class="text-orange-100 text-sm">Track and monitor your shipping data instantly</p>
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
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full logo-circle mb-4">
                        <span class="text-3xl font-bold text-white">H</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Welcome Back</h2>
                    <p class="text-orange-100 text-sm">Sign in to access your admin panel</p>
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

                    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-6">
                        @csrf

                        <!-- Email/ID Field -->
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
                            Sign In to Admin Panel
                        </button>
                    </form>

                    <!-- Demo Credentials Note -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-600 text-center leading-relaxed">
                            <strong class="text-gray-700">Note:</strong> Use your registered email and password to login.
                            <br>If you don't have an account, please contact the system administrator.
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
</body>
</html>