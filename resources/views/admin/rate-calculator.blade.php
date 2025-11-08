@extends('layouts.admin')

@section('title', 'Rate Calculator')

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
        .calculator-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
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
        .shipment-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 8px;
        }
        .shipment-type-card {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .shipment-type-card:hover {
            border-color: #FF750F;
            background: linear-gradient(135deg, #fff5ed 0%, #fff5ed 100%);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(255, 117, 15, 0.15);
        }
        .shipment-type-card.selected {
            border-color: #FF750F;
            background: var(--admin-gradient);
            color: white;
            box-shadow: 0 2px 12px rgba(255, 117, 15, 0.3);
        }
        .shipment-type-card input[type="radio"] {
            display: none;
        }
        .shipment-type-card .icon {
            width: 24px;
            height: 24px;
            margin: 0 auto 6px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 117, 15, 0.1);
            transition: all 0.3s ease;
        }
        .shipment-type-card.selected .icon {
            background: rgba(255, 255, 255, 0.2);
        }
        .shipment-type-card .icon svg {
            width: 12px;
            height: 12px;
            color: #FF750F;
            transition: all 0.3s ease;
        }
        .shipment-type-card.selected .icon svg {
            color: white;
        }
        .shipment-type-card .name {
            font-weight: 600;
            font-size: 13px;
            color: #374151;
            transition: all 0.3s ease;
        }
        .shipment-type-card.selected .name {
            color: white;
        }
        .result-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
            border: 2px solid #FF750F;
            border-radius: 16px;
            padding: 32px;
            margin-top: 32px;
            display: none;
        }
        .result-card.show {
            display: block;
            animation: slideUp 0.4s ease;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .rate-amount {
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, #FF750F 0%, #5a52ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .breakdown-item {
            padding: 16px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            margin-bottom: 12px;
        }
        .loading-spinner {
            display: none;
        }
        .loading-spinner.show {
            display: inline-block;
        }
        .matching-charges-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .matching-charges-table thead {
            background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
            color: white;
        }
        .matching-charges-table th {
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .matching-charges-table td {
            padding: 12px;
            font-size: 13px;
            border-bottom: 1px solid #e5e7eb;
        }
        .matching-charges-table tbody tr:hover {
            background-color: #fff5ed;
        }
        .matching-charges-table tbody tr:last-child td {
            border-bottom: none;
        }
        .network-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }
        .service-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .rate-price {
            font-weight: 700;
            color: #FF750F;
            font-size: 14px;
        }
        .no-charges-message {
            padding: 24px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            background: #f9fafb;
            border-radius: 10px;
            border: 1px dashed #d1d5db;
        }
        .formula-item {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 12px;
        }
        .formula-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .formula-name {
            font-weight: 700;
            color: #92400e;
            font-size: 14px;
        }
        .formula-priority {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: #fbbf24;
            color: #78350f;
        }
        .formula-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 8px;
            font-size: 12px;
            color: #78350f;
            margin-top: 8px;
        }
        .formula-charge {
            font-weight: 700;
            color: #FF750F;
            font-size: 16px;
            text-align: right;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Rate Calculator</h1>
                    <p class="text-xs text-gray-600">Calculate shipping rates based on origin, destination, and weight</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calculator Form -->
        <div class="lg:col-span-2">
            <div class="calculator-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Calculate Shipping Rate
                </h2>

                <form id="rateCalculatorForm">
                    @csrf

                    <!-- Shipment Type -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Shipment Type <span class="required">*</span>
                        </label>
                        <div class="shipment-type-grid">
                            @foreach($shipmentTypes as $type)
                                <label class="shipment-type-card">
                                    <input type="radio" name="shipment_type" value="{{ $type }}" required>
                                    <div class="icon">
                                        @if($type == 'Dox')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @elseif($type == 'Non-Dox')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        @elseif($type == 'Medicine')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                            </svg>
                                        @else
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="name">{{ $type }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Origin Country -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Origin Country <span class="required">*</span>
                        </label>
                        <select name="origin_country" id="origin_country" class="form-select" required>
                            <option value="">Select Origin Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Origin Pincode -->
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

                    <!-- Destination Country -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Destination Country <span class="required">*</span>
                        </label>
                        <select name="destination_country" id="destination_country" class="form-select" required>
                            <option value="">Select Destination Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Destination Pincode -->
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

                    <!-- Weight -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                            </svg>
                            Weight in Kg <span class="required">*</span>
                        </label>
                        <input type="number" name="weight" id="weight" step="0.1" min="0.1" class="form-input" placeholder="Enter weight in kilograms" required>
                    </div>

                    <!-- Calculate Button -->
                    <button type="submit" class="admin-btn-primary w-full py-3 text-sm font-semibold">
                        <div class="flex items-center justify-center gap-2">
                            <span class="loading-spinner">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="button-text">Calculate Rate</span>
                        </div>
                    </button>
                </form>

                <!-- Result Card -->
                <div id="resultCard" class="result-card">
                    <div class="text-center mb-6">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600 font-medium mb-2">Estimated Shipping Rate</p>
                        <div class="rate-amount" id="rateAmount">{{ currency(0) }}</div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Rate Breakdown</h3>
                        <div id="rateBreakdown">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Applied Formulas Section -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Applied Weight Charges (From Formulas)
                        </h3>
                        <div id="appliedFormulasContainer">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Matching Shipping Charges Section -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Available Networks & Services
                        </h3>
                        <div id="matchingChargesContainer">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Sidebar -->
        <div class="lg:col-span-1">
            <div class="calculator-card p-5 sticky top-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    How It Works
                </h3>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-6 h-6 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-orange-600 font-bold text-xs">1</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Select Shipment Type</h4>
                            <p class="text-xs text-gray-600">Choose the type of shipment (Dox, Non-Dox, Medicine, or Special)</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-orange-600 font-bold text-xs">2</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Enter Origin & Destination</h4>
                            <p class="text-xs text-gray-600">Select the origin and destination countries and pincodes</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-orange-600 font-bold text-xs">3</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Add Weight</h4>
                            <p class="text-xs text-gray-600">Enter the weight of your shipment in kilograms</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-orange-600 font-bold text-xs">4</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Calculate</h4>
                            <p class="text-xs text-gray-600">Click calculate to get instant rate estimation</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Rate Factors</h4>
                    <div class="space-y-2 text-xs text-gray-600">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-600"></div>
                            <span>Shipment type affects base rate</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-600"></div>
                            <span>Weight determines additional charges</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-600"></div>
                            <span>Distance impacts final pricing</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pincode data
        const pincodeData = @json($pincodes);

        // Update pincode options based on country selection
        function updatePincodes(selectElement, country) {
            const pincodes = pincodeData[country] || [];
            selectElement.innerHTML = '<option value="">Select Pincode</option>';
            pincodes.forEach(pincode => {
                const option = document.createElement('option');
                option.value = pincode;
                option.textContent = pincode;
                selectElement.appendChild(option);
            });
        }

        // Origin country change
        document.getElementById('origin_country').addEventListener('change', function() {
            updatePincodes(document.getElementById('origin_pincode'), this.value);
        });

        // Destination country change
        document.getElementById('destination_country').addEventListener('change', function() {
            updatePincodes(document.getElementById('destination_pincode'), this.value);
        });

        // Shipment type selection
        document.querySelectorAll('input[name="shipment_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.shipment-type-card').forEach(card => {
                    card.classList.remove('selected');
                });
                if (this.checked) {
                    this.closest('.shipment-type-card').classList.add('selected');
                }
            });
        });

        // Form submission
        document.getElementById('rateCalculatorForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const buttonText = submitButton.querySelector('.button-text');
            const loadingSpinner = submitButton.querySelector('.loading-spinner');
            const resultCard = document.getElementById('resultCard');
            
            // Show loading
            loadingSpinner.classList.add('show');
            buttonText.textContent = 'Calculating...';
            submitButton.disabled = true;
            
            try {
                const response = await fetch('{{ route("admin.rate-calculator.calculate") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Currency formatting function
                    const currencySymbol = '{{ currency_symbol() }}';
                    const formatCurrency = (amount) => {
                        return currencySymbol + parseFloat(amount).toFixed(2);
                    };
                    
                    // Update rate amount
                    document.getElementById('rateAmount').textContent = formatCurrency(data.rate);
                    
                    // Update breakdown
                    const breakdownHtml = `
                        <div class="breakdown-item">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Base Rate</span>
                                <span class="text-sm font-bold text-gray-900">${formatCurrency(data.breakdown.base_rate)}</span>
                            </div>
                        </div>
                        <div class="breakdown-item">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Weight Charge ${data.applied_formulas && data.applied_formulas.length > 0 ? '(From Formulas)' : ''}</span>
                                <span class="text-sm font-bold text-gray-900">${formatCurrency(data.breakdown.weight_charge)}</span>
                            </div>
                            ${data.applied_formulas && data.applied_formulas.length > 0 ? `
                                <div class="mt-2 text-xs text-gray-600">
                                    <span class="font-semibold">Applied ${data.applied_formulas.length} formula(s)</span>
                                </div>
                            ` : ''}
                        </div>
                        <div class="breakdown-item">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Distance Charge</span>
                                <span class="text-sm font-bold text-gray-900">${formatCurrency(data.breakdown.distance_charge)}</span>
                            </div>
                        </div>
                        <div class="breakdown-item bg-purple-50 border-purple-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-purple-700">Service Type</span>
                                <span class="text-sm font-bold text-purple-900">${data.breakdown.service_type}</span>
                            </div>
                        </div>
                    `;
                    document.getElementById('rateBreakdown').innerHTML = breakdownHtml;
                    
            // Display applied formulas
            const appliedFormulasContainer = document.getElementById('appliedFormulasContainer');
            if (data.applied_formulas && data.applied_formulas.length > 0) {
                let formulasHtml = '<div class="space-y-3">';
                
                // Group by network only (since formulas are matched by network)
                const groupedFormulas = {};
                data.applied_formulas.forEach(formula => {
                    const network = formula.network || 'Unknown';
                    if (!groupedFormulas[network]) {
                        groupedFormulas[network] = {
                            network: network,
                            formulas: [],
                            totalCharge: 0
                        };
                    }
                    groupedFormulas[network].formulas.push(formula);
                    groupedFormulas[network].totalCharge += parseFloat(formula.calculated_charge) || 0;
                });
                
                // Display each network group
                Object.values(groupedFormulas).forEach(group => {
                    formulasHtml += `
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="network-badge">${group.network}</span>
                                    <span class="text-sm text-gray-600">(${group.formulas.length} formula${group.formulas.length > 1 ? 's' : ''})</span>
                                </div>
                                <div class="text-sm font-bold text-blue-700">
                                    Total: ${formatCurrency(group.totalCharge)}
                                </div>
                            </div>
                            <div class="space-y-2">
                    `;
                            
                            group.formulas.forEach(formula => {
                                // Get weight from response breakdown or form
                                const weight = data.breakdown.weight || parseFloat(document.getElementById('weight').value) || 0;
                                let calculationDesc = '';
                                
                                if (formula.scope === 'per kg') {
                                    if (formula.type === 'Fixed') {
                                        calculationDesc = `${formatCurrency(formula.value)} × ${weight} kg = ${formatCurrency(formula.calculated_charge)}`;
                                    } else {
                                        const percentageAmount = (data.breakdown.base_rate * formula.value / 100);
                                        calculationDesc = `${formatCurrency(percentageAmount)} (${formula.value}% of ${formatCurrency(data.breakdown.base_rate)}) × ${weight} kg = ${formatCurrency(formula.calculated_charge)}`;
                                    }
                                } else {
                                    if (formula.type === 'Fixed') {
                                        calculationDesc = `Flat ${formatCurrency(formula.value)} = ${formatCurrency(formula.calculated_charge)}`;
                                    } else {
                                        calculationDesc = `${formula.value}% of ${formatCurrency(data.breakdown.base_rate)} = ${formatCurrency(formula.calculated_charge)}`;
                                    }
                                }
                                
                                formulasHtml += `
                                    <div class="formula-item">
                                        <div class="formula-header">
                                            <div class="flex items-center gap-2">
                                                <span class="formula-name">${formula.formula_name}</span>
                                                ${formula.service && formula.service !== 'N/A' ? `<span class="service-badge">${formula.service}</span>` : ''}
                                            </div>
                                            <span class="formula-priority">${formula.priority} Priority</span>
                                        </div>
                                        <div class="formula-details">
                                            <div>
                                                <span class="font-semibold">Type:</span> ${formula.type}
                                            </div>
                                            <div>
                                                <span class="font-semibold">Scope:</span> ${formula.scope}
                                            </div>
                                            <div>
                                                <span class="font-semibold">Value:</span> ${formula.value}${formula.type === 'Percentage' ? '%' : ''}
                                            </div>
                                            <div class="formula-charge">
                                                Charge: ${formatCurrency(formula.calculated_charge)}
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-600 italic">
                                            Calculation: ${calculationDesc}
                                        </div>
                                        ${formula.remark ? `<div class="mt-1 text-xs text-gray-500">${formula.remark}</div>` : ''}
                                    </div>
                                `;
                            });
                            
                            formulasHtml += `
                                    </div>
                                </div>
                            `;
                        });
                        
                        formulasHtml += '</div>';
                        appliedFormulasContainer.innerHTML = formulasHtml;
                    } else {
                        appliedFormulasContainer.innerHTML = `
                            <div class="no-charges-message">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <p>No formulas applied for weight charges.</p>
                                <p class="text-xs mt-1">Weight charges are calculated using default formula.</p>
                            </div>
                        `;
                    }
                    
                    // Display matching shipping charges
                    const matchingChargesContainer = document.getElementById('matchingChargesContainer');
                    if (data.matching_charges && data.matching_charges.length > 0) {
                        let chargesHtml = `
                            <div class="overflow-x-auto">
                                <table class="matching-charges-table">
                                    <thead>
                                        <tr>
                                            <th>Network</th>
                                            <th>Service</th>
                                            <th>Base Price</th>
                                            <th>Weight Range</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        
                        data.matching_charges.forEach(charge => {
                            chargesHtml += `
                                <tr>
                                    <td>
                                        <span class="network-badge">${charge.network || 'N/A'}</span>
                                    </td>
                                    <td>
                                        <span class="service-badge">${charge.service || 'N/A'}</span>
                                    </td>
                                    <td>
                                        <span class="rate-price">${formatCurrency(charge.rate || 0)}</span>
                                    </td>
                                    <td>
                                        <span class="text-xs text-gray-600">
                                            ${charge.min_weight || 0} - ${charge.max_weight || 0} kg
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-xs text-gray-600">${charge.remark || '-'}</span>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        chargesHtml += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                        matchingChargesContainer.innerHTML = chargesHtml;
                    } else {
                        matchingChargesContainer.innerHTML = `
                            <div class="no-charges-message">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p>No matching shipping charges found for this route.</p>
                                <p class="text-xs mt-1">Please check the Shipping Charges section to create charges for this route.</p>
                            </div>
                        `;
                    }
                    
                    // Show result card
                    resultCard.classList.add('show');
                    
                    // Scroll to result
                    resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while calculating the rate. Please try again.');
            } finally {
                // Hide loading
                loadingSpinner.classList.remove('show');
                buttonText.textContent = 'Calculate Rate';
                submitButton.disabled = false;
            }
        });
    </script>
@endsection



