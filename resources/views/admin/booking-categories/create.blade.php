@extends('layouts.admin')

@section('title', 'Create Booking Category')

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
        .form-input, .form-select {
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
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
        .radio-group {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        .radio-option:hover {
            border-color: #FF750F;
            background: #fff5ed;
        }
        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #FF750F;
        }
        .radio-option input[type="radio"]:checked + label {
            color: #FF750F;
            font-weight: 600;
        }
        .radio-option label {
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin: 0;
        }
        .radio-option input[type="radio"]:checked ~ label {
            color: #FF750F;
            font-weight: 600;
        }
        .status-toggle {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .status-switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 28px;
        }
        .status-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .status-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 28px;
        }
        .status-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .status-slider {
            background-color: #FF750F;
        }
        input:checked + .status-slider:before {
            transform: translateX(24px);
        }
        .success-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            min-width: 300px;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        .success-popup.closing {
            animation: slideOut 0.3s ease-out;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Create Booking Category</h1>
                    <p class="text-xs text-gray-600">Category Single & Bulk - Add new booking category</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Import Section -->
    <div class="form-card p-6 mb-6" style="background: linear-gradient(135deg, #fff5ed 0%, #ffe8d6 100%); border: 2px solid rgba(255, 117, 15, 0.2);">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Bulk Import Booking Categories</h2>
                    <p class="text-sm text-gray-600">Upload an Excel file to import multiple categories at once</p>
                </div>
            </div>
            <a href="{{ route('admin.booking-categories.template.download') }}" class="admin-btn-primary px-4 py-2 text-sm">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Template
            </a>
        </div>
        <form method="POST" action="{{ route('admin.booking-categories.import') }}" enctype="multipart/form-data" class="flex items-center gap-3">
            @csrf
            <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
            <button type="submit" class="admin-btn-primary px-6 py-2">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Category Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Category Information
                </h2>

                <form method="POST" action="{{ route('admin.booking-categories.store') }}">
                    @csrf

                    <!-- Name -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Name <span class="required">*</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-input" placeholder="Enter category name" required value="{{ old('name') }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type (Drop-Down) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            Type (Drop-Down) <span class="required">*</span>
                        </label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="network" {{ old('type') == 'network' ? 'selected' : '' }}>Network</option>
                            <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                            <option value="support" {{ old('type') == 'support' ? 'selected' : '' }}>Support</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requires AWB -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Requires AWB <span class="required">*</span>
                        </label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="requires_awb" id="requires_awb_yes" value="1" {{ old('requires_awb') == '1' ? 'checked' : '' }} required>
                                <label for="requires_awb_yes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="requires_awb" id="requires_awb_no" value="0" {{ old('requires_awb') == '0' || old('requires_awb') === null ? 'checked' : '' }} required>
                                <label for="requires_awb_no">No</label>
                            </div>
                        </div>
                        @error('requires_awb')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status (Active/In-active) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status (Active/In-active) <span class="required">*</span>
                        </label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Active" {{ old('status') == 'Active' || old('status') === null ? 'selected' : '' }}>Active</option>
                            <option value="In-active" {{ old('status') == 'In-active' ? 'selected' : '' }}>In-active</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Create Category</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.booking-categories.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
                </form>


                @if(session('error'))
                    <div class="mt-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 rounded-xl flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <p class="text-red-700 font-bold text-sm">{{ session('error') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="form-card p-5 sticky top-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Category Guidelines
                </h3>
                <div class="space-y-3 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Category Name must be unique and descriptive</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Choose Single for individual bookings or Bulk for multiple items</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Requires AWB determines if Air Waybill is mandatory</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Status can be changed later from All Categories</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.booking-categories.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        View All Categories
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Popup -->
    @if(session('success'))
        <div id="success-popup" class="success-popup">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
            </div>
            <button onclick="closePopup()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <script>
            function closePopup() {
                const popup = document.getElementById('success-popup');
                popup.classList.add('closing');
                setTimeout(() => {
                    popup.remove();
                }, 300);
            }
            // Auto close after 5 seconds
            setTimeout(() => {
                closePopup();
            }, 5000);
        </script>
    @endif
@endsection



