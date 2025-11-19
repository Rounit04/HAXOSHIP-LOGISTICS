@extends('layouts.admin')

@section('title', 'Create Shipping Charge')

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
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
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
        
        /* Error Modal Styles */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
        
        #errorModal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 9999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(0, 0, 0, 0.5) !important;
            backdrop-filter: blur(2px);
            pointer-events: auto !important;
            overflow: auto !important;
            padding: 20px !important;
            animation: fadeIn 0.2s ease-out;
        }
        
        #errorModal.closing {
            animation: fadeOut 0.2s ease-out forwards;
        }
        
        #errorModal > div {
            position: relative !important;
            transform: translateZ(0) !important;
            backface-visibility: hidden !important;
            pointer-events: auto !important;
            will-change: auto !important;
            max-height: 80vh !important;
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            max-width: 42rem !important;
            margin: auto !important;
            animation: slideUp 0.3s ease-out;
        }
        
        #errorScrollContainer {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f1f1;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
            max-height: 100% !important;
        }
        
        #errorScrollContainer::-webkit-scrollbar {
            width: 10px;
        }
        
        #errorScrollContainer::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
            margin: 4px;
        }
        
        #errorScrollContainer::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
            border: 2px solid #f1f5f9;
        }
        
        #errorScrollContainer::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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
            <p class="text-green-700 font-semibold text-sm" id="success-message">Shipping charge created successfully!</p>
        </div>
        <button onclick="closePopup()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Error Modal (will be shown dynamically - ONLY for import errors) -->
    <div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none !important; position: fixed; top: 0; left: 0; right: 0; bottom: 0; visibility: hidden;">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 flex flex-col" style="max-height: 80vh; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); position: relative;">
            <!-- Modal Header - Fixed at top -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-5 py-4 flex items-center justify-between rounded-t-lg flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white bg-opacity-20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Import Validation Failed</h3>
                        <p class="text-xs text-red-100" id="errorModalSubtitle">Found 0 error(s) - Please review and fix</p>
                    </div>
                </div>
                <button onclick="closeErrorModal()" class="text-white hover:text-red-200 transition p-1.5 rounded-lg hover:bg-white hover:bg-opacity-10 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body - Scrollable middle section -->
            <div class="flex-1 overflow-hidden flex flex-col bg-gray-50 min-h-0">
                <!-- Summary Banner - Fixed -->
                <div class="bg-red-50 border-b border-red-200 px-5 py-3 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm font-semibold text-red-800">No shipping charges were imported. All errors must be resolved before importing.</p>
                    </div>
                </div>
                
                <!-- Error List Container - Scrollable -->
                <div class="flex-1 overflow-hidden px-5 py-4 min-h-0">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm h-full flex flex-col">
                        <!-- Error List Header - Fixed -->
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between flex-shrink-0">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide" id="errorDetailsTitle">Error Details (0 total)</span>
                            </div>
                            <div class="text-xs text-gray-500 font-medium">
                                <span id="visibleCount">0</span> / <span id="totalErrors">0</span> visible
                            </div>
                        </div>
                        
                        <!-- Scrollable Error List -->
                        <div class="flex-1 overflow-y-auto min-h-0" id="errorScrollContainer" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f1f1;">
                            <div class="divide-y divide-gray-100" id="errorListContainer">
                                <!-- Errors will be inserted here dynamically -->
                            </div>
                        </div>
                        
                        <!-- Scroll Indicator - Fixed at bottom of scroll area -->
                        <div id="scrollIndicator" class="px-4 py-2 border-t border-gray-200 bg-gray-50 text-center flex-shrink-0" style="display: none;">
                            <p class="text-xs text-gray-500">Scroll down to see all errors</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer - Fixed at bottom -->
            <div class="bg-white border-t border-gray-200 px-5 py-4 rounded-b-lg flex items-center justify-between flex-shrink-0">
                <div class="text-xs text-gray-500">
                    <span class="font-medium" id="footerErrorCount">0</span> error(s) found
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="closeErrorModal()" class="px-5 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Close
                    </button>
                    <button onclick="closeErrorModal()" class="px-5 py-2 text-sm font-semibold text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition shadow-sm">
                        I Understand
                    </button>
                </div>
            </div>
        </div>
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Create Shipping Charge</h1>
                    <p class="text-xs text-gray-600">Add a new shipping charge - Single, bulk, and Update (Excel or Single update)</p>
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
                    <h2 class="text-lg font-bold text-gray-900">Bulk Import Shipping Charges</h2>
                    <p class="text-sm text-gray-600">Upload an Excel file to import multiple shipping charges at once</p>
                </div>
            </div>
            <a href="{{ route('admin.shipping-charges.template.download') }}" class="admin-btn-primary px-4 py-2 text-sm">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Template
            </a>
        </div>
        <form id="importForm" method="POST" action="{{ route('admin.shipping-charges.import') }}" enctype="multipart/form-data" class="flex items-center gap-3">
            @csrf
            <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls,.csv" required class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
            <button type="submit" id="importBtn" class="admin-btn-primary px-6 py-2">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </button>
        </form>
    </div>

    <!-- Bulk Update Section -->
    <div class="form-card p-6 mb-6" style="background: linear-gradient(135deg, #f0faff 0%, #e0f2ff 100%); border: 2px solid rgba(14, 165, 233, 0.2);">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Bulk Update Shipping Charges</h2>
                    <p class="text-sm text-gray-600">Upload an Excel/CSV file to update rate or remark for existing charges</p>
                </div>
            </div>
            <a href="{{ route('admin.shipping-charges.update-template.download') }}" class="admin-btn-secondary px-4 py-2 text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Download Update Template
            </a>
        </div>
        <div class="bg-white rounded-lg p-4 border border-blue-100 text-sm text-gray-600 mb-4">
            <ul class="list-disc pl-5 space-y-1">
                <li>Only the <strong>Rate</strong> and <strong>Remark</strong> columns will be updated.</li>
                <li>Origin, Destination, both Zones, Network, and Service are used to find the existing record.</li>
                <li>If no rate is supplied the previous rate remains unchanged.</li>
                <li>Leave remark blank to clear it or omit the column to keep the previous remark.</li>
            </ul>
        </div>
        <form id="updateForm" method="POST" action="{{ route('admin.shipping-charges.import-updates') }}" enctype="multipart/form-data" class="flex flex-col md:flex-row md:items-center gap-3">
            @csrf
            <input type="file" name="update_file" id="update_file" accept=".xlsx,.xls,.csv" required class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-sky-500">
            <button type="submit" id="updateBtn" class="admin-btn-secondary px-6 py-2 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update from File
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Shipping Charge Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Shipping Charge Information
                </h2>

                <form id="shippingChargeForm" method="POST" action="{{ route('admin.shipping-charges.store') }}">
                    @csrf

                    <!-- Origin, Origin Pincode & Origin Zone -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                Origin <span class="required">*</span>
                            </label>
                            <select name="origin" id="origin" class="form-select" required>
                                <option value="">Select Origin</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country['name'] }}">{{ $country['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Origin Pincode <span class="required">*</span>
                            </label>
                            <select name="origin_pincode" id="origin_pincode" class="form-select" required>
                                <option value="">Select Origin Country First</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Origin Zone <span class="required">*</span>
                            </label>
                            <select name="origin_zone" id="origin_zone" class="form-select" required>
                                <option value="">Select Origin Pincode First</option>
                            </select>
                        </div>
                    </div>

                    <!-- Destination & Destination Zone -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Destination <span class="required">*</span>
                            </label>
                            <select name="destination" id="destination" class="form-select" required>
                                <option value="">Select Destination</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country['name'] }}">{{ $country['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Destination Pincode <span class="required">*</span>
                            </label>
                            <select name="destination_pincode" id="destination_pincode" class="form-select" required>
                                <option value="">Select Destination Country First</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Destination Zone <span class="required">*</span>
                            </label>
                            <select name="destination_zone" id="destination_zone" class="form-select" required>
                                <option value="">Select Destination Pincode First</option>
                            </select>
                        </div>
                    </div>

                    <!-- Shipment Type -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Shipment Type <span class="required">*</span>
                        </label>
                        <select name="shipment_type" id="shipment_type" class="form-select" required>
                            <option value="">Select Shipment Type</option>
                            @foreach($shipmentTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Min Weight & Max Weight -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                                Min Weight (KG) <span class="required">*</span>
                            </label>
                            <input type="number" name="min_weight" id="min_weight" class="form-input" step="0.01" min="0.01" value="0.01" required>
                            <p class="text-xs text-gray-500 mt-1">Minimum: 0.01 KG</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                                Max Weight (KG) <span class="required">*</span>
                            </label>
                            <input type="number" name="max_weight" id="max_weight" class="form-input" step="0.01" min="0.01" value="10" required>
                            <p class="text-xs text-gray-500 mt-1">Example: 10 KG</p>
                        </div>
                    </div>

                    <!-- Network & Service -->
                    <div class="form-grid">
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
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Service <span class="required">*</span> (Network Wise)
                            </label>
                            <select name="service" id="service" class="form-select" required>
                                <option value="">Select Network First</option>
                            </select>
                        </div>
                    </div>

                    <!-- Rate -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Rate <span class="required">*</span>
                        </label>
                        <input type="number" name="rate" id="rate" class="form-input" step="0.01" min="0" placeholder="0.00" required>
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

                    <!-- Submit Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Create Shipping Charge</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.shipping-charges.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
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
                    Shipping Charge Guidelines
                </h3>
                <div class="space-y-3 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Select origin and destination countries and zones</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Shipment types: Dox, Non-Dox, Medicine</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Min weight: 0.01 KG, Max weight: 10 KG (example)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Select network first, then service will be filtered</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Rate is the shipping charge amount</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.shipping-charges.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        View All Charges
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Services data for filtering
        const allServices = @json($services);
        
        // Network-wise service filter
        const networkSelect = document.getElementById('network');
        const serviceSelect = document.getElementById('service');

        networkSelect.addEventListener('change', function() {
            const selectedNetwork = this.value;
            serviceSelect.innerHTML = '<option value="">Select Service</option>';
            
            if (selectedNetwork) {
                const filteredServices = allServices.filter(service => service.network === selectedNetwork);
                
                if (filteredServices.length > 0) {
                    filteredServices.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.name;
                        option.textContent = service.name;
                        serviceSelect.appendChild(option);
                    });
                } else {
                    serviceSelect.innerHTML = '<option value="">No services available for this network</option>';
                }
            }
        });

        // Validate max weight is greater than min weight
        const minWeightInput = document.getElementById('min_weight');
        const maxWeightInput = document.getElementById('max_weight');

        function validateWeight() {
            const minWeight = parseFloat(minWeightInput.value);
            const maxWeight = parseFloat(maxWeightInput.value);
            
            if (maxWeight <= minWeight) {
                maxWeightInput.setCustomValidity('Max weight must be greater than min weight');
            } else {
                maxWeightInput.setCustomValidity('');
            }
        }

        minWeightInput.addEventListener('input', validateWeight);
        maxWeightInput.addEventListener('input', validateWeight);

        // API endpoint for fetching pincodes by country
        const pincodeApiUrl = '{{ route("admin.api.pincodes-by-country") }}';

        // Update pincode options based on country selection
        async function updatePincodes(selectElement, country) {
            if (!country) {
                selectElement.innerHTML = '<option value="">Select Country First</option>';
                return;
            }

            selectElement.innerHTML = '<option value="">Loading...</option>';
            selectElement.disabled = true;

            try {
                const response = await fetch(`${pincodeApiUrl}?country=${encodeURIComponent(country)}`);
                const result = await response.json();

                if (result.success && result.data) {
                    selectElement.innerHTML = '<option value="">Select Pincode</option>';
                    result.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.pincode;
                        // Show pincode with zones if available
                        const zonesText = item.zones && item.zones.length > 0 
                            ? ` (${item.zones.join(', ')})` 
                            : '';
                        option.textContent = item.pincode + zonesText;
                        option.setAttribute('data-zones', JSON.stringify(item.zones || []));
                        selectElement.appendChild(option);
                    });
                } else {
                    selectElement.innerHTML = '<option value="">No pincodes found</option>';
                }
            } catch (error) {
                console.error('Error fetching pincodes:', error);
                selectElement.innerHTML = '<option value="">Error loading pincodes</option>';
            } finally {
                selectElement.disabled = false;
            }
        }

        // Update zone options based on pincode selection
        function updateZones(zoneSelectElement, pincodeSelectElement) {
            const selectedOption = pincodeSelectElement.options[pincodeSelectElement.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                zoneSelectElement.innerHTML = '<option value="">Select Pincode First</option>';
                return;
            }

            const zonesData = selectedOption.getAttribute('data-zones');
            if (zonesData) {
                try {
                    const zones = JSON.parse(zonesData);
                    zoneSelectElement.innerHTML = '<option value="">Select Zone</option>';
                    zones.forEach(zone => {
                        const option = document.createElement('option');
                        option.value = zone;
                        option.textContent = zone;
                        zoneSelectElement.appendChild(option);
                    });
                } catch (e) {
                    zoneSelectElement.innerHTML = '<option value="">No zones available</option>';
                }
            } else {
                zoneSelectElement.innerHTML = '<option value="">No zones available</option>';
            }
        }

        // Origin country change
        document.getElementById('origin')?.addEventListener('change', function() {
            updatePincodes(document.getElementById('origin_pincode'), this.value);
            document.getElementById('origin_zone').innerHTML = '<option value="">Select Origin Pincode First</option>';
        });

        // Origin pincode change
        document.getElementById('origin_pincode')?.addEventListener('change', function() {
            updateZones(document.getElementById('origin_zone'), this);
        });

        // Destination country change
        document.getElementById('destination')?.addEventListener('change', function() {
            updatePincodes(document.getElementById('destination_pincode'), this.value);
            document.getElementById('destination_zone').innerHTML = '<option value="">Select Destination Pincode First</option>';
        });

        // Destination pincode change
        document.getElementById('destination_pincode')?.addEventListener('change', function() {
            updateZones(document.getElementById('destination_zone'), this);
        });

        // Form submission with AJAX
        document.getElementById('shippingChargeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            
            // Validate required fields first
            const origin = document.getElementById('origin').value;
            const originPincode = document.getElementById('origin_pincode').value;
            const originZone = document.getElementById('origin_zone').value;
            const destination = document.getElementById('destination').value;
            const destinationPincode = document.getElementById('destination_pincode').value;
            const destinationZone = document.getElementById('destination_zone').value;
            const shipmentType = document.getElementById('shipment_type').value;
            const minWeight = document.getElementById('min_weight').value;
            const maxWeight = document.getElementById('max_weight').value;
            const network = document.getElementById('network').value;
            const service = document.getElementById('service').value;
            const rate = document.getElementById('rate').value;
            
            if (!origin || !originPincode || !originZone || !destination || !destinationPincode || !destinationZone || !shipmentType || !minWeight || !maxWeight || !network || !service || !rate) {
                alert('Please fill in all required fields.');
                return;
            }
            
            // Validate weight
            if (parseFloat(maxWeight) <= parseFloat(minWeight)) {
                alert('Max weight must be greater than min weight.');
                return;
            }
            
            const formData = new FormData(form);
            
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
                    showSuccessPopup('Shipping charge created successfully!');
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.shipping-charges.all") }}';
                    }, 1500);
                } else if (result.data) {
                    if (result.data.success) {
                        showSuccessPopup(result.data.message || 'Shipping charge created successfully!');
                        // Reset form
                        form.reset();
                        document.getElementById('min_weight').value = '0.01';
                        document.getElementById('max_weight').value = '10';
                        document.getElementById('service').innerHTML = '<option value="">Select Network First</option>';
                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = result.data.redirect || '{{ route("admin.shipping-charges.all") }}';
                        }, 1500);
                    } else {
                        // Handle validation errors
                        if (result.data.errors) {
                            let errorMessages = Object.values(result.data.errors).flat().join('\n');
                            alert('Validation errors:\n' + errorMessages);
                            console.error('Validation errors:', result.data.errors);
                        } else {
                            alert(result.data.message || 'An error occurred while creating the shipping charge.');
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
                alert('An error occurred while creating the shipping charge. Please check the console for details.');
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

        // Show error modal (like zones section)
        function showErrorModal(errorList) {
            const modal = document.getElementById('errorModal');
            if (!modal || !errorList || errorList.length === 0) {
                console.error('Cannot show error modal: modal not found or no errors');
                return;
            }
            
            const totalErrors = errorList.length;
            
            // Update header
            const subtitleEl = document.getElementById('errorModalSubtitle');
            const detailsTitleEl = document.getElementById('errorDetailsTitle');
            const totalErrorsEl = document.getElementById('totalErrors');
            const footerErrorCountEl = document.getElementById('footerErrorCount');
            
            if (subtitleEl) subtitleEl.textContent = `Found ${totalErrors} error(s) - Please review and fix`;
            if (detailsTitleEl) detailsTitleEl.textContent = `Error Details (${totalErrors} total)`;
            if (totalErrorsEl) totalErrorsEl.textContent = totalErrors;
            if (footerErrorCountEl) footerErrorCountEl.textContent = totalErrors;
            
            // Clear and populate error list
            const errorListContainer = document.getElementById('errorListContainer');
            if (!errorListContainer) {
                console.error('Error list container not found');
                return;
            }
            
            errorListContainer.innerHTML = '';
            
            errorList.forEach((error, index) => {
                // Escape HTML to prevent XSS
                const errorText = String(error).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                
                const errorItem = document.createElement('div');
                errorItem.className = 'px-4 py-3 hover:bg-red-50 transition-colors';
                errorItem.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center">
                                <span class="text-xs font-bold text-red-600">${index + 1}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800 leading-relaxed break-words">${errorText}</p>
                        </div>
                    </div>
                `;
                errorListContainer.appendChild(errorItem);
            });
            
            // Show scroll indicator if more than 20 errors
            const scrollIndicator = document.getElementById('scrollIndicator');
            if (scrollIndicator) {
                if (totalErrors > 20) {
                    scrollIndicator.style.display = 'block';
                    const indicatorText = scrollIndicator.querySelector('p');
                    if (indicatorText) {
                        indicatorText.textContent = `Scroll down to see all ${totalErrors} errors`;
                    }
                } else {
                    scrollIndicator.style.display = 'none';
                }
            }
            
            // Reset scroll position
            const scrollContainer = document.getElementById('errorScrollContainer');
            if (scrollContainer) {
                scrollContainer.scrollTop = 0;
            }
            
            // Update visible count
            updateVisibleErrorCount();
            
            // Show modal
            modal.style.display = 'flex';
            modal.style.visibility = 'visible';
            
            // Add event listeners (remove old ones first to prevent duplicates)
            document.removeEventListener('keydown', handleEscapeKey);
            document.addEventListener('keydown', handleEscapeKey);
            
            modal.removeEventListener('click', handleOutsideClick);
            modal.addEventListener('click', handleOutsideClick);
            
            // Add scroll listener
            if (scrollContainer) {
                scrollContainer.removeEventListener('scroll', updateVisibleErrorCount);
                scrollContainer.addEventListener('scroll', updateVisibleErrorCount);
            }
        }
        
        // Close error modal
        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            if (modal) {
                modal.classList.add('closing');
                setTimeout(() => {
                    modal.style.display = 'none';
                    modal.style.visibility = 'hidden';
                    modal.classList.remove('closing');
                    document.removeEventListener('keydown', handleEscapeKey);
                    modal.removeEventListener('click', handleOutsideClick);
                    const scrollContainer = document.getElementById('errorScrollContainer');
                    if (scrollContainer) {
                        scrollContainer.removeEventListener('scroll', updateVisibleErrorCount);
                    }
                }, 200);
            }
        }
        
        // Make closeErrorModal available globally
        window.closeErrorModal = closeErrorModal;
        
        // Handle escape key
        function handleEscapeKey(e) {
            if (e.key === 'Escape') {
                closeErrorModal();
            }
        }
        
        // Handle outside click
        function handleOutsideClick(e) {
            const modal = document.getElementById('errorModal');
            if (modal && e.target === modal) {
                closeErrorModal();
            }
        }
        
        // Ensure modal is hidden on page load
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('errorModal');
            if (modal) {
                modal.style.display = 'none';
                modal.style.visibility = 'hidden';
            }
        });
        
        // Update visible error count
        function updateVisibleErrorCount() {
            const scrollContainer = document.getElementById('errorScrollContainer');
            const visibleCountEl = document.getElementById('visibleCount');
            const totalErrorsEl = document.getElementById('totalErrors');
            
            if (!scrollContainer || !visibleCountEl || !totalErrorsEl) return;
            
            const scrollTop = scrollContainer.scrollTop;
            const containerHeight = scrollContainer.clientHeight;
            const itemHeight = 60; // Approximate height per error item
            const visibleItems = Math.ceil((scrollTop + containerHeight) / itemHeight);
            const totalItems = parseInt(totalErrorsEl.textContent) || 0;
            
            visibleCountEl.textContent = Math.min(Math.max(visibleItems, 1), totalItems);
        }

        function ensureFileSelected(form, fileInput, submitButton) {
            if (!fileInput) {
                return false;
            }

            if (fileInput.files && fileInput.files[0]) {
                return false;
            }

            const resubmitIfChosen = () => {
                fileInput.removeEventListener('change', resubmitIfChosen);
                if (fileInput.files && fileInput.files[0]) {
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit(submitButton || null);
                    } else {
                        form.submit();
                    }
                }
            };

            fileInput.addEventListener('change', resubmitIfChosen, { once: true });
            fileInput.click();
            return true;
        }

        // Handle import form submission with AJAX
        document.getElementById('importForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const fileInput = document.getElementById('excel_file');
            const importBtn = document.getElementById('importBtn');

            if (ensureFileSelected(form, fileInput, importBtn)) {
                return;
            }
            
            // Check file size (max 50MB as per server config)
            const file = fileInput.files[0];
            const maxSize = 50 * 1024 * 1024; // 50MB in bytes
            if (file.size > maxSize) {
                showErrorModal([`File size exceeds the maximum allowed size of 50MB. Your file is ${(file.size / (1024 * 1024)).toFixed(2)}MB.`]);
                return;
            }
            
            const formData = new FormData();
            const originalButtonText = importBtn.innerHTML;
            
            // Manually append file to avoid browsers dropping it when using new FormData(form)
            formData.append('excel_file', file);
            
            // Append CSRF token (prefer token inside this form, fallback to global)
            const formCsrf = form.querySelector('input[name="_token"]');
            const globalCsrf = document.querySelector('meta[name="csrf-token"]');
            const csrfValue = formCsrf ? formCsrf.value : (globalCsrf ? globalCsrf.content : '');
            if (formCsrf) {
                formData.append('_token', formCsrf.value);
            } else if (globalCsrf) {
                formData.append('_token', globalCsrf.content);
            }
            
            // Disable submit button and show loading state
            importBtn.disabled = true;
            importBtn.innerHTML = '<svg class="animate-spin h-4 w-4 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Importing...';
            
            // Submit form via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfValue
                }
            })
            .then(response => {
                // Check if response is OK (status 200-299)
                if (!response.ok && response.status !== 422 && response.status !== 500) {
                    // Handle non-OK responses
                    return response.text().then(text => {
                        throw new Error(`Server error: ${response.status} - ${response.statusText}`);
                    });
                }
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        return { status: response.status, data: data };
                    }).catch(err => {
                        // If JSON parsing fails, try to get text
                        return response.text().then(text => {
                            return { status: response.status, data: { success: false, message: text || 'Invalid response from server' } };
                        });
                    });
                } else {
                    // If response is not JSON (e.g., redirect), try to parse as text
                    return response.text().then(text => {
                        // Check if it's a redirect response
                        if (response.redirected || (response.ok && response.status >= 200 && response.status < 300)) {
                            // Try to extract success/error from session flash
                            return { status: response.status, redirected: true, text: text };
                        }
                        return { status: response.status, data: { success: false, message: text || 'An error occurred' } };
                    });
                }
            })
            .then(result => {
                if (result.redirected) {
                    // If redirected, check for success/error in the response
                    // For now, assume success if redirected
                    showSuccessPopup('Shipping charges imported successfully!');
                    // Reset form
                    form.reset();
                    // Optionally redirect after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.shipping-charges.all") }}';
                    }, 1500);
                } else if (result.data) {
                    if (result.data.success) {
                        const message = result.data.message || `Successfully imported ${result.data.imported_count || 0} shipping charge(s)!`;
                        showSuccessPopup(message);
                        // Reset form
                        form.reset();
                        // Optionally redirect after a short delay
                        setTimeout(() => {
                            if (result.data.redirect) {
                                window.location.href = result.data.redirect;
                            } else {
                                window.location.href = '{{ route("admin.shipping-charges.all") }}';
                            }
                        }, 1500);
                    } else {
                        // Handle errors - show in modal (same style as zones section)
                        let errorList = [];
                        
                        // Debug: log the response to see what we're getting
                        console.log('Error response:', result.data);
                        
                        // Handle validation errors (Laravel format)
                        if (result.data.errors) {
                            if (Array.isArray(result.data.errors)) {
                                // Array of error messages (from ShippingChargesImport)
                                errorList = result.data.errors;
                            } else if (typeof result.data.errors === 'object') {
                                // Laravel validation errors format (nested object)
                                Object.keys(result.data.errors).forEach(key => {
                                    const fieldErrors = Array.isArray(result.data.errors[key]) 
                                        ? result.data.errors[key] 
                                        : [result.data.errors[key]];
                                    fieldErrors.forEach(err => {
                                        errorList.push(err);
                                    });
                                });
                            } else if (typeof result.data.errors === 'string') {
                                errorList = [result.data.errors];
                            }
                        }
                        
                        // If no errors in array but we have a message, use it
                        if (errorList.length === 0 && result.data.message) {
                            errorList = [result.data.message];
                        }
                        
                        // Only show modal if we have errors
                        if (errorList.length > 0) {
                            // Show error modal
                            showErrorModal(errorList);
                        } else {
                            // Fallback: show a simple error message
                            alert('An error occurred while importing the file. Please check the console for details.');
                            console.error('No errors found in response:', result.data);
                        }
                        
                        importBtn.disabled = false;
                        importBtn.innerHTML = originalButtonText;
                    }
                } else {
                    // No data returned
                    showErrorModal(['Unexpected response from server. Please try again.']);
                    importBtn.disabled = false;
                    importBtn.innerHTML = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Upload Error:', error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack,
                    name: error.name
                });
                
                let errorMsg = 'Failed to upload file. ';
                
                // Provide more specific error messages
                if (error.message) {
                    if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                        errorMsg = 'Network error: Could not connect to server. Please check your internet connection and try again.';
                    } else if (error.message.includes('Server error')) {
                        errorMsg = error.message;
                    } else if (error.message.includes('timeout') || error.message.includes('Timeout')) {
                        errorMsg = 'Upload timeout: The file is too large or the server is taking too long to respond. Please try a smaller file or contact support.';
                    } else {
                        errorMsg += error.message;
                    }
                } else {
                    errorMsg += 'Please check your file and try again. If the problem persists, the file may be corrupted or too large.';
                }
                
                showErrorModal([errorMsg]);
                importBtn.disabled = false;
                importBtn.innerHTML = originalButtonText;
            });
        });

        // Handle update form submission
        document.getElementById('updateForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const fileInput = document.getElementById('update_file');
            const updateBtn = document.getElementById('updateBtn');

            if (ensureFileSelected(form, fileInput, updateBtn)) {
                return;
            }

            const file = fileInput.files[0];
            const maxSize = 20 * 1024 * 1024; // 20MB
            if (file.size > maxSize) {
                showErrorModal([`File size exceeds the maximum allowed size of 20MB. Your file is ${(file.size / (1024 * 1024)).toFixed(2)}MB.`]);
                return;
            }

            const formData = new FormData();
            formData.append('update_file', file);

            const formCsrf = form.querySelector('input[name="_token"]');
            const globalCsrf = document.querySelector('meta[name="csrf-token"]');
            const csrfValue = formCsrf ? formCsrf.value : (globalCsrf ? globalCsrf.content : '');
            if (formCsrf) {
                formData.append('_token', formCsrf.value);
            } else if (globalCsrf) {
                formData.append('_token', globalCsrf.content);
            }

            updateBtn.disabled = true;
            const originalButtonText = updateBtn.innerHTML;
            updateBtn.innerHTML = '<svg class="animate-spin h-4 w-4 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Updating...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfValue
                }
            })
            .then(response => {
                if (!response.ok && response.status !== 422 && response.status !== 500) {
                    return response.text().then(text => {
                        throw new Error(`Server error: ${response.status} - ${response.statusText}`);
                    });
                }

                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        return { status: response.status, data: data };
                    }).catch(() => response.text().then(text => {
                        return { status: response.status, data: { success: false, message: text || 'Invalid response from server' } };
                    }));
                }

                return response.text().then(text => {
                    if (response.redirected || (response.ok && response.status >= 200 && response.status < 300)) {
                        return { status: response.status, redirected: true, text: text };
                    }
                    return { status: response.status, data: { success: false, message: text || 'An error occurred' } };
                });
            })
            .then(result => {
                if (result.redirected) {
                    showSuccessPopup('Shipping charges updated successfully!');
                    form.reset();
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.shipping-charges.all") }}';
                    }, 1500);
                } else if (result.data) {
                    if (result.data.success) {
                        const message = result.data.message || `Successfully updated ${result.data.updated_count || 0} shipping charge(s)!`;
                        showSuccessPopup(message);
                        form.reset();
                        setTimeout(() => {
                            if (result.data.redirect) {
                                window.location.href = result.data.redirect;
                            } else {
                                window.location.href = '{{ route("admin.shipping-charges.all") }}';
                            }
                        }, 1500);
                    } else {
                        let errorList = [];
                        if (result.data.errors) {
                            if (Array.isArray(result.data.errors)) {
                                errorList = result.data.errors;
                            } else if (typeof result.data.errors === 'object') {
                                Object.keys(result.data.errors).forEach(key => {
                                    const fieldErrors = Array.isArray(result.data.errors[key])
                                        ? result.data.errors[key]
                                        : [result.data.errors[key]];
                                    fieldErrors.forEach(err => errorList.push(err));
                                });
                            } else if (typeof result.data.errors === 'string') {
                                errorList = [result.data.errors];
                            }
                        }

                        if (errorList.length === 0 && result.data.message) {
                            errorList = [result.data.message];
                        }

                        if (errorList.length > 0) {
                            showErrorModal(errorList);
                        } else {
                            alert('An error occurred while updating shipping charges. Please check the console for details.');
                            console.error('Update error response:', result.data);
                        }

                        updateBtn.disabled = false;
                        updateBtn.innerHTML = originalButtonText;
                    }
                } else {
                    showErrorModal(['Unexpected response from server. Please try again.']);
                    updateBtn.disabled = false;
                    updateBtn.innerHTML = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Update Upload Error:', error);
                let errorMsg = 'Failed to upload file. ';

                if (error.message) {
                    if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                        errorMsg = 'Network error: Could not connect to server. Please check your internet connection and try again.';
                    } else if (error.message.includes('Server error')) {
                        errorMsg = error.message;
                    } else if (error.message.includes('timeout') || error.message.includes('Timeout')) {
                        errorMsg = 'Upload timeout: The file is too large or the server is taking too long to respond. Please try a smaller file or contact support.';
                    } else {
                        errorMsg += error.message;
                    }
                } else {
                    errorMsg += 'Please check your file and try again. If the problem persists, the file may be corrupted or too large.';
                }

                showErrorModal([errorMsg]);
                updateBtn.disabled = false;
                updateBtn.innerHTML = originalButtonText;
            });
        });
    </script>
@endsection



