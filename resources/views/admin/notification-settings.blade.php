@extends('layouts.admin')

@section('title', 'Notification Settings')

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
            transform: translateY(-2px);
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
        .toggle-item-desc {
            font-size: 12px;
            color: #6b7280;
        }
        .icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(255, 117, 15, 0.15), rgba(255, 117, 15, 0.08));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .notification-type-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .notification-type-card:hover {
            border-color: #FF750F;
            box-shadow: 0 4px 12px rgba(255, 117, 15, 0.1);
        }
        .notification-type-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .notification-type-title {
            font-size: 16px;
            font-weight: 700;
            color: #374151;
        }
        .notification-type-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Notification Settings</h1>
                    <p class="text-xs text-gray-600">Configure notification preferences and behavior</p>
                </div>
            </div>
            <a href="{{ route('admin.settings') }}" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                ← Back to Settings
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.notification-settings.update') }}">
        @csrf
        
        <!-- Global Settings -->
        <div class="settings-section">
            <div class="settings-section-header">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                    <div class="icon-wrapper">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span>Global Settings</span>
                </h3>
            </div>
            <div class="settings-section-body">
                <div class="form-group">
                    <label class="form-label">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Polling Interval (seconds)
                    </label>
                    <input type="number" name="polling_interval" value="{{ $settings->first()->polling_interval ?? 30 }}" min="5" max="300" class="form-input" placeholder="30">
                    <p class="text-xs text-gray-500 mt-1">How often to check for new notifications (5-300 seconds)</p>
                </div>
            </div>
        </div>

        <!-- Notification Types -->
        <div class="settings-section">
            <div class="settings-section-header">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                    <div class="icon-wrapper">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <span>Notification Types</span>
                </h3>
            </div>
            <div class="settings-section-body">
                @foreach($settings as $setting)
                    <div class="notification-type-card">
                        <div class="notification-type-header">
                            <div>
                                <h4 class="notification-type-title">{{ $setting->title }}</h4>
                                <p class="text-xs text-gray-600 mt-1">{{ $setting->description }}</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="settings[{{ $setting->key }}][enabled]" value="1" {{ $setting->enabled ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="notification-type-body">
                            <div class="toggle-item">
                                <div class="toggle-item-content">
                                    <div class="toggle-item-title">Show in Dropdown</div>
                                    <div class="toggle-item-desc">Display in notification dropdown</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="settings[{{ $setting->key }}][show_dropdown]" value="1" {{ $setting->show_dropdown ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="toggle-item">
                                <div class="toggle-item-content">
                                    <div class="toggle-item-title">Show Popup</div>
                                    <div class="toggle-item-desc">Display popup notification</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="settings[{{ $setting->key }}][show_popup]" value="1" {{ $setting->show_popup ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="toggle-item">
                                <div class="toggle-item-content">
                                    <div class="toggle-item-title">Polling Interval</div>
                                    <div class="toggle-item-desc">Check interval (seconds)</div>
                                </div>
                                <input type="number" name="settings[{{ $setting->key }}][polling_interval]" value="{{ $setting->polling_interval }}" min="5" max="300" class="form-input w-24">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.settings') }}" class="px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Cancel
            </a>
            <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Save Settings</span>
                </div>
            </button>
        </div>
    </form>
@endsection

