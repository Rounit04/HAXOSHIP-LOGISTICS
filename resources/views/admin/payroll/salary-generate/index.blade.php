@extends('layouts.admin')

@section('title', 'Salary Generate')

@section('content')
    <style>
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            border: 1px solid rgba(255, 117, 15, 0.1);
        }
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .form-label .required {
            color: #ef4444;
            font-size: 12px;
        }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            background: white;
            transition: all 0.3s ease;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Salary Generate</h1>
                    <p class="text-xs text-gray-600">Generate salary for employees</p>
                </div>
            </div>
            <a href="{{ route('admin.payroll.list') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                View Payroll List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Salary Generate Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Manual Salary Generation
                </h2>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-200 rounded-xl">
                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.payroll.salary-generate.store') }}" id="salaryForm">
                    @csrf

                    <!-- User Selection -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Enter User <span class="required">*</span>
                        </label>
                        <input type="text" id="user_search" class="form-input" placeholder="Type or select user from the list below" autocomplete="off" oninput="filterUserList()">
                        <input type="hidden" name="user_id" id="user_id" required>
                        <div id="user_display" class="mt-2 text-sm text-gray-600 font-medium" style="display: none;">
                            <span class="text-green-600">Selected: </span><span id="selected_user_name"></span>
                        </div>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- User List -->
                        <div class="mt-3 border-2 border-gray-200 rounded-lg max-h-64 overflow-y-auto" id="user_list_container">
                            <div class="p-2 bg-gray-50 border-b border-gray-200 sticky top-0">
                                <p class="text-xs font-semibold text-gray-700">Available Users (Logged In):</p>
                            </div>
                            <div id="user_list" class="divide-y divide-gray-100">
                                @foreach($users as $user)
                                    <div class="user-item p-3 hover:bg-orange-50 cursor-pointer transition" 
                                         data-user-id="{{ $user->id }}" 
                                         data-user-name="{{ htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') }}" 
                                         data-user-email="{{ htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8') }}" 
                                         data-user-role="{{ $user->role }}">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-600">{{ $user->email }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-purple-100 text-purple-700">{{ ucfirst($user->role) }}</span>
                                                @if($user->last_login_at)
                                                    <p class="text-xs text-gray-500 mt-1">Last login: {{ \Carbon\Carbon::parse($user->last_login_at)->format('d M Y') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(count($users) == 0)
                                <div class="p-4 text-center text-gray-500 text-sm">
                                    <p>No users have logged in yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- User Type -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            User Type <span class="required">*</span>
                        </label>
                        <input type="text" name="user_type" id="user_type" class="form-input" placeholder="e.g., Merchant, Deliveryman, User" value="{{ old('user_type') }}" required>
                        @error('user_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Salary Amount -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Salary Amount <span class="required">*</span>
                        </label>
                        <input type="number" name="salary_amount" id="salary_amount" class="form-input" placeholder="e.g., 50000.00" value="{{ old('salary_amount') }}" step="0.01" min="0" required>
                        @error('salary_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Period Start and End -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Period Start <span class="required">*</span>
                            </label>
                            <input type="date" name="period_start" id="period_start" class="form-input" value="{{ old('period_start') }}" required>
                            @error('period_start')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Period End <span class="required">*</span>
                            </label>
                            <input type="date" name="period_end" id="period_end" class="form-input" value="{{ old('period_end') }}" required>
                            @error('period_end')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Generation Type -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Generation Type <span class="required">*</span>
                        </label>
                        <select name="generation_type" id="generation_type" class="form-select" required>
                            <option value="manual" {{ old('generation_type') == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="calendar" {{ old('generation_type') == 'calendar' ? 'selected' : '' }}>Calendar</option>
                        </select>
                        @error('generation_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remarks -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Remarks
                        </label>
                        <textarea name="remarks" id="remarks" class="form-textarea" rows="3" placeholder="Enter any remarks or notes...">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Generate Salary</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Auto Generate Section -->
            <div class="form-card p-6 mt-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Auto Generate Salary
                </h2>

                <form method="POST" action="{{ route('admin.payroll.salary-generate.auto') }}" id="autoSalaryForm">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                User Type <span class="required">*</span>
                            </label>
                            <input type="text" name="user_type" id="auto_user_type" class="form-input" placeholder="e.g., Merchant, Deliveryman, User" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Salary Amount <span class="required">*</span>
                            </label>
                            <input type="number" name="salary_amount" id="auto_salary_amount" class="form-input" placeholder="e.g., 50000.00" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Period Start <span class="required">*</span>
                            </label>
                            <input type="date" name="period_start" id="auto_period_start" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Period End <span class="required">*</span>
                            </label>
                            <input type="date" name="period_end" id="auto_period_end" class="form-input" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Generation Type <span class="required">*</span>
                        </label>
                        <select name="generation_type" id="auto_generation_type" class="form-select" required>
                            <option value="manual">Manual</option>
                            <option value="calendar">Calendar</option>
                        </select>
                    </div>

                    <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold w-full">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Auto Generate for All Users</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="form-card p-5 sticky top-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Salary Generation Guidelines
                </h3>
                <div class="space-y-3 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>All fields marked with <span class="text-red-600 font-semibold">*</span> are required.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Select a user who has logged into the system at least once.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Period End date must be after or equal to Period Start date.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Manual generation creates salary for a single user.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Auto generation creates salary for all users of selected type.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Calendar type generates salary based on calendar month.</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.payroll.list') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        View Payroll List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>

        // Handle user item clicks
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.user-item').forEach(item => {
                item.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    const userEmail = this.getAttribute('data-user-email');
                    const userRole = this.getAttribute('data-user-role');
                    
                    selectUser(userId, userName, userEmail, userRole);
                });
            });
        });

        function selectUser(userId, userName, userEmail, userRole) {
            document.getElementById('user_id').value = userId;
            document.getElementById('user_search').value = userName + ' (' + userEmail + ')';
            document.getElementById('selected_user_name').textContent = userName + ' (' + userEmail + ') - ' + userRole.charAt(0).toUpperCase() + userRole.slice(1);
            document.getElementById('user_display').style.display = 'block';
            // Auto-populate user type with capitalized role
            document.getElementById('user_type').value = userRole.charAt(0).toUpperCase() + userRole.slice(1);
            
            // Hide user list after selection
            document.getElementById('user_list_container').style.display = 'none';
        }

        function filterUserList() {
            const searchTerm = document.getElementById('user_search').value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');
            let hasVisibleItems = false;

            userItems.forEach(item => {
                const userName = item.getAttribute('data-user-name').toLowerCase();
                const userEmail = item.getAttribute('data-user-email').toLowerCase();
                const userRole = item.getAttribute('data-user-role').toLowerCase();
                
                if (userName.includes(searchTerm) || userEmail.includes(searchTerm) || userRole.includes(searchTerm)) {
                    item.style.display = 'block';
                    hasVisibleItems = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide container based on results
            if (hasVisibleItems || searchTerm === '') {
                document.getElementById('user_list_container').style.display = 'block';
            } else {
                document.getElementById('user_list_container').style.display = 'none';
            }

            // Clear selection if input is cleared
            if (searchTerm === '') {
                document.getElementById('user_id').value = '';
                document.getElementById('user_display').style.display = 'none';
                document.getElementById('user_type').value = '';
            }
        }

        // Show user list when clicking on input
        document.getElementById('user_search').addEventListener('focus', function() {
            document.getElementById('user_list_container').style.display = 'block';
        });

        // Hide user list when clicking outside
        document.addEventListener('click', function(event) {
            const userSearch = document.getElementById('user_search');
            const userListContainer = document.getElementById('user_list_container');
            
            if (!userSearch.contains(event.target) && !userListContainer.contains(event.target)) {
                userListContainer.style.display = 'none';
            }
        });

        // Set default dates
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            const periodStart = document.getElementById('period_start');
            const periodEnd = document.getElementById('period_end');
            const autoPeriodStart = document.getElementById('auto_period_start');
            const autoPeriodEnd = document.getElementById('auto_period_end');

            if (periodStart && !periodStart.value) {
                periodStart.value = formatDate(firstDay);
            }
            if (periodEnd && !periodEnd.value) {
                periodEnd.value = formatDate(lastDay);
            }
            if (autoPeriodStart && !autoPeriodStart.value) {
                autoPeriodStart.value = formatDate(firstDay);
            }
            if (autoPeriodEnd && !autoPeriodEnd.value) {
                autoPeriodEnd.value = formatDate(lastDay);
            }
        });
    </script>
@endsection
