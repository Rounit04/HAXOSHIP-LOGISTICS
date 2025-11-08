@extends('layouts.admin')

@section('title', 'SMS Settings')

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
        .settings-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .settings-section-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
            border-bottom: 2px solid #e5e7eb;
        }
        .settings-section-body {
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
        .form-input, .form-select {
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
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 56px;
            height: 30px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 30px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        input:checked + .toggle-slider {
            background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
        }
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        .toggle-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: linear-gradient(135deg, #fff5ed 0%, #fff5ed 100%);
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-bottom: 12px;
        }
        .toggle-item:hover {
            border-color: #FF750F;
            box-shadow: 0 4px 12px rgba(255, 117, 15, 0.1);
        }
        .toggle-item-content {
            flex: 1;
        }
        .toggle-item-title {
            font-size: 14px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 4px;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">SMS Settings</h1>
                    <p class="text-xs text-gray-600">Configure SMS provider settings</p>
                </div>
            </div>
            <a href="{{ route('admin.settings') }}" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                ← Back to Settings
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

    <form method="POST" action="{{ route('admin.sms-settings.update') }}">
        @csrf
        
        <div class="settings-section">
            <div class="settings-section-header">
                <h3 class="text-lg font-bold text-gray-900">SMS Provider Configuration</h3>
            </div>
            <div class="settings-section-body">
                <div class="form-group">
                    <label class="form-label">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Provider <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="provider" value="{{ $settings->provider ?? '' }}" class="form-input" placeholder="e.g., Twilio, Nexmo" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        API Key
                    </label>
                    <input type="text" name="api_key" value="{{ $settings->api_key ?? '' }}" class="form-input" placeholder="Enter API Key">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        API Secret
                    </label>
                    <input type="password" name="api_secret" value="{{ $settings->api_secret ?? '' }}" class="form-input" placeholder="Enter API Secret">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Sender ID
                    </label>
                    <input type="text" name="sender_id" value="{{ $settings->sender_id ?? '' }}" class="form-input" placeholder="Enter Sender ID">
                </div>

                <div class="toggle-item">
                    <div class="toggle-item-content">
                        <div class="toggle-item-title">Enable SMS</div>
                        <p class="text-xs text-gray-600">Enable or disable SMS functionality</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="enabled" value="1" {{ ($settings->enabled ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Save Settings</span>
                </div>
            </button>
            <a href="{{ route('admin.settings') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
                Cancel
            </a>
        </div>
    </form>
@endsection
