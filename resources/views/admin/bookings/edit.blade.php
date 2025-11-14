@extends('layouts.admin')

@section('title', 'Edit Booking')

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
        .form-input[readonly] {
            background: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
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
    </style>

    <!-- Success Popup -->
    <div id="success-popup" class="success-popup" style="display: none;">
        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-green-700 font-semibold text-sm" id="success-message">Booking updated successfully!</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Edit Booking</h1>
                    <p class="text-xs text-gray-600">Update Booking - {{ $booking['awb_no'] }}</p>
                </div>
            </div>
            <a href="{{ route('admin.bookings.all') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to All
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Booking Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Update Booking Information
                </h2>

                <form id="bookingForm" method="POST" action="{{ route('admin.bookings.update', $booking['id']) }}">
                    @csrf
                    @method('PUT')

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 rounded-xl flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <p class="text-red-700 font-bold text-sm">{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Current Booking Date (Automatic) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Current Booking Date <span class="required">*</span>
                        </label>
                        <input type="date" name="current_booking_date" id="current_booking_date" class="form-input" value="{{ $booking['current_booking_date'] }}" readonly>
                        <p class="text-xs text-gray-500 mt-1">Automatically set to booking date</p>
                    </div>

                    <!-- AWB No. (Dropdown) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            AWB No. <span class="required">**</span>
                        </label>
                        <select name="awb_no" id="awb_no" class="form-select" required>
                            <option value="">Select AWB No.</option>
                            @foreach($awbUploads as $awb)
                                <option value="{{ $awb['awb_no'] }}" {{ $booking['awb_no'] == $awb['awb_no'] ? 'selected' : '' }}>{{ $awb['awb_no'] }} - {{ $awb['destination'] }}</option>
                            @endforeach
                        </select>
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
                                <option value="{{ $type }}" {{ $booking['shipment_type'] == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Booking Type & Date of Sale -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Booking Type
                            </label>
                            <select name="booking_type" id="booking_type" class="form-select">
                                <option value="">Select Booking Type</option>
                                @foreach($bookingTypes as $type)
                                    <option value="{{ $type }}" {{ $booking['booking_type'] == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Date of Sale
                            </label>
                            <input type="date" name="date_of_sale" id="date_of_sale" class="form-input" value="{{ $booking['date_of_sale'] }}">
                        </div>
                    </div>

                    <!-- Consignee Name -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Consignee Name
                        </label>
                        <input type="text" name="consignee_name" id="consignee_name" class="form-input" value="{{ $booking['consignee_name'] }}" placeholder="Consignee Name">
                    </div>

                    <!-- Origin & Destination -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Origin <span class="required">**</span>
                            </label>
                            <select name="origin" id="origin" class="form-select" required>
                                <option value="">Select Origin</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country['name'] }}" {{ $booking['origin'] == $country['name'] ? 'selected' : '' }}>{{ $country['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
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
                                    <option value="{{ $country['name'] }}" {{ $booking['destination'] == $country['name'] ? 'selected' : '' }}>{{ $country['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Origin Pin & Destination Pin -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Origin Pin
                            </label>
                            <select name="origin_pin" id="origin_pin" class="form-select">
                                <option value="">Select Origin Country First</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Destination Pin <span class="required">*</span>
                            </label>
                            <select name="destination_pin" id="destination_pin" class="form-select" required>
                                <option value="">Select Destination Country First</option>
                            </select>
                        </div>
                    </div>

                    <!-- Chr Weight & Pieces -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                                Chr Weight (KG) <span class="required">*</span>
                            </label>
                            <input type="number" name="chr_weight" id="chr_weight" class="form-input" step="0.01" min="0" value="{{ $booking['chr_weight'] }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Pieces
                            </label>
                            <input type="number" name="pieces" id="pieces" class="form-input" min="1" value="{{ $booking['pieces'] }}">
                        </div>
                    </div>

                    <!-- Booking Amount (Auto Calculated) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Booking Amount (Auto Calculated) <span class="required">*</span>
                        </label>
                        <input type="number" name="booking_amount" id="booking_amount" class="form-input" step="0.01" min="0" value="{{ $booking['booking_amount'] }}" required>
                        <p class="text-xs text-gray-500 mt-1">Automatically calculated based on weight and destination</p>
                    </div>

                    <!-- Network Selection -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            Network
                        </label>
                        <select name="network" id="network" class="form-select">
                            <option value="">Select Network</option>
                            @if(isset($networks) && is_array($networks))
                                @foreach($networks as $network)
                                    <option value="{{ $network['name'] ?? '' }}" 
                                        {{ (isset($booking['network']) && $booking['network'] == ($network['name'] ?? '')) ? 'selected' : '' }}>
                                        {{ $network['name'] ?? '' }} 
                                        @if(isset($network['type']))
                                            ({{ $network['type'] }})
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select the network for this booking. Price changes will be credited/debited to this network.</p>
                    </div>

                    <!-- Forwarding Service, V.AWB, F.AWB -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Forwarding Service
                            </label>
                            <input type="text" name="forwarding_service" id="forwarding_service" class="form-input" value="{{ $booking['forwarding_service'] }}" placeholder="e.g., FedEx">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                V.AWB
                            </label>
                            <input type="text" name="v_awb" id="v_awb" class="form-input" value="{{ $booking['v_awb'] }}" placeholder="V123456789">
                        </div>
                    </div>

                    <!-- F.AWB -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            F.AWB
                        </label>
                        <input type="text" name="f_awb" id="f_awb" class="form-input" value="{{ $booking['f_awb'] }}" placeholder="F987654321">
                    </div>

                    <!-- Dummy Number -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            Dummy Number
                        </label>
                        <input type="text" name="dummy_number" id="dummy_number" class="form-input" value="{{ $booking['dummy_number'] ?? '' }}" placeholder="Enter dummy number">
                    </div>

                    <!-- Remark 1-7 -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remarks (1-7)
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="remark_1" id="remark_1" class="form-input" value="{{ $booking['remark_1'] }}" placeholder="Remark 1">
                            <input type="text" name="remark_2" id="remark_2" class="form-input" value="{{ $booking['remark_2'] }}" placeholder="Remark 2">
                            <input type="text" name="remark_3" id="remark_3" class="form-input" value="{{ $booking['remark_3'] }}" placeholder="Remark 3">
                            <input type="text" name="remark_4" id="remark_4" class="form-input" value="{{ $booking['remark_4'] }}" placeholder="Remark 4">
                            <input type="text" name="remark_5" id="remark_5" class="form-input" value="{{ $booking['remark_5'] }}" placeholder="Remark 5">
                            <input type="text" name="remark_6" id="remark_6" class="form-input" value="{{ $booking['remark_6'] }}" placeholder="Remark 6">
                            <input type="text" name="remark_7" id="remark_7" class="form-input md:col-span-2" value="{{ $booking['remark_7'] }}" placeholder="Remark 7">
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Update Booking</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.bookings.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
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
                    Booking Details
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Booking ID</p>
                        <p class="text-sm font-bold text-gray-900">#{{ $booking['id'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Booking Date</p>
                        <p class="text-sm font-bold text-gray-900">{{ $booking['current_booking_date'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">AWB No.</p>
                        <p class="text-sm font-bold text-orange-600">{{ $booking['awb_no'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Route</p>
                        <p class="text-sm font-bold text-gray-900">{{ $booking['origin'] }} → {{ $booking['destination'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Weight & Pieces</p>
                        <p class="text-sm font-bold text-gray-900">{{ number_format($booking['chr_weight'], 2) }} KG / {{ $booking['pieces'] }} pcs</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Booking Amount</p>
                        <p class="text-sm font-bold text-green-600">₹{{ number_format($booking['booking_amount'], 2) }}</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.bookings.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        Back to All Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // API endpoint for fetching pincodes by country
        const pincodeApiUrl = '{{ route("admin.api.pincodes-by-country") }}';
        const currentOriginPin = '{{ $booking["origin_pin"] ?? "" }}';
        const currentDestinationPin = '{{ $booking["destination_pin"] ?? "" }}';

        // Update pincode options based on country selection
        async function updatePincodes(selectElement, country, selectedValue = '') {
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
                        if (selectedValue && item.pincode == selectedValue) {
                            option.selected = true;
                        }
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

        // Origin country change
        document.getElementById('origin')?.addEventListener('change', function() {
            updatePincodes(document.getElementById('origin_pin'), this.value, currentOriginPin);
        });

        // Destination country change
        document.getElementById('destination')?.addEventListener('change', function() {
            updatePincodes(document.getElementById('destination_pin'), this.value, currentDestinationPin);
        });

        // Load pincodes on page load if countries are already selected
        document.addEventListener('DOMContentLoaded', function() {
            const originCountry = document.getElementById('origin')?.value;
            const destinationCountry = document.getElementById('destination')?.value;
            
            if (originCountry) {
                updatePincodes(document.getElementById('origin_pin'), originCountry, currentOriginPin);
            }
            if (destinationCountry) {
                updatePincodes(document.getElementById('destination_pin'), destinationCountry, currentDestinationPin);
            }
        });

        // Auto-calculate booking amount based on chr_weight
        document.getElementById('chr_weight')?.addEventListener('input', function() {
            const chrWeight = parseFloat(this.value) || 0;
            const bookingAmount = chrWeight * 250; // Sample calculation (250 per KG)
            document.getElementById('booking_amount').value = bookingAmount.toFixed(2);
        });

        // Form submission with AJAX
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            
            // Validate required fields first
            const awbNo = document.getElementById('awb_no').value;
            const shipmentType = document.getElementById('shipment_type').value;
            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;
            const destinationPin = document.getElementById('destination_pin').value;
            const chrWeight = document.getElementById('chr_weight').value;
            const bookingAmount = document.getElementById('booking_amount').value;
            
            if (!awbNo || !shipmentType || !origin || !destination || !destinationPin || !chrWeight || !bookingAmount) {
                alert('Please fill in all required fields.');
                return;
            }
            
            const formData = new FormData(form);
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Disable submit button
            submitButton.disabled = true;
            submitButton.innerHTML = '<div class="flex items-center justify-center gap-2"><svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Updating...</span></div>';
            
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
                    showSuccessPopup('Booking updated successfully!');
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.bookings.all") }}';
                    }, 1500);
                } else if (result.data) {
                    if (result.data.success) {
                        showSuccessPopup(result.data.message || 'Booking updated successfully!');
                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = result.data.redirect || '{{ route("admin.bookings.all") }}';
                        }, 1500);
                    } else {
                        // Handle validation errors
                        if (result.data.errors) {
                            let errorMessages = Object.values(result.data.errors).flat().join('\n');
                            alert('Validation errors:\n' + errorMessages);
                            console.error('Validation errors:', result.data.errors);
                        } else {
                            alert(result.data.message || 'An error occurred while updating the booking.');
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
                alert('An error occurred while updating the booking. Please check the console for details.');
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
    </script>
@endsection



