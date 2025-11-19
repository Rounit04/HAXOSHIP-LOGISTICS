@extends('layouts.admin')

@section('title', 'Edit AWB Upload')

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
    </style>

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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Edit AWB Upload</h1>
                    <p class="text-xs text-gray-600">Update AWB Upload - {{ $upload['awb_no'] }}</p>
                </div>
            </div>
            <a href="{{ route('admin.awb-upload.all') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to All
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- AWB Upload Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Update AWB Upload Information
                </h2>

                <form id="awbUploadForm" method="POST" action="{{ route('admin.awb-upload.update', $upload['id']) }}">
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

                    <!-- Branch & Hub -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Branch <span class="required">*</span>
                            </label>
                            <input type="text" name="branch" id="branch" class="form-input" value="{{ $upload['branch'] ?? '' }}" placeholder="Enter Branch" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253V4m0 0a8 8 0 018 8v5a3 3 0 01-3 3H7a3 3 0 01-3-3v-5a8 8 0 018-8z"/>
                                </svg>
                                Hub <span class="required">*</span>
                            </label>
                            <input type="text" name="hub" id="hub" class="form-input" value="{{ $upload['hub'] ?? '' }}" placeholder="Enter Hub" required>
                        </div>
                    </div>

                    <!-- AWB No. -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            AWB No. <span class="required">*</span>
                        </label>
                        <input type="text" name="awb_no" id="awb_no" class="form-input" value="{{ $upload['awb_no'] }}" placeholder="AWB123456789 or AWB-123_456" required>
                        <p class="text-xs text-gray-500 mt-1">AWB No. must be unique (no duplicates). Special characters are allowed.</p>
                    </div>

                    <!-- Type & Origin -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Type <span class="required">*</span>
                            </label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="domestic" {{ strcasecmp($upload['type'] ?? '', 'domestic') === 0 ? 'selected' : '' }}>Domestic</option>
                                <option value="international" {{ strcasecmp($upload['type'] ?? '', 'international') === 0 ? 'selected' : '' }}>International</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Origin <span class="required">*</span>
                            </label>
                            <select name="origin" id="origin" class="form-select" required>
                                <option value="">Select Origin</option>
                                @php
                                    $currentOrigin = trim($upload['origin'] ?? '');
                                @endphp
                                @foreach($countries as $country)
                                    @php
                                        $countryName = trim($country['name'] ?? '');
                                        $isSelected = !empty($currentOrigin) && strcasecmp($currentOrigin, $countryName) === 0;
                                    @endphp
                                    <option value="{{ $countryName }}" {{ $isSelected ? 'selected' : '' }}>{{ $countryName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Origin Zone & Origin Zone Pincode -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                Origin Zone <span class="required">*</span>
                            </label>
                            <select name="origin_zone" id="origin_zone" class="form-select" required>
                                <option value="">Select Origin Zone</option>
                                @if(!empty($upload['origin_zone']))
                                    <option value="{{ $upload['origin_zone'] }}" selected>{{ $upload['origin_zone'] }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Origin Zone Pincode <span class="required">*</span>
                            </label>
                            <select name="origin_zone_pincode" id="origin_zone_pincode" class="form-select" required>
                                <option value="">Select Origin Pincode</option>
                                @if(!empty($upload['origin_zone_pincode']))
                                    <option value="{{ $upload['origin_zone_pincode'] }}" selected>{{ $upload['origin_zone_pincode'] }}</option>
                                @endif
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
                                @php
                                    $currentDestination = trim($upload['destination'] ?? '');
                                @endphp
                                @foreach($countries as $country)
                                    @php
                                        $countryName = trim($country['name'] ?? '');
                                        $isSelected = !empty($currentDestination) && strcasecmp($currentDestination, $countryName) === 0;
                                    @endphp
                                    <option value="{{ $countryName }}" {{ $isSelected ? 'selected' : '' }}>{{ $countryName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                Destination Zone <span class="required">*</span>
                            </label>
                            <select name="destination_zone" id="destination_zone" class="form-select" required>
                                <option value="">Select Destination Zone</option>
                                @if(!empty($upload['destination_zone']))
                                    <option value="{{ $upload['destination_zone'] }}" selected>{{ $upload['destination_zone'] }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Destination Zone Pincode & Reference No -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Destination Zone Pincode <span class="required">*</span>
                            </label>
                            <select name="destination_zone_pincode" id="destination_zone_pincode" class="form-select" required>
                                <option value="">Select Destination Pincode</option>
                                @if(!empty($upload['destination_zone_pincode']))
                                    <option value="{{ $upload['destination_zone_pincode'] }}" selected>{{ $upload['destination_zone_pincode'] }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Reference No
                            </label>
                            <input type="text" name="reference_no" id="reference_no" class="form-input" value="{{ $upload['reference_no'] ?? '' }}" placeholder="e.g., AWB-972">
                        </div>
                    </div>

                    <!-- Date of Sale (DOS) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            DOS (Date of Sale)
                        </label>
                        <input type="date" name="date_of_sale" id="date_of_sale" class="form-input" value="{{ isset($upload['date_of_sale']) && $upload['date_of_sale'] ? (\Carbon\Carbon::parse($upload['date_of_sale'])->format('Y-m-d') ?? $upload['date_of_sale']) : '' }}">
                    </div>

                    <!-- Non-Commercial & Consignor -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Non-Commercial
                            </label>
                            <select name="non_commercial" id="non_commercial" class="form-select">
                                <option value="">Select</option>
                                <option value="Yes" {{ ($upload['non_commercial'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ ($upload['non_commercial'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Consignor <span class="required">*</span>
                            </label>
                            <input type="text" name="consignor" id="consignor" class="form-input" value="{{ $upload['consignor'] ?? '' }}" placeholder="Consignor Name" required>
                        </div>
                    </div>

                    <!-- Consignor Attn & Consignee -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Consignor Attn <span class="required">*</span>
                            </label>
                            <input type="text" name="consignor_attn" id="consignor_attn" class="form-input" value="{{ $upload['consignor_attn'] ?? '' }}" placeholder="Consignor Attention" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Consignee <span class="required">*</span>
                            </label>
                            <input type="text" name="consignee" id="consignee" class="form-input" value="{{ $upload['consignee'] ?? $upload['consignee_name'] ?? '' }}" placeholder="Consignee Name" required>
                        </div>
                    </div>

                    <!-- Consignee Attn -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Consignee Attn <span class="required">*</span>
                        </label>
                        <input type="text" name="consignee_attn" id="consignee_attn" class="form-input" value="{{ $upload['consignee_attn'] ?? '' }}" placeholder="Consignee Attention" required>
                    </div>

                    <!-- Goods Type -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Goods Type
                        </label>
                        <input type="text" name="goods_type" id="goods_type" class="form-input" value="{{ $upload['goods_type'] ?? '' }}" placeholder="e.g., Electronics, Documents">
                    </div>

                    <!-- PKC (Pieces) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            PKC (Pieces) <span class="required">*</span>
                        </label>
                        <input type="number" name="pk" id="pk" class="form-input" min="1" value="{{ $upload['pk'] ?? 1 }}" required>
                    </div>

                    <!-- Actual Wt. & Volumetric Wt. -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                                Actual Wt. (KG) <span class="required">*</span>
                            </label>
                            <input type="number" name="actual_weight" id="actual_weight" class="form-input" step="0.01" min="0" value="{{ $upload['actual_weight'] ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                                Volumetric Wt. (KG) <span class="required">*</span>
                            </label>
                            <input type="number" name="volumetric_weight" id="volumetric_weight" class="form-input" step="0.01" min="0" value="{{ $upload['volumetric_weight'] ?? '' }}" required>
                        </div>
                    </div>

                    <!-- Chargeable Wt. & Network Name -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                                Chargeable Wt. (KG) <span class="required">*</span>
                            </label>
                            <input type="text" name="chargeable_weight" id="chargeable_weight" class="form-input" value="{{ $upload['chargeable_weight'] ?? '' }}" placeholder="e.g., 0.22 D1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                </svg>
                                Network Name <span class="required">*</span>
                            </label>
                            <select name="network_name" id="network_name" class="form-select" required>
                                <option value="">Select Network</option>
                                @php
                                    $currentNetwork = trim($upload['network_name'] ?? '');
                                @endphp
                                @foreach($networks as $network)
                                    @php
                                        $isNetworkSelected = !empty($currentNetwork) && strcasecmp($currentNetwork, trim($network['name'] ?? '')) === 0;
                                    @endphp
                                    <option value="{{ $network['name'] }}" {{ $isNetworkSelected ? 'selected' : '' }}>{{ $network['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Service Name & Amount -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Service Name <span class="required">*</span>
                            </label>
                            <select name="service_name" id="service_name" class="form-select" required>
                                <option value="">Select Service</option>
                                @php
                                    $currentService = trim($upload['service_name'] ?? '');
                                @endphp
                                @foreach($services as $service)
                                    @php
                                        $isServiceSelected = !empty($currentService) && strcasecmp($currentService, trim($service['name'] ?? '')) === 0;
                                    @endphp
                                    <option value="{{ $service['name'] }}" {{ $isServiceSelected ? 'selected' : '' }}>{{ $service['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Amount <span class="required">*</span>
                            </label>
                            <input type="number" name="amour" id="amour" class="form-input" step="0.01" min="0" value="{{ $upload['amour'] ?? '' }}" required>
                        </div>
                    </div>

                    <!-- Medical Shipment & Invoice Value -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                                Medical Shipment
                            </label>
                            <select name="medical_shipment" id="medical_shipment" class="form-select">
                                <option value="">Select</option>
                                <option value="Yes" {{ ($upload['medical_shipment'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ ($upload['medical_shipment'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Invoice Value
                            </label>
                            <input type="number" name="invoice_value" id="invoice_value" class="form-input" step="0.01" min="0" value="{{ $upload['invoice_value'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Invoice Date & is_coc -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Invoice Date
                            </label>
                            <input type="date" name="invoice_date" id="invoice_date" class="form-input" value="{{ isset($upload['invoice_date']) && $upload['invoice_date'] ? (\Carbon\Carbon::parse($upload['invoice_date'])->format('Y-m-d') ?? $upload['invoice_date']) : '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                is_coc
                            </label>
                            <select name="is_coc" id="is_coc" class="form-select">
                                <option value="0" {{ ($upload['is_coc'] ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ ($upload['is_coc'] ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>

                    <!-- cod_amount & Clearance Required -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                cod_amount
                            </label>
                            <input type="number" name="cod_amount" id="cod_amount" class="form-input" step="0.01" min="0" value="{{ $upload['cod_amount'] ?? 0 }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Clearance Required
                            </label>
                            <select name="clearance_required" id="clearance_required" class="form-select">
                                <option value="">Select</option>
                                <option value="Yes" {{ ($upload['clearance_required'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ ($upload['clearance_required'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Clearance Remark -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Clearance Remark
                        </label>
                        <input type="text" name="clearance_remark" id="clearance_remark" class="form-input" value="{{ $upload['clearance_remark'] ?? '' }}" placeholder="Enter clearance remark">
                    </div>

                    <!-- Status & payment_deduct -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Status <span class="required">*</span>
                            </label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="">Select Status</option>
                                <option value="publish" {{ strcasecmp($upload['status'] ?? '', 'publish') === 0 ? 'selected' : '' }}>Publish</option>
                                <option value="Booked" {{ strcasecmp($upload['status'] ?? '', 'Booked') === 0 ? 'selected' : '' }}>Booked</option>
                                <option value="RTO" {{ strcasecmp($upload['status'] ?? '', 'RTO') === 0 ? 'selected' : '' }}>RTO</option>
                                <option value="Cancelled" {{ strcasecmp($upload['status'] ?? '', 'Cancelled') === 0 ? 'selected' : '' }}>Cancelled</option>
                                <option value="Delivered" {{ strcasecmp($upload['status'] ?? '', 'Delivered') === 0 ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                payment_deduct
                            </label>
                            <select name="payment_deduct" id="payment_deduct" class="form-select">
                                <option value="">Select</option>
                                <option value="Yes" {{ ($upload['payment_deduct'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ ($upload['payment_deduct'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Location & Forwarding Service -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Location
                            </label>
                            <input type="text" name="location" id="location" class="form-input" value="{{ $upload['location'] ?? '' }}" placeholder="e.g., transit, Ex-Delhi">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Forwarding Service
                            </label>
                            <input type="text" name="forwarding_service" id="forwarding_service" class="form-input" value="{{ $upload['forwarding_service'] ?? '' }}" placeholder="e.g., EKART, Delhivery, DHL">
                        </div>
                    </div>

                    <!-- Forward Number -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            Forward Number
                        </label>
                        <input type="text" name="forwarding_number" id="forwarding_number" class="form-input" value="{{ $upload['forwarding_number'] ?? '' }}" placeholder="Enter Forward Number">
                    </div>

                    <!-- Transfer By & Transfer On -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Transfer By
                            </label>
                            <input type="text" name="transfer" id="transfer" class="form-input" value="{{ $upload['transfer'] ?? '' }}" placeholder="Enter transfer by">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Transfer On
                            </label>
                            <input type="date" name="transfer_on" id="transfer_on" class="form-input" value="{{ isset($upload['transfer_on']) && $upload['transfer_on'] ? (\Carbon\Carbon::parse($upload['transfer_on'])->format('Y-m-d') ?? $upload['transfer_on']) : '' }}">
                        </div>
                    </div>

                    <!-- Remark 1 -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remark 1
                        </label>
                        <input type="text" name="remark_1" id="remark_1" class="form-input" value="{{ $upload['remark_1'] ?? '' }}" placeholder="Remark 1">
                    </div>

                    <!-- Remark 2 -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remark 2
                        </label>
                        <input type="text" name="remark_2" id="remark_2" class="form-input" value="{{ $upload['remark_2'] ?? '' }}" placeholder="Remark 2">
                    </div>

                    <!-- Remark 3 -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remark 3
                        </label>
                        <input type="text" name="remark_3" id="remark_3" class="form-input" value="{{ $upload['remark_3'] ?? '' }}" placeholder="Remark 3">
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Update AWB Upload</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.awb-upload.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
                </form>

                @if(session('success'))
                    <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
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
                    Upload Details
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Upload ID</p>
                        <p class="text-sm font-bold text-gray-900">#{{ $upload['id'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">AWB No.</p>
                        <p class="text-sm font-bold text-orange-600">{{ $upload['awb_no'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Destination</p>
                        <p class="text-sm font-bold text-gray-900">{{ $upload['destination'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Shipment Type</p>
                        <p class="text-sm font-bold text-gray-900">{{ $upload['shipment_type'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Weight & Pieces</p>
                        <p class="text-sm font-bold text-gray-900">{{ number_format($upload['actual_weight'] ?? $upload['weight'] ?? 0, 2) }} KG / {{ $upload['pk'] ?? $upload['pieces'] ?? 0 }} pcs</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="status-badge {{ strtolower($upload['status']) }}">
                            {{ $upload['status'] }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.awb-upload.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        Back to All Uploads
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const networkSelect = document.getElementById('network_name');
            const serviceSelect = document.getElementById('service_name');
            const services = @json($services);

            // Origin fields
            const originSelect = document.getElementById('origin');
            const originZoneSelect = document.getElementById('origin_zone');
            const originPincodeSelect = document.getElementById('origin_zone_pincode');

            // Destination fields
            const destinationSelect = document.getElementById('destination');
            const destinationZoneSelect = document.getElementById('destination_zone');
            const destinationPincodeSelect = document.getElementById('destination_zone_pincode');

            // Store current values for pre-selection
            const currentOriginZone = '{{ $upload['origin_zone'] ?? '' }}';
            const currentOriginPincode = '{{ $upload['origin_zone_pincode'] ?? '' }}';
            const currentDestinationZone = '{{ $upload['destination_zone'] ?? '' }}';
            const currentDestinationPincode = '{{ $upload['destination_zone_pincode'] ?? '' }}';
            const currentService = '{{ $upload['service_name'] ?? '' }}';
            const currentNetwork = '{{ $upload['network_name'] ?? '' }}';

            function buildOption(value, label, extraClasses = '', disabled = false) {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = label;
                if (extraClasses) {
                    option.className = extraClasses;
                }
                if (disabled) {
                    option.disabled = true;
                }
                return option;
            }

            function populateServices() {
                if (!serviceSelect) {
                    return;
                }

                const selectedNetwork = (networkSelect?.value || '').trim();
                const currentServiceValue = currentService.trim();
                
                // Store current selection if it exists
                const wasSelected = serviceSelect.value === currentServiceValue;

                serviceSelect.innerHTML = '';
                serviceSelect.appendChild(buildOption('', 'Select Service'));

                // If no network is selected, don't show any services
                if (!selectedNetwork) {
                    return;
                }

                const filteredServices = (services || []).filter(service => {
                    const serviceNetwork = (service.network || '').trim().toLowerCase();
                    const isActive = (service.status || '').toLowerCase() === 'active';
                    const networkMatch = serviceNetwork === selectedNetwork.toLowerCase();

                    return isActive && networkMatch;
                });

                if (filteredServices.length === 0) {
                    serviceSelect.appendChild(
                        buildOption('', 'No services available for this network', 'text-gray-400', true)
                    );
                    serviceSelect.value = '';
                    return;
                }

                // Sort services alphabetically by name
                filteredServices.sort((a, b) => {
                    const nameA = (a.name || '').toLowerCase();
                    const nameB = (b.name || '').toLowerCase();
                    return nameA.localeCompare(nameB);
                });

                filteredServices.forEach(service => {
                    const option = buildOption(service.name || '', service.name || 'Unnamed Service');
                    if (wasSelected && service.name === currentServiceValue) {
                        option.selected = true;
                    }
                    serviceSelect.appendChild(option);
                });

                // Restore selection if it matches
                if (wasSelected && currentServiceValue) {
                    serviceSelect.value = currentServiceValue;
                }
            }

            // Function to fetch zones by country
            async function fetchZonesByCountry(country, zoneSelect, pincodeSelect, currentZone = '', currentPincode = '') {
                if (!country || !zoneSelect) return;

                zoneSelect.innerHTML = '';
                zoneSelect.appendChild(buildOption('', 'Select Zone'));
                pincodeSelect.innerHTML = '';
                pincodeSelect.appendChild(buildOption('', 'Select Pincode'));

                try {
                    const response = await fetch(`{{ route('admin.api.zones-by-country') }}?country=${encodeURIComponent(country)}`);
                    const data = await response.json();

                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(zone => {
                            const option = buildOption(zone, zone);
                            if (currentZone && zone === currentZone) {
                                option.selected = true;
                            }
                            zoneSelect.appendChild(option);
                        });

                        // If current zone is selected, fetch pincodes
                        if (currentZone) {
                            await fetchPincodesByZone(country, currentZone, pincodeSelect, currentPincode);
                        }
                    } else {
                        zoneSelect.appendChild(buildOption('', 'No zones found', 'text-gray-400', true));
                    }
                } catch (error) {
                    console.error('Error fetching zones:', error);
                    zoneSelect.appendChild(buildOption('', 'Error loading zones', 'text-gray-400', true));
                }
            }

            // Function to fetch pincodes by zone
            async function fetchPincodesByZone(country, zone, pincodeSelect, currentPincode = '') {
                if (!country || !zone || !pincodeSelect) return;

                pincodeSelect.innerHTML = '';
                pincodeSelect.appendChild(buildOption('', 'Select Pincode'));

                try {
                    const response = await fetch(`{{ route('admin.api.pincodes-by-zone') }}?country=${encodeURIComponent(country)}&zone=${encodeURIComponent(zone)}`);
                    const data = await response.json();

                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(pincode => {
                            const option = buildOption(pincode, pincode);
                            if (currentPincode && pincode === currentPincode) {
                                option.selected = true;
                            }
                            pincodeSelect.appendChild(option);
                        });
                    } else {
                        pincodeSelect.appendChild(buildOption('', 'No pincodes found', 'text-gray-400', true));
                    }
                } catch (error) {
                    console.error('Error fetching pincodes:', error);
                    pincodeSelect.appendChild(buildOption('', 'Error loading pincodes', 'text-gray-400', true));
                }
            }

            // Initialize zones and pincodes on page load
            if (originSelect && originSelect.value && currentOriginZone) {
                fetchZonesByCountry(originSelect.value, originZoneSelect, originPincodeSelect, currentOriginZone, currentOriginPincode);
            }

            if (destinationSelect && destinationSelect.value && currentDestinationZone) {
                fetchZonesByCountry(destinationSelect.value, destinationZoneSelect, destinationPincodeSelect, currentDestinationZone, currentDestinationPincode);
            }

            // Origin country change handler
            if (originSelect) {
                originSelect.addEventListener('change', function() {
                    const country = this.value;
                    if (country) {
                        fetchZonesByCountry(country, originZoneSelect, originPincodeSelect);
                    } else {
                        originZoneSelect.innerHTML = '';
                        originZoneSelect.appendChild(buildOption('', 'Select Origin Zone'));
                        originPincodeSelect.innerHTML = '';
                        originPincodeSelect.appendChild(buildOption('', 'Select Origin Pincode'));
                    }
                });
            }

            // Origin zone change handler
            if (originZoneSelect) {
                originZoneSelect.addEventListener('change', function() {
                    const country = originSelect?.value;
                    const zone = this.value;
                    if (country && zone) {
                        fetchPincodesByZone(country, zone, originPincodeSelect);
                    } else {
                        originPincodeSelect.innerHTML = '';
                        originPincodeSelect.appendChild(buildOption('', 'Select Origin Pincode'));
                    }
                });
            }

            // Destination country change handler
            if (destinationSelect) {
                destinationSelect.addEventListener('change', function() {
                    const country = this.value;
                    if (country) {
                        fetchZonesByCountry(country, destinationZoneSelect, destinationPincodeSelect);
                    } else {
                        destinationZoneSelect.innerHTML = '';
                        destinationZoneSelect.appendChild(buildOption('', 'Select Destination Zone'));
                        destinationPincodeSelect.innerHTML = '';
                        destinationPincodeSelect.appendChild(buildOption('', 'Select Destination Pincode'));
                    }
                });
            }

            // Destination zone change handler
            if (destinationZoneSelect) {
                destinationZoneSelect.addEventListener('change', function() {
                    const country = destinationSelect?.value;
                    const zone = this.value;
                    if (country && zone) {
                        fetchPincodesByZone(country, zone, destinationPincodeSelect);
                    } else {
                        destinationPincodeSelect.innerHTML = '';
                        destinationPincodeSelect.appendChild(buildOption('', 'Select Destination Pincode'));
                    }
                });
            }

            if (networkSelect) {
                networkSelect.addEventListener('change', () => {
                    populateServices();
                    if (serviceSelect) {
                        serviceSelect.value = '';
                    }
                });
            }

            // Initialize services on page load
            populateServices();
        });
    </script>
@endsection




