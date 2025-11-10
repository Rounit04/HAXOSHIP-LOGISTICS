@extends('layouts.admin')

@section('title', 'SMS Send Settings')

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
        .form-input, .form-textarea {
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
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
        .form-textarea {
            resize: vertical;
            min-height: 100px;
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
        .template-section {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">SMS Send Settings</h1>
                    <p class="text-xs text-gray-600">Configure when to send SMS and message templates</p>
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

    <form method="POST" action="{{ route('admin.sms-send-settings.update') }}">
        @csrf
        
        <div class="settings-section">
            <div class="settings-section-header">
                <h3 class="text-lg font-bold text-gray-900">SMS Send Options</h3>
            </div>
            <div class="settings-section-body">
                <!-- Send on Booking -->
                <div class="toggle-item">
                    <div class="toggle-item-content">
                        <div class="toggle-item-title">Send SMS on Booking</div>
                        <p class="text-xs text-gray-600">Send SMS notification when a new booking is created</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="send_on_booking" value="1" {{ ($settings->send_on_booking ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="template-section" id="booking-template-section" style="{{ ($settings->send_on_booking ?? false) ? '' : 'display: none;' }}">
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Booking SMS Template
                        </label>
                        <textarea name="booking_template" class="form-textarea" placeholder="Enter SMS template for booking notifications. Use {booking_id}, {customer_name}, etc. as placeholders.">{{ $settings->booking_template ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Send on Delivery -->
                <div class="toggle-item">
                    <div class="toggle-item-content">
                        <div class="toggle-item-title">Send SMS on Delivery</div>
                        <p class="text-xs text-gray-600">Send SMS notification when a package is delivered</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="send_on_delivery" value="1" {{ ($settings->send_on_delivery ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="template-section" id="delivery-template-section" style="{{ ($settings->send_on_delivery ?? false) ? '' : 'display: none;' }}">
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Delivery SMS Template
                        </label>
                        <textarea name="delivery_template" class="form-textarea" placeholder="Enter SMS template for delivery notifications. Use {booking_id}, {customer_name}, etc. as placeholders.">{{ $settings->delivery_template ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Send on Pickup -->
                <div class="toggle-item">
                    <div class="toggle-item-content">
                        <div class="toggle-item-title">Send SMS on Pickup</div>
                        <p class="text-xs text-gray-600">Send SMS notification when a package is picked up</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="send_on_pickup" value="1" {{ ($settings->send_on_pickup ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="template-section" id="pickup-template-section" style="{{ ($settings->send_on_pickup ?? false) ? '' : 'display: none;' }}">
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Pickup SMS Template
                        </label>
                        <textarea name="pickup_template" class="form-textarea" placeholder="Enter SMS template for pickup notifications. Use {booking_id}, {customer_name}, etc. as placeholders.">{{ $settings->pickup_template ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Send on Status Update -->
                <div class="toggle-item">
                    <div class="toggle-item-content">
                        <div class="toggle-item-title">Send SMS on Status Update</div>
                        <p class="text-xs text-gray-600">Send SMS notification when booking status is updated</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="send_on_status_update" value="1" {{ ($settings->send_on_status_update ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="template-section" id="status-update-template-section" style="{{ ($settings->send_on_status_update ?? false) ? '' : 'display: none;' }}">
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Status Update SMS Template
                        </label>
                        <textarea name="status_update_template" class="form-textarea" placeholder="Enter SMS template for status update notifications. Use {booking_id}, {customer_name}, {status}, etc. as placeholders.">{{ $settings->status_update_template ?? '' }}</textarea>
                    </div>
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

    <script>
        // Toggle template sections based on checkbox state
        document.querySelectorAll('input[type="checkbox"][name^="send_on_"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const sectionId = this.name.replace('send_on_', '') + '-template-section';
                const section = document.getElementById(sectionId);
                if (section) {
                    section.style.display = this.checked ? 'block' : 'none';
                }
            });
        });
    </script>
@endsection





