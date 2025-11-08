@extends('layouts.admin')

@section('title', 'Create Service')

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
            border-radius: 8px;
            padding: 12px 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
            min-width: 280px;
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
        /* Custom Icon Dropdown Styles */
        .icon-dropdown-wrapper {
            position: relative;
            width: 100%;
        }
        .icon-dropdown-button {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        .icon-dropdown-button:hover {
            border-color: #FF750F;
            background: #fff5ed;
        }
        .icon-dropdown-button:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
        .icon-dropdown-button .selected-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            color: #FF750F;
        }
        .icon-dropdown-button .selected-text {
            flex: 1;
            text-align: left;
        }
        .icon-dropdown-button .dropdown-arrow {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            color: #6b7280;
            transition: transform 0.3s ease;
        }
        .icon-dropdown-button.open .dropdown-arrow {
            transform: rotate(180deg);
        }
        .icon-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 4px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }
        .icon-dropdown-menu.show {
            display: block;
        }
        .icon-dropdown-search {
            padding: 12px;
            border-bottom: 2px solid #e5e7eb;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }
        .icon-dropdown-search input {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .icon-dropdown-search input:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 3px rgba(255, 117, 15, 0.1);
        }
        .icon-dropdown-options {
            padding: 8px;
        }
        .icon-dropdown-option {
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s ease;
            margin-bottom: 2px;
        }
        .icon-dropdown-option:hover {
            background: #fff5ed;
        }
        .icon-dropdown-option.selected {
            background: #FF750F;
            color: white;
        }
        .icon-dropdown-option .option-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            color: #FF750F;
        }
        .icon-dropdown-option.selected .option-icon {
            color: white;
        }
        .icon-dropdown-option .option-text {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
        }
        .icon-dropdown-empty {
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>

    <!-- Success Popup -->
    <div id="success-popup" class="success-popup" style="display: none;">
        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-green-700 font-semibold text-sm" id="success-message">Service created successfully!</p>
        </div>
        <button onclick="closePopup()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Create Service</h1>
                    <p class="text-xs text-gray-600">Add a new service - Single, bulk, editable Status (Active/Inactive)</p>
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
                    <h2 class="text-lg font-bold text-gray-900">Bulk Import Services</h2>
                    <p class="text-sm text-gray-600">Upload an Excel file to import multiple services at once</p>
                </div>
            </div>
            <a href="{{ route('admin.services.template.download') }}" class="admin-btn-primary px-4 py-2 text-sm">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Template
            </a>
        </div>
        <form method="POST" action="{{ route('admin.services.import') }}" enctype="multipart/form-data" class="flex items-center gap-3">
            @csrf
            <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
            <button type="submit" class="admin-btn-primary px-6 py-2">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </button>
        </form>
        @if(session('error'))
            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Service Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Service Information
                </h2>

                <form id="serviceForm" method="POST" action="{{ route('admin.services.store') }}">
                    @csrf

                    <!-- Service Name -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Service Name <span class="required">*</span>
                        </label>
                        <input type="text" name="service_name" id="service_name" class="form-input" placeholder="e.g., Express, Economy" required>
                    </div>

                    <!-- Network -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                            </svg>
                            Network <span class="required">*</span>
                        </label>
                        <select name="network" id="network" class="form-select" required>
                            <option value="">Select Network</option>
                            @foreach($networks as $network)
                                <option value="{{ $network['name'] }}">{{ $network['name'] }} ({{ $network['type'] }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Transit Time -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Transit Time <span class="required">*</span>
                        </label>
                        <input type="text" name="transit_time" id="transit_time" class="form-input" placeholder="e.g., 24-48 Hours, 5-7 Days" required>
                    </div>

                    <!-- Items Allowed -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Items Allowed <span class="required">*</span>
                        </label>
                        <input type="text" name="items_allowed" id="items_allowed" class="form-input" placeholder="e.g., Documents, Small Packages, All Items" required>
                    </div>

                    <!-- Display Title (for Landing Page) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Display Title (for Landing Page)
                        </label>
                        <input type="text" name="display_title" id="display_title" class="form-input" placeholder="e.g., E-Commerce delivery, Pick & Drop">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to use service name</p>
                    </div>

                    <!-- Description (for Landing Page) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Description (for Landing Page)
                        </label>
                        <textarea name="description" id="description" rows="3" class="form-textarea resize-none" placeholder="Service description to display on landing page"></textarea>
                    </div>

                    <!-- Icon Type -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Icon Type
                        </label>
                        <div class="icon-dropdown-wrapper">
                            <input type="hidden" name="icon_type" id="icon_type" value="truck">
                            <button type="button" class="icon-dropdown-button" id="iconDropdownButton" tabindex="0">
                                <span class="selected-icon" id="selectedIcon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </span>
                                <span class="selected-text" id="selectedText">Truck (E-Commerce delivery)</span>
                                <svg class="dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="icon-dropdown-menu" id="iconDropdownMenu">
                                <div class="icon-dropdown-search">
                                    <input type="text" id="iconSearchInput" placeholder="Search icons..." autocomplete="off">
                                </div>
                                <div class="icon-dropdown-options" id="iconOptions">
                                    <!-- Icons will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Is Highlighted -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            Highlight on Landing Page
                        </label>
                        <div class="status-toggle">
                            <label class="status-switch">
                                <input type="checkbox" name="is_highlighted" id="is_highlighted">
                                <span class="status-slider"></span>
                            </label>
                            <span class="text-sm font-medium text-gray-700" id="highlightLabel">Not Highlighted</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Highlighted services appear with orange background on landing page</p>
                    </div>

                    <!-- Remark -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remark
                        </label>
                        <textarea name="remark" id="remark" rows="3" class="form-textarea resize-none" placeholder="Any additional notes"></textarea>
                    </div>

                    <!-- Status -->
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

                    <!-- Submit Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Create Service</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.services.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
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
                    Service Guidelines
                </h3>
                <div class="space-y-3 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Service Name must be unique (e.g., Express, Economy)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Select network from available networks</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Transit time should indicate delivery timeframe</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Specify what items are allowed for this service</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Status can be changed later from All Services</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.services.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        View All Services
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Status toggle
        document.getElementById('status').addEventListener('change', function() {
            document.getElementById('statusLabel').textContent = this.checked ? 'Active' : 'Inactive';
        });
        
        // Highlight toggle
        document.getElementById('is_highlighted').addEventListener('change', function() {
            document.getElementById('highlightLabel').textContent = this.checked ? 'Highlighted' : 'Not Highlighted';
        });

        // Form submission with AJAX
        document.getElementById('serviceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            
            // Validate required fields first
            const serviceName = document.getElementById('service_name').value.trim();
            const network = document.getElementById('network').value;
            const transitTime = document.getElementById('transit_time').value.trim();
            const itemsAllowed = document.getElementById('items_allowed').value.trim();
            
            if (!serviceName || !network || !transitTime || !itemsAllowed) {
                alert('Please fill in all required fields (Service Name, Network, Transit Time, and Items Allowed).');
                return;
            }
            
            const formData = new FormData(form);
            
            // Ensure status checkbox is properly handled
            const statusCheckbox = document.getElementById('status');
            // Remove any existing status value
            formData.delete('status');
            // Set status explicitly based on checkbox state
            if (statusCheckbox.checked) {
                formData.append('status', '1');
            } else {
                formData.append('status', '0');
            }
            
            // Handle is_highlighted checkbox
            const isHighlightedCheckbox = document.getElementById('is_highlighted');
            formData.delete('is_highlighted');
            if (isHighlightedCheckbox.checked) {
                formData.append('is_highlighted', '1');
            } else {
                formData.append('is_highlighted', '0');
            }
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Disable submit button
            submitButton.disabled = true;
            submitButton.innerHTML = '<div class="flex items-center justify-center gap-2"><svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Creating...</span></div>';
            
            // Submit form via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                return response.json().then(data => {
                    return { status: response.status, data: data };
                }).catch(() => {
                    // If response is not JSON (e.g., redirect), handle it
                    if (response.redirected || response.ok) {
                        return { status: response.status, redirected: true };
                    }
                    return { status: response.status, data: { success: false, message: 'An error occurred' } };
                });
            })
            .then(result => {
                if (result.redirected) {
                    // Show success popup
                    showSuccessPopup('Service created successfully!');
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.services.all") }}';
                    }, 1500);
                } else if (result.data) {
                    if (result.data.success) {
                        showSuccessPopup(result.data.message || 'Service created successfully!');
                        // Reset form
                        form.reset();
                        document.getElementById('statusLabel').textContent = 'Active';
                        document.getElementById('highlightLabel').textContent = 'Not Highlighted';
                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = result.data.redirect || '{{ route("admin.services.all") }}';
                        }, 1500);
                    } else {
                        // Handle validation errors
                        if (result.data.errors) {
                            let errorMessages = Object.values(result.data.errors).flat().join('\n');
                            alert('Validation errors:\n' + errorMessages);
                            console.error('Validation errors:', result.data.errors);
                        } else {
                            alert(result.data.message || 'An error occurred while creating the service.');
                            console.error('Error response:', result.data);
                        }
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    }
                } else {
                    // No data returned
                    console.error('No data in response:', result);
                    alert('Unexpected response from server. Please try again.');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                console.error('Error details:', error.message, error.stack);
                alert('An error occurred while creating the service. Please check the console for details.');
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });

        // Show success popup
        function showSuccessPopup(message) {
            const popup = document.getElementById('success-popup');
            const messageElement = document.getElementById('success-message');
            
            if (popup && messageElement) {
                messageElement.textContent = message;
                popup.style.display = 'flex';
                
                // Auto-close after 5 seconds
                setTimeout(() => {
                    closePopup();
                }, 5000);
            }
        }

        // Close popup
        function closePopup() {
            const popup = document.getElementById('success-popup');
            if (popup) {
                popup.classList.add('closing');
                setTimeout(() => {
                    popup.style.display = 'none';
                    popup.classList.remove('closing');
                }, 300);
            }
        }

        // Icon Types Configuration
        const iconTypes = {
            'truck': {
                name: 'Truck (E-Commerce delivery)',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h10v8H3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10h5l3 3v2h-8"/><circle cx="7" cy="17" r="1.5"/><circle cx="17" cy="17" r="1.5"/><rect x="13" y="8" width="3" height="3" rx="0.5"/><path stroke-linecap="round" d="M14.5 9l1 1"/>'
            },
            'pickup': {
                name: 'Pickup (Pick & Drop)',
                svg: '<circle cx="9" cy="9" r="3"/><circle cx="15" cy="15" r="3"/><path stroke-linecap="round" d="M9 9l6 6"/><path stroke-linecap="round" d="M12 6v3m0 3v3"/><path stroke-linecap="round" d="M15 12l-3-3"/>'
            },
            'package': {
                name: 'Package (Packaging)',
                svg: '<rect x="5" y="7" width="14" height="10" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7v-2h6v2"/><circle cx="18" cy="9" r="2" fill="currentColor"/><path d="M18 8v2" stroke="currentColor" stroke-width="1"/>'
            },
            'warehouse': {
                name: 'Warehouse (Warehousing)',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16v9H4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10V7h8v3"/><rect x="7" y="13" width="3" height="3" rx="0.5"/><rect x="14" y="13" width="3" height="3" rx="0.5"/>'
            },
            'cargo': {
                name: 'Cargo (Freight)',
                svg: '<rect x="3" y="6" width="18" height="12" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18"/><circle cx="7" cy="12" r="1"/><circle cx="17" cy="12" r="1"/><path stroke-linecap="round" d="M9 6V4h6v2"/>'
            },
            'airplane': {
                name: 'Airplane (Air Freight)',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>'
            },
            'ship': {
                name: 'Ship (Sea Freight)',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 18h18M3 14h18M5 10h14l-1-4H6l-1 4z"/><circle cx="8" cy="18" r="2"/><circle cx="16" cy="18" r="2"/><path stroke-linecap="round" d="M12 10v4"/>'
            },
            'box': {
                name: 'Box (Parcel)',
                svg: '<rect x="6" y="6" width="12" height="12" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"/><path stroke-linecap="round" d="M9 9h6M9 15h6"/>'
            },
            'delivery': {
                name: 'Delivery (Express)',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>'
            },
            'location': {
                name: 'Location (Tracking)',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>'
            },
            'clock': {
                name: 'Clock (Time-Sensitive)',
                svg: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'
            },
            'storage': {
                name: 'Storage (Inventory)',
                svg: '<rect x="3" y="4" width="18" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8h18M3 12h18M3 16h18"/><circle cx="7" cy="10" r="1"/><circle cx="7" cy="14" r="1"/>'
            },
            'trolley': {
                name: 'Trolley (Handling)',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>'
            },
            'container': {
                name: 'Container (Bulk Shipping)',
                svg: '<rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9h18M3 13h18"/><rect x="6" y="10" width="4" height="4" rx="0.5"/><rect x="14" y="10" width="4" height="4" rx="0.5"/>'
            },
            'forklift': {
                name: 'Forklift (Material Handling)',
                svg: '<rect x="3" y="12" width="8" height="6" rx="1"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 18h2M5 12V8h6v4M15 12h4M17 10v4"/><circle cx="7" cy="20" r="2"/><circle cx="17" cy="20" r="2"/>'
            },
            'pallet': {
                name: 'Pallet (Warehouse)',
                svg: '<rect x="3" y="6" width="18" height="12" rx="1"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 6v12M15 6v12"/><rect x="5" y="7" width="3" height="3" rx="0.5"/><rect x="16" y="7" width="3" height="3" rx="0.5"/>'
            },
            'truck-fast': {
                name: 'Fast Truck (Express Delivery)',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h10v8H3z"/><circle cx="7" cy="17" r="1.5"/><circle cx="17" cy="17" r="1.5"/>'
            },
            'document': {
                name: 'Document (Document Delivery)',
                svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
            }
        };

        // Icon Dropdown Functionality
        const iconDropdownButton = document.getElementById('iconDropdownButton');
        const iconDropdownMenu = document.getElementById('iconDropdownMenu');
        const iconSearchInput = document.getElementById('iconSearchInput');
        const iconOptions = document.getElementById('iconOptions');
        const iconTypeInput = document.getElementById('icon_type');
        const selectedIcon = document.getElementById('selectedIcon');
        const selectedText = document.getElementById('selectedText');

        // Function to get icon SVG
        function getIconSvg(iconKey) {
            return iconTypes[iconKey] ? iconTypes[iconKey].svg : '';
        }

        // Function to render icon option
        function renderIconOption(iconKey, iconData, isSelected = false) {
            const option = document.createElement('div');
            option.className = `icon-dropdown-option ${isSelected ? 'selected' : ''}`;
            option.dataset.value = iconKey;
            option.innerHTML = `
                <span class="option-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        ${iconData.svg}
                    </svg>
                </span>
                <span class="option-text">${iconData.name}</span>
            `;
            option.addEventListener('click', () => selectIcon(iconKey));
            return option;
        }

        // Function to populate icon options
        function populateIconOptions(filter = '') {
            iconOptions.innerHTML = '';
            const filtered = Object.entries(iconTypes).filter(([key, data]) => 
                data.name.toLowerCase().includes(filter.toLowerCase())
            );

            if (filtered.length === 0) {
                iconOptions.innerHTML = '<div class="icon-dropdown-empty">No icons found</div>';
                return;
            }

            const currentValue = iconTypeInput.value || 'truck';
            filtered.forEach(([key, data]) => {
                const option = renderIconOption(key, data, key === currentValue);
                iconOptions.appendChild(option);
            });
        }

        // Function to select icon
        function selectIcon(iconKey) {
            const iconData = iconTypes[iconKey];
            if (!iconData) return;

            iconTypeInput.value = iconKey;
            selectedIcon.innerHTML = `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    ${iconData.svg}
                </svg>
            `;
            selectedText.textContent = iconData.name;

            // Update selected state in dropdown
            document.querySelectorAll('.icon-dropdown-option').forEach(opt => {
                opt.classList.toggle('selected', opt.dataset.value === iconKey);
            });

            // Close dropdown
            iconDropdownMenu.classList.remove('show');
            iconDropdownButton.classList.remove('open');
        }

        // Toggle dropdown
        iconDropdownButton.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = iconDropdownMenu.classList.contains('show');
            if (isOpen) {
                iconDropdownMenu.classList.remove('show');
                iconDropdownButton.classList.remove('open');
            } else {
                iconDropdownMenu.classList.add('show');
                iconDropdownButton.classList.add('open');
                iconSearchInput.focus();
            }
        });

        // Search functionality
        iconSearchInput.addEventListener('input', (e) => {
            populateIconOptions(e.target.value);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!iconDropdownButton.contains(e.target) && !iconDropdownMenu.contains(e.target)) {
                iconDropdownMenu.classList.remove('show');
                iconDropdownButton.classList.remove('open');
            }
        });

        // Initialize with default icon
        populateIconOptions();
        selectIcon('truck');
    </script>
@endsection



