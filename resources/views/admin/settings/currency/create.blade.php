@extends('layouts.admin')

@section('title', 'Create Currency')

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
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
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
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Create Currency</h1>
                    <p class="text-xs text-gray-600">Add a new currency</p>
                </div>
            </div>
            <a href="{{ route('admin.currency.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                Back to List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card p-6">
        <form method="POST" action="{{ route('admin.currency.store') }}">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label class="form-label">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Currency Name <span class="required">*</span>
                </label>
                <input type="text" name="name" id="name" class="form-input" placeholder="e.g., US Dollar, Euro" required>
            </div>

            <!-- Code & Symbol -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        Currency Code <span class="required">*</span>
                    </label>
                    <input type="text" name="code" id="code" class="form-input" placeholder="USD" maxlength="3" style="text-transform: uppercase;" required>
                    <p class="text-xs text-gray-500 mt-1">3-letter ISO code (e.g., USD, EUR, GBP)</p>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Symbol <span class="required">*</span>
                    </label>
                    <input type="text" name="symbol" id="symbol" class="form-input" placeholder="$" maxlength="10" required>
                    <p class="text-xs text-gray-500 mt-1">Currency symbol (e.g., $, €, £)</p>
                </div>
            </div>

            <!-- Exchange Rate -->
            <div class="form-group">
                <label class="form-label">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Exchange Rate <span class="required">*</span>
                </label>
                <input type="number" name="exchange_rate" id="exchange_rate" class="form-input" step="0.0001" min="0" placeholder="1.0000" required>
                <p class="text-xs text-gray-500 mt-1">Exchange rate relative to base currency (1.0000 = base currency)</p>
            </div>

            <!-- Status & Default -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Status <span class="required">*</span>
                    </label>
                    <div class="status-toggle">
                        <label class="status-switch">
                            <input type="checkbox" name="status" id="status" checked>
                            <span class="status-slider"></span>
                        </label>
                        <span class="text-sm font-medium text-gray-700" id="statusLabel">Active</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        Set as Default
                    </label>
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_default" id="is_default" value="1">
                        <label for="is_default" class="text-sm font-medium text-gray-700 cursor-pointer">Make this the default currency</label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">If checked, this will become the default currency</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3">
                <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Create Currency</span>
                    </div>
                </button>
                <a href="{{ route('admin.currency.index') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('status').addEventListener('change', function() {
            document.getElementById('statusLabel').textContent = this.checked ? 'Active' : 'Inactive';
        });
        
        // Auto-uppercase currency code
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endsection

