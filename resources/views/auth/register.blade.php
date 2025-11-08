<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Haxo Shipping</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            overflow: hidden;
        }
        .register-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            height: 100vh;
            overflow: hidden;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        .left-panel {
            background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
            position: relative;
            overflow: hidden;
            height: 100vh;
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
            padding: 1rem;
            overflow: hidden;
            height: 100vh;
        }
        .register-card {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(255, 117, 15, 0.15);
            overflow: hidden;
            max-height: 95vh;
            display: flex;
            flex-direction: column;
        }
        .step-container {
            display: none;
            flex: 1;
            overflow-y: auto;
        }
        .step-container.active {
            display: block;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 1rem;
            background: #f9fafb;
        }
        .step-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #e5e7eb;
            transition: all 0.3s ease;
        }
        .step-dot.active {
            background: #FF750F;
            width: 30px;
            border-radius: 5px;
        }
        .register-btn {
            background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
            transition: all 0.3s ease;
        }
        .register-btn:hover {
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
            .register-container {
                grid-template-columns: 1fr;
            }
            .left-panel {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Left Panel - Branding -->
        <div class="left-panel relative flex items-center justify-center text-white">
            <div class="pattern-bg"></div>
            <div class="relative z-10 text-center px-12">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full logo-circle mb-8" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
                    <span class="text-5xl font-bold text-white">H</span>
                </div>
                <h1 class="text-5xl font-bold mb-4">Join HaxoShipping</h1>
                <p class="text-xl text-orange-100 mb-8">Start your shipping journey today</p>
                <div class="space-y-4 text-left max-w-md mx-auto">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-filter blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Easy Registration</h3>
                            <p class="text-orange-100 text-sm">Create your account in seconds</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-filter blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Verified Accounts</h3>
                            <p class="text-orange-100 text-sm">Secure and verified profiles</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-filter blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Quick Setup</h3>
                            <p class="text-orange-100 text-sm">Get started immediately</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Register Form -->
        <div class="right-panel">
            <div class="register-card">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-400 p-4 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full logo-circle mb-2" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
                        <span class="text-2xl font-bold text-white">H</span>
                    </div>
                    <h2 class="text-xl font-bold text-white mb-1">Create Account</h2>
                    <p class="text-orange-100 text-xs">Join us and start shipping</p>
                </div>

                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step-dot active" data-step="1"></div>
                    <div class="step-dot" data-step="2"></div>
                    <div class="step-dot" data-step="3"></div>
                </div>

                <!-- Form Content -->
                <div style="flex: 1; overflow-y: auto; padding: 1.5rem;">
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

                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf

                        <!-- Step 1: Personal Information -->
                        <div class="step-container active" id="step1">
                            <div class="text-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Personal Information</h3>
                                <p class="text-sm text-gray-600">Tell us about yourself</p>
                            </div>

                            <div class="space-y-4">
                                <!-- Name Field -->
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Full Name
                                    </label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        value="{{ old('name') }}"
                                        required 
                                        autofocus
                                        class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 transition-colors"
                                        placeholder="Enter your full name"
                                    >
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button 
                                    type="button" 
                                    onclick="nextStep(2)"
                                    class="register-btn px-8 py-3 text-white font-semibold rounded-lg text-base"
                                >
                                    Next →
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Email -->
                        <div class="step-container" id="step2">
                            <div class="text-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Email Address</h3>
                                <p class="text-sm text-gray-600">We'll use this to contact you</p>
                            </div>

                            <div class="space-y-4">
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
                                        class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 transition-colors"
                                        placeholder="Enter your email address"
                                    >
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6 flex justify-between">
                                <button 
                                    type="button" 
                                    onclick="nextStep(1)"
                                    class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg text-base hover:bg-gray-50 transition-colors"
                                >
                                    ← Previous
                                </button>
                                <button 
                                    type="button" 
                                    onclick="nextStep(3)"
                                    class="register-btn px-8 py-3 text-white font-semibold rounded-lg text-base"
                                >
                                    Next →
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Password -->
                        <div class="step-container" id="step3">
                            <div class="text-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Create Password</h3>
                                <p class="text-sm text-gray-600">Choose a strong password</p>
                            </div>

                            <div class="space-y-4">
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

                                <!-- Confirm Password Field -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Confirm Password
                                    </label>
                                    <input 
                                        type="password" 
                                        id="password_confirmation" 
                                        name="password_confirmation" 
                                        required
                                        class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 transition-colors"
                                        placeholder="Confirm your password"
                                    >
                                </div>
                            </div>

                            <div class="mt-6 flex justify-between">
                                <button 
                                    type="button" 
                                    onclick="nextStep(2)"
                                    class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg text-base hover:bg-gray-50 transition-colors"
                                >
                                    ← Previous
                                </button>
                                <button 
                                    type="submit" 
                                    class="register-btn px-8 py-3 text-white font-semibold rounded-lg text-base"
                                >
                                    Create Account
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Login Link -->
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-orange-600 hover:text-orange-700 font-semibold transition-colors">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        let currentStep = 1;
        const totalSteps = 3;

        function nextStep(step) {
            // Validate current step before moving
            if (step > currentStep) {
                if (!validateStep(currentStep)) {
                    return;
                }
            }

            // Hide current step
            document.getElementById('step' + currentStep).classList.remove('active');
            document.querySelector(`.step-dot[data-step="${currentStep}"]`).classList.remove('active');

            // Show next step
            currentStep = step;
            document.getElementById('step' + currentStep).classList.add('active');
            document.querySelector(`.step-dot[data-step="${currentStep}"]`).classList.add('active');
        }

        function validateStep(step) {
            if (step === 1) {
                const name = document.getElementById('name').value.trim();
                if (!name) {
                    alert('Please enter your full name');
                    document.getElementById('name').focus();
                    return false;
                }
            } else if (step === 2) {
                const email = document.getElementById('email').value.trim();
                if (!email) {
                    alert('Please enter your email address');
                    document.getElementById('email').focus();
                    return false;
                }
                // Basic email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Please enter a valid email address');
                    document.getElementById('email').focus();
                    return false;
                }
            }
            return true;
        }

        // Prevent form submission until step 3
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            if (currentStep !== 3) {
                e.preventDefault();
                nextStep(3);
            }
        });
    </script>
</body>
</html>