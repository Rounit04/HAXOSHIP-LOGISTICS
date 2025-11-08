@extends('layouts.admin')

@section('title', 'Salary Generate')

@section('content')
    <style>
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            border: 1px solid rgba(255, 117, 15, 0.1);
        }
        .form-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .form-section-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
            border-bottom: 2px solid #e5e7eb;
        }
        .form-section-body {
            padding: 24px;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
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
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        .tab-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .tab-button {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background: white;
            font-weight: 600;
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .tab-button.active {
            background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
            color: white;
            border-color: #FF750F;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Salary Generate</h1>
                    <p class="text-xs text-gray-600">Generate salary for merchants, deliverymen, or users</p>
                </div>
            </div>
            <a href="{{ route('admin.payroll.list') }}" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                View Payroll List
            </a>
        </div>
    </div>

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

    <!-- Tabs -->
    <div class="tab-buttons">
        <button class="tab-button active" onclick="switchTab('manual')">Manual Generate</button>
        <button class="tab-button" onclick="switchTab('auto')">Auto Generate (Calendar)</button>
    </div>

    <!-- Manual Generate Tab -->
    <div id="manual-tab" class="tab-content active">
        <form method="POST" action="{{ route('admin.payroll.salary-generate.store') }}">
            @csrf
            <div class="form-section">
                <div class="form-section-header">
                    <h3 class="text-lg font-bold text-gray-900">Manual Salary Generation</h3>
                </div>
                <div class="form-section-body">
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Select User <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->role ?? 'user') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            User Type <span class="text-red-500">*</span>
                        </label>
                        <select name="user_type" class="form-select" required>
                            <option value="merchant">Merchant</option>
                            <option value="deliveryman">Deliveryman</option>
                            <option value="user">User</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Salary Amount <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="salary_amount" step="0.01" min="0" class="form-input" placeholder="Enter salary amount" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Period Start <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="period_start" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Period End <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="period_end" class="form-input" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Generation Type <span class="text-red-500">*</span>
                        </label>
                        <select name="generation_type" class="form-select" required>
                            <option value="manual">Manual</option>
                            <option value="calendar">Calendar</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Remarks
                        </label>
                        <textarea name="remarks" class="form-textarea" placeholder="Enter any remarks or notes"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Generate Salary</span>
                    </div>
                </button>
            </div>
        </form>
    </div>

    <!-- Auto Generate Tab -->
    <div id="auto-tab" class="tab-content">
        <form method="POST" action="{{ route('admin.payroll.salary-generate.auto') }}">
            @csrf
            <div class="form-section">
                <div class="form-section-header">
                    <h3 class="text-lg font-bold text-gray-900">Automatic Salary Generation</h3>
                    <p class="text-sm text-gray-600 mt-1">Generate salary for all users of selected type automatically</p>
                </div>
                <div class="form-section-body">
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            User Type <span class="text-red-500">*</span>
                        </label>
                        <select name="user_type" class="form-select" required>
                            <option value="merchant">Merchant</option>
                            <option value="deliveryman">Deliveryman</option>
                            <option value="user">User</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Salary Amount <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="salary_amount" step="0.01" min="0" class="form-input" placeholder="Enter salary amount for all users" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Period Start <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="period_start" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Period End <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="period_end" class="form-input" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Generation Type <span class="text-red-500">*</span>
                        </label>
                        <select name="generation_type" class="form-select" required>
                            <option value="manual">Manual</option>
                            <option value="calendar" selected>Calendar</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Generate Salary Automatically</span>
                    </div>
                </button>
            </div>
        </form>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tab + '-tab').classList.add('active');
            event.target.classList.add('active');
        }
    </script>
@endsection



