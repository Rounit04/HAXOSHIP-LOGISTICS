@extends('layouts.admin')

@section('title', 'Settings')

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
            background: linear-gradient(135deg, #FF750F 0%, #5a52ff 100%);
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
        .color-picker-wrapper {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .color-picker {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            border: 3px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .color-picker:hover {
            border-color: #FF750F;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(255, 117, 15, 0.2);
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
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Settings</h1>
                    <p class="text-xs text-gray-600">Manage your application settings and preferences</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Settings Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- General Settings -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                        <div class="icon-wrapper">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                        </div>
                        <span>General Settings</span>
                    </h3>
                </div>
                <div class="settings-section-body">
                    <form class="space-y-6">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Site Name
                            </label>
                            <input type="text" value="{{ config('app.name') }}" class="form-input" placeholder="Enter site name">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Site Email
                            </label>
                            <input type="email" value="admin@haxoshipping.com" class="form-input" placeholder="Enter site email">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                Site Description
                            </label>
                            <textarea rows="4" class="form-textarea resize-none" placeholder="Enter site description"></textarea>
                        </div>

                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Save Changes</span>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                        <div class="icon-wrapper">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <span>Security Settings</span>
                    </h3>
                </div>
                <div class="settings-section-body">
                    <div class="space-y-4">
                        <div class="toggle-item">
                            <div class="toggle-item-content">
                                <div class="toggle-item-title">Enable Two-Factor Authentication</div>
                                <div class="toggle-item-desc">Add an extra layer of security to your account</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="toggle-item">
                            <div class="toggle-item-content">
                                <div class="toggle-item-title">Require Strong Passwords</div>
                                <div class="toggle-item-desc">Force users to use complex passwords</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="toggle-item">
                            <div class="toggle-item-content">
                                <div class="toggle-item-title">Session Timeout</div>
                                <div class="toggle-item-desc">Automatically log out after inactivity</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="toggle-item">
                            <div class="toggle-item-content">
                                <div class="toggle-item-title">IP Whitelist</div>
                                <div class="toggle-item-desc">Restrict access to specific IP addresses</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                        <div class="icon-wrapper">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <span>Notification Settings</span>
                    </h3>
                </div>
                <div class="settings-section-body">
                    <p class="text-sm text-gray-600 mb-4">Manage notification preferences and behavior for the admin panel.</p>
                    <a href="{{ route('admin.notification-settings') }}" class="admin-btn-primary px-6 py-3 text-sm font-semibold inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Manage Notification Settings</span>
                    </a>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                        <div class="icon-wrapper">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a4 4 0 004-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4z"/>
                            </svg>
                        </div>
                        <span>Appearance Settings</span>
                    </h3>
                </div>
                <div class="settings-section-body">
                    <form class="space-y-6">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a4 4 0 004-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4z"/>
                                </svg>
                                Primary Color
                            </label>
                            <div class="color-picker-wrapper">
                                <input type="color" value="#FF750F" class="color-picker">
                                <div class="flex-1">
                                    <input type="text" value="#FF750F" class="form-input" placeholder="#FF750F">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                Theme
                            </label>
                            <select class="form-select">
                                <option>Light</option>
                                <option>Dark</option>
                                <option>Auto (System Default)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Logo
                            </label>
                            <div class="flex items-center gap-4">
                                <div class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                </div>
                                <button type="button" class="px-5 py-2.5 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                                    Upload Logo
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Save Changes</span>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- GDPR Cookie Settings -->
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                        <div class="icon-wrapper">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </div>
                        <span>GDPR Cookie Settings</span>
                    </h3>
                </div>
                <div class="settings-section-body">
                    @php
                        $settings = \App\Models\FrontendSetting::getSettings();
                    @endphp
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    <form id="gdprCookieForm" action="{{ route('admin.settings.update-gdpr-cookie') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="toggle-item">
                            <div class="toggle-item-content">
                                <div class="toggle-item-title">Enable GDPR Cookie Banner</div>
                                <div class="toggle-item-desc">Display a GDPR-compliant cookie consent banner to visitors</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="gdpr_cookie_enabled" value="1" {{ old('gdpr_cookie_enabled', $settings->gdpr_cookie_enabled ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                Cookie Message
                            </label>
                            <textarea name="gdpr_cookie_message" rows="4" class="form-textarea resize-none" placeholder="We use cookies to enhance your browsing experience...">{{ old('gdpr_cookie_message', $settings->gdpr_cookie_message ?? 'We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Accept Button Text
                                </label>
                                <input type="text" name="gdpr_cookie_button_text" value="{{ old('gdpr_cookie_button_text', $settings->gdpr_cookie_button_text ?? 'Accept All') }}" class="form-input" placeholder="Accept All">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Decline Button Text
                                </label>
                                <input type="text" name="gdpr_cookie_decline_text" value="{{ old('gdpr_cookie_decline_text', $settings->gdpr_cookie_decline_text ?? 'Decline') }}" class="form-input" placeholder="Decline">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Settings Button Text
                                </label>
                                <input type="text" name="gdpr_cookie_settings_text" value="{{ old('gdpr_cookie_settings_text', $settings->gdpr_cookie_settings_text ?? 'Settings') }}" class="form-input" placeholder="Settings">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                    </svg>
                                    Banner Position
                                </label>
                                <select name="gdpr_cookie_position" class="form-select">
                                    <option value="bottom" {{ old('gdpr_cookie_position', $settings->gdpr_cookie_position ?? 'bottom') === 'bottom' ? 'selected' : '' }}>Bottom</option>
                                    <option value="top" {{ old('gdpr_cookie_position', $settings->gdpr_cookie_position ?? 'bottom') === 'top' ? 'selected' : '' }}>Top</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Cookie Expiry (Days)
                                </label>
                                <input type="number" name="gdpr_cookie_expiry_days" value="{{ old('gdpr_cookie_expiry_days', $settings->gdpr_cookie_expiry_days ?? 365) }}" class="form-input" placeholder="365" min="1" max="3650">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a4 4 0 004-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4z"/>
                                    </svg>
                                    Background Color
                                </label>
                                <div class="color-picker-wrapper">
                                    <input type="color" id="gdpr_cookie_bg_color_picker" value="{{ old('gdpr_cookie_bg_color', $settings->gdpr_cookie_bg_color ?? '#ffffff') }}" class="color-picker">
                                    <input type="text" name="gdpr_cookie_bg_color" id="gdpr_cookie_bg_color_input" value="{{ old('gdpr_cookie_bg_color', $settings->gdpr_cookie_bg_color ?? '#ffffff') }}" class="form-input" placeholder="#ffffff">
                                </div>
                                <script>
                                    document.getElementById('gdpr_cookie_bg_color_picker').addEventListener('input', function(e) {
                                        document.getElementById('gdpr_cookie_bg_color_input').value = e.target.value;
                                    });
                                    document.getElementById('gdpr_cookie_bg_color_input').addEventListener('input', function(e) {
                                        if(/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
                                            document.getElementById('gdpr_cookie_bg_color_picker').value = e.target.value;
                                        }
                                    });
                                </script>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                    Text Color
                                </label>
                                <div class="color-picker-wrapper">
                                    <input type="color" id="gdpr_cookie_text_color_picker" value="{{ old('gdpr_cookie_text_color', $settings->gdpr_cookie_text_color ?? '#1b1b18') }}" class="color-picker">
                                    <input type="text" name="gdpr_cookie_text_color" id="gdpr_cookie_text_color_input" value="{{ old('gdpr_cookie_text_color', $settings->gdpr_cookie_text_color ?? '#1b1b18') }}" class="form-input" placeholder="#1b1b18">
                                </div>
                                <script>
                                    document.getElementById('gdpr_cookie_text_color_picker').addEventListener('input', function(e) {
                                        document.getElementById('gdpr_cookie_text_color_input').value = e.target.value;
                                    });
                                    document.getElementById('gdpr_cookie_text_color_input').addEventListener('input', function(e) {
                                        if(/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
                                            document.getElementById('gdpr_cookie_text_color_picker').value = e.target.value;
                                        }
                                    });
                                </script>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2z"/>
                                    </svg>
                                    Button Color
                                </label>
                                <div class="color-picker-wrapper">
                                    <input type="color" id="gdpr_cookie_button_color_picker" value="{{ old('gdpr_cookie_button_color', $settings->gdpr_cookie_button_color ?? '#FF750F') }}" class="color-picker">
                                    <input type="text" name="gdpr_cookie_button_color" id="gdpr_cookie_button_color_input" value="{{ old('gdpr_cookie_button_color', $settings->gdpr_cookie_button_color ?? '#FF750F') }}" class="form-input" placeholder="#FF750F">
                                </div>
                                <script>
                                    document.getElementById('gdpr_cookie_button_color_picker').addEventListener('input', function(e) {
                                        document.getElementById('gdpr_cookie_button_color_input').value = e.target.value;
                                    });
                                    document.getElementById('gdpr_cookie_button_color_input').addEventListener('input', function(e) {
                                        if(/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
                                            document.getElementById('gdpr_cookie_button_color_picker').value = e.target.value;
                                        }
                                    });
                                </script>
                            </div>
                        </div>

                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Save GDPR Cookie Settings</span>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Settings Sidebar -->
        <div class="lg:col-span-1">
            <div class="settings-section sticky top-6">
                <div class="settings-section-header">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                        <div class="icon-wrapper">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span>Quick Info</span>
                    </h3>
                </div>
                <div class="settings-section-body">
                    <div class="space-y-4">
                        <div class="p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg border border-purple-100">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h4 class="font-bold text-sm text-gray-900">Tips</h4>
                            </div>
                            <p class="text-xs text-gray-600">Changes are saved automatically. Some settings may require a page refresh to take effect.</p>
                        </div>

                        <div class="p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-100">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h4 class="font-bold text-sm text-gray-900">Security</h4>
                            </div>
                            <p class="text-xs text-gray-600">Keep your security settings up to date to protect your account from unauthorized access.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


