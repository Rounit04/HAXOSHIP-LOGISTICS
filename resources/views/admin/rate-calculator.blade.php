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
        #toggleBreakdownBtn svg {
            transition: transform 0.3s ease;
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
                                <option value="{{ $country['name'] }}">
                                    {{ $country['name'] }} 
                                    @if(!empty($country['isd_no']))
                                        ({{ $country['isd_no'] }})
                                    @endif
                                    @if(!empty($country['code']))
                                        - {{ $country['code'] }}
                                    @endif
                                </option>
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
                                <option value="{{ $country['name'] }}">
                                    {{ $country['name'] }} 
                                    @if(!empty($country['isd_no']))
                                        ({{ $country['isd_no'] }})
                                    @endif
                                    @if(!empty($country['code']))
                                        - {{ $country['code'] }}
                                    @endif
                                </option>
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
                        <p class="text-sm text-gray-600 font-medium mb-2">Network and Service (Best Price)</p>
                        <div class="rate-amount" id="rateAmountWithGST">₹0.00</div>
                        <p class="text-xs text-gray-500 mt-1 mb-3">Total (Including GST)</p>
                        <div class="text-xs text-gray-600 mt-3 mb-2" id="selectedNetworkService">
                            <!-- Selected network/service will be populated here -->
                        </div>
                        <div class="text-xs text-gray-500" id="networkServiceList">
                            <!-- Network/Service list will be populated here -->
                        </div>
                        <div class="text-xs text-gray-500 mt-2" id="rateDetails">
                            <!-- Formula rate info will be populated here -->
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="button" id="toggleBreakdownBtn" class="w-full mb-4 px-4 py-2 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg text-sm font-semibold text-orange-700 flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <span>View Detailed Breakdown</span>
                        </button>
                        
                        <div id="rateBreakdown" style="display: none;">
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
                            <p class="text-xs text-gray-600">Click calculate to get instant rate estimation with automatic network/service matching</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Rate Factors</h4>
                    <div class="space-y-2 text-xs text-gray-600">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-600"></div>
                            <span>Base price from shipping charges (destination pincode, network, service)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-600"></div>
                            <span>Weight price from formulas (network, service)</span>
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

        // Toggle breakdown visibility (set up once, outside form submission)
        let breakdownVisible = false;
        const toggleBreakdownBtn = document.getElementById('toggleBreakdownBtn');
        if (toggleBreakdownBtn) {
            toggleBreakdownBtn.addEventListener('click', function() {
                const rateBreakdown = document.getElementById('rateBreakdown');
                if (rateBreakdown) {
                    breakdownVisible = !breakdownVisible;
                    if (breakdownVisible) {
                        rateBreakdown.style.display = 'block';
                        this.querySelector('span').textContent = 'Hide Detailed Breakdown';
                        this.querySelector('svg').style.transform = 'rotate(180deg)';
                    } else {
                        rateBreakdown.style.display = 'none';
                        this.querySelector('span').textContent = 'View Detailed Breakdown';
                        this.querySelector('svg').style.transform = 'rotate(0deg)';
                    }
                }
            });
        }

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
                    // Calculate rate with GST - use selected option's total if available, otherwise use main rate
                    let displayRate = data.rate;
                    if (data.all_network_service_options && data.all_network_service_options.length > 0) {
                        const selectedOption = data.all_network_service_options.find(function(opt) {
                            return opt.is_selected;
                        });
                        if (selectedOption) {
                            displayRate = selectedOption.total_rate;
                        }
                    }
                    
                    // Calculate GST (18%)
                    const gstRate = 18;
                    const gstAmount = (displayRate * gstRate) / 100;
                    const totalWithGST = displayRate + gstAmount;
                    
                    // Update rate amount at top with GST included
                    document.getElementById('rateAmountWithGST').textContent = '₹' + totalWithGST.toFixed(2);
                    
                    // Build selected network/service info
                    let selectedNetworkHtml = '';
                    const network = data.base_price_info.network || 'N/A';
                    const service = data.base_price_info.service || 'N/A';
                    
                    if (network !== 'N/A' && service !== 'N/A') {
                        selectedNetworkHtml = '<div class="inline-block px-3 py-1 bg-blue-50 border border-blue-200 rounded-lg">';
                        selectedNetworkHtml += '<span class="font-semibold text-blue-700">Selected: ' + network + ' - ' + service + '</span>';
                        selectedNetworkHtml += '</div>';
                    }
                    document.getElementById('selectedNetworkService').innerHTML = selectedNetworkHtml;
                    
                    // Build network/service list (all available options)
                    let networkServiceListHtml = '';
                    if (data.all_network_service_options && data.all_network_service_options.length > 0) {
                        networkServiceListHtml = '<div class="mt-3 pt-3 border-t border-gray-200">';
                        networkServiceListHtml += '<div class="text-xs font-semibold text-gray-600 mb-2">Available Options:</div>';
                        data.all_network_service_options.forEach(function(option, index) {
                            const isSelected = option.is_selected;
                            const optionGST = (option.total_rate * gstRate) / 100;
                            const optionTotalWithGST = option.total_rate + optionGST;
                            
                            networkServiceListHtml += '<div class="mb-2 p-2 rounded text-xs ' + (isSelected ? 'bg-blue-50 border border-blue-300' : 'bg-gray-50 border border-gray-200') + '">';
                            networkServiceListHtml += '<div class="flex items-center justify-between mb-1">';
                            networkServiceListHtml += '<span class="' + (isSelected ? 'font-semibold text-blue-700' : 'text-gray-700') + '">';
                            networkServiceListHtml += option.network + ' - ' + option.service;
                            if (isSelected) {
                                networkServiceListHtml += ' <span class="text-green-600 text-xs">(Selected - Best Price)</span>';
                            }
                            networkServiceListHtml += '</span>';
                            networkServiceListHtml += '<span class="' + (isSelected ? 'font-bold text-blue-900' : 'font-semibold text-gray-800') + '">₹' + optionTotalWithGST.toFixed(2) + '</span>';
                            networkServiceListHtml += '</div>';
                            if (!isSelected) {
                                networkServiceListHtml += '<div class="text-gray-500 text-xs">Base: ₹' + option.base_rate.toFixed(2) + ' + Weight: ₹' + option.weight_charge.toFixed(2) + ' + Distance: ₹' + option.distance_charge.toFixed(2) + ' + GST: ₹' + optionGST.toFixed(2) + '</div>';
                            }
                            networkServiceListHtml += '</div>';
                        });
                        networkServiceListHtml += '</div>';
                    }
                    document.getElementById('networkServiceList').innerHTML = networkServiceListHtml;
                    
                    // Build formula rate info for brackets
                    let rateDetailsHtml = '';
                    if (data.applied_formulas && data.applied_formulas.length > 0) {
                        const formulaRates = [];
                        data.applied_formulas.forEach(function(formula) {
                            const formulaType = formula.type || 'Fixed';
                            const formulaScope = formula.scope || 'Flat';
                            const formulaValue = formula.value || 0;
                            
                            let rateText = '';
                            if (formulaType === 'Fixed') {
                                if (formulaScope === 'per kg') {
                                    rateText = '₹' + formulaValue.toFixed(2) + ' per kg';
                                } else {
                                    rateText = '₹' + formulaValue.toFixed(2) + ' flat';
                                }
                            } else { // Percentage
                                if (formulaScope === 'per kg') {
                                    rateText = formulaValue.toFixed(2) + '% per kg';
                                } else {
                                    rateText = formulaValue.toFixed(2) + '%';
                                }
                            }
                            if (rateText) {
                                formulaRates.push(rateText);
                            }
                        });
                        
                        if (formulaRates.length > 0) {
                            rateDetailsHtml = '<div class="mt-2 text-gray-600">';
                            rateDetailsHtml += '<span class="font-semibold">Formula:</span> ';
                            rateDetailsHtml += '<span class="text-gray-500">' + formulaRates.join(', ') + '</span>';
                            rateDetailsHtml += '</div>';
                        }
                    }
                    document.getElementById('rateDetails').innerHTML = rateDetailsHtml;
                    
                    // Build breakdown HTML
                    let breakdownHtml = '';
                    
                    // Network and Services Rate with Network/Service info
                    breakdownHtml += '<div class="breakdown-item bg-blue-50 border-blue-200">';
                    breakdownHtml += '<div class="flex justify-between items-center mb-2">';
                    breakdownHtml += '<span class="text-sm font-medium text-gray-700">Network and Services Rate</span>';
                    breakdownHtml += '<span class="text-sm font-bold text-gray-900">₹' + data.breakdown.base_rate.toFixed(2) + '</span>';
                    breakdownHtml += '</div>';
                    breakdownHtml += '<div class="text-xs text-gray-600 mt-1 pt-2 border-t border-blue-200">';
                    breakdownHtml += '<div class="flex items-center gap-2 mb-1">';
                    breakdownHtml += '<span class="font-semibold">Network:</span>';
                    breakdownHtml += '<span>' + (data.base_price_info.network || 'N/A') + '</span>';
                    breakdownHtml += '</div>';
                    breakdownHtml += '<div class="flex items-center gap-2 mb-1">';
                    breakdownHtml += '<span class="font-semibold">Service:</span>';
                    breakdownHtml += '<span>' + (data.base_price_info.service || 'N/A') + '</span>';
                    breakdownHtml += '</div>';
                    breakdownHtml += '<div class="flex items-center gap-2 mb-1">';
                    breakdownHtml += '<span class="font-semibold">Destination Zone:</span>';
                    breakdownHtml += '<span>' + (data.base_price_info.destination_zone || 'N/A') + '</span>';
                    breakdownHtml += '</div>';
                    breakdownHtml += '<div class="flex items-center gap-2 mb-1">';
                    breakdownHtml += '<span class="font-semibold">Transit Time:</span>';
                    breakdownHtml += '<span>' + (data.base_price_info.transit_time || 'N/A') + '</span>';
                    breakdownHtml += '</div>';
                    breakdownHtml += '<div class="flex items-center gap-2 mb-2">';
                    breakdownHtml += '<span class="font-semibold">Items Allowed:</span>';
                    breakdownHtml += '<span>' + (data.base_price_info.items_allowed || 'N/A') + '</span>';
                    breakdownHtml += '</div>';
                    
                    // Show all available network/service options if multiple exist
                    if (data.all_network_service_options && data.all_network_service_options.length > 1) {
                        breakdownHtml += '<div class="mt-2 pt-2 border-t border-blue-300">';
                        breakdownHtml += '<div class="font-semibold mb-2 text-blue-700">All Available Options:</div>';
                        data.all_network_service_options.forEach(function(option) {
                            const isSelected = option.is_selected;
                            breakdownHtml += '<div class="mb-3 p-2 rounded ' + (isSelected ? 'bg-blue-100 border border-blue-300' : 'bg-gray-50 border border-gray-200') + '">';
                            breakdownHtml += '<div class="flex items-center justify-between mb-1">';
                            breakdownHtml += '<span class="text-xs font-semibold ' + (isSelected ? 'text-blue-800' : 'text-gray-700') + '">';
                            breakdownHtml += option.network + ' - ' + option.service;
                            if (isSelected) {
                                breakdownHtml += ' <span class="text-green-600">[Selected - Best Rate]</span>';
                            }
                            breakdownHtml += '</span>';
                            breakdownHtml += '<span class="text-xs font-bold ' + (isSelected ? 'text-blue-900' : 'text-gray-800') + '">Total: ₹' + option.total_rate.toFixed(2) + '</span>';
                            breakdownHtml += '</div>';
                            
                            // Show breakdown for this option
                            breakdownHtml += '<div class="text-xs text-gray-600 ml-2 mt-1">';
                            breakdownHtml += '<div>Base: ₹' + option.base_rate.toFixed(2) + '</div>';
                            breakdownHtml += '<div>Weight: ₹' + option.weight_charge.toFixed(2);
                            if (option.formulas && option.formulas.length > 0) {
                                breakdownHtml += ' (';
                                option.formulas.forEach(function(formula, idx) {
                                    if (idx > 0) breakdownHtml += ', ';
                                    breakdownHtml += formula.name;
                                });
                                breakdownHtml += ')';
                            }
                            breakdownHtml += '</div>';
                            breakdownHtml += '<div>Distance: ₹' + option.distance_charge.toFixed(2) + '</div>';
                            breakdownHtml += '</div>';
                            
                            // Show transit time and items allowed for this option
                            if (option.transit_time || option.items_allowed) {
                                breakdownHtml += '<div class="text-xs text-gray-600 ml-2 mt-2 pt-2 border-t border-gray-300">';
                                if (option.transit_time && option.transit_time !== 'N/A') {
                                    breakdownHtml += '<div class="mb-1">';
                                    breakdownHtml += '<span class="font-semibold">Transit Time:</span> ';
                                    breakdownHtml += '<span>' + option.transit_time + '</span>';
                                    breakdownHtml += '</div>';
                                }
                                if (option.items_allowed && option.items_allowed !== 'N/A') {
                                    breakdownHtml += '<div>';
                                    breakdownHtml += '<span class="font-semibold">Items Allowed:</span> ';
                                    breakdownHtml += '<span>' + option.items_allowed + '</span>';
                                    breakdownHtml += '</div>';
                                }
                                breakdownHtml += '</div>';
                            }
                            
                            if (option.count > 1) {
                                breakdownHtml += '<div class="text-xs text-gray-500 mt-1 ml-2">(' + option.count + ' charges with this network/service)</div>';
                            }
                            breakdownHtml += '</div>';
                        });
                        breakdownHtml += '</div>';
                    } else if (data.all_matching_charges && data.all_matching_charges.length > 1) {
                        breakdownHtml += '<div class="mt-2 pt-2 border-t border-blue-300">';
                        breakdownHtml += '<div class="text-gray-500 text-xs">';
                        breakdownHtml += 'Note: ' + data.all_matching_charges.length + ' charges found with same network/service. Using best rate (₹' + data.breakdown.base_rate.toFixed(2) + ').';
                        breakdownHtml += '</div>';
                        breakdownHtml += '</div>';
                    }
                    
                    breakdownHtml += '</div>';
                    breakdownHtml += '</div>';
                    
                    // Formula with Formula info
                    breakdownHtml += '<div class="breakdown-item bg-green-50 border-green-200">';
                    breakdownHtml += '<div class="flex justify-between items-center mb-2">';
                    breakdownHtml += '<span class="text-sm font-medium text-gray-700">Formula</span>';
                    breakdownHtml += '<span class="text-sm font-bold text-gray-900">₹' + data.breakdown.weight_charge.toFixed(2) + '</span>';
                    breakdownHtml += '</div>';
                    
                    if (data.applied_formulas && data.applied_formulas.length > 0) {
                        breakdownHtml += '<div class="text-xs text-gray-600 mt-1 pt-2 border-t border-green-200">';
                        breakdownHtml += '<div class="font-semibold mb-2">Applied Formulas (Network: ' + (data.base_price_info.network || 'N/A') + ', Service: ' + (data.base_price_info.service || 'N/A') + '):</div>';
                        
                        data.applied_formulas.forEach(function(formula, index) {
                            const formulaName = formula.name || formula.formula_name || ('Formula ' + (index + 1));
                            const formulaType = formula.type || 'Fixed';
                            const formulaScope = formula.scope || 'Flat';
                            const formulaValue = formula.value || 0;
                            const calculatedCharge = formula.calculated_charge || 0;
                            const hasBorder = index < data.applied_formulas.length - 1;
                            
                            breakdownHtml += '<div class="mb-2 pb-2' + (hasBorder ? ' border-b border-green-200' : '') + '">';
                            breakdownHtml += '<div class="flex justify-between items-center mb-1">';
                            breakdownHtml += '<span class="font-medium">' + formulaName + '</span>';
                            breakdownHtml += '<span class="font-bold">₹' + calculatedCharge.toFixed(2) + '</span>';
                            breakdownHtml += '</div>';
                            breakdownHtml += '<div class="text-gray-500 text-xs">';
                            
                            let formulaDesc = formulaType;
                            if (formulaScope === 'per kg') {
                                formulaDesc += ' - ' + formulaValue + (formulaType === 'Percentage' ? '%' : '') + ' per kg';
                            } else if (formulaType === 'Percentage') {
                                formulaDesc += ' - ' + formulaValue + '%';
                            } else {
                                formulaDesc += ' - ₹' + formulaValue;
                            }
                            
                            if (formula.priority) {
                                formulaDesc += ' (' + formula.priority + ' priority)';
                            }
                            
                            // Show network/service if different from base or if it's a general formula
                            if (formula.network && formula.service) {
                                if (formula.network !== data.base_price_info.network || formula.service !== data.base_price_info.service) {
                                    formulaDesc += ' | Network: ' + formula.network + ', Service: ' + formula.service;
                                }
                            } else if (!formula.network && !formula.service) {
                                formulaDesc += ' | General formula (applies to all)';
                            }
                            
                            breakdownHtml += formulaDesc;
                            breakdownHtml += '</div>';
                            breakdownHtml += '</div>';
                        });
                        
                        breakdownHtml += '</div>';
                    } else {
                        breakdownHtml += '<div class="text-xs text-gray-600 mt-1 pt-2 border-t border-green-200">';
                        breakdownHtml += '<span>No formulas found for Network: ' + (data.base_price_info.network || 'N/A') + ', Service: ' + (data.base_price_info.service || 'N/A') + '</span>';
                        breakdownHtml += '<br><span class="text-gray-400">Using default calculation (₹10 per kg)</span>';
                        breakdownHtml += '</div>';
                    }
                    
                    breakdownHtml += '</div>';
                    
                    // Distance Charge
                    breakdownHtml += '<div class="breakdown-item">';
                    breakdownHtml += '<div class="flex justify-between items-center">';
                    breakdownHtml += '<span class="text-sm font-medium text-gray-700">Distance Charge</span>';
                    breakdownHtml += '<span class="text-sm font-bold text-gray-900">₹' + data.breakdown.distance_charge.toFixed(2) + '</span>';
                    breakdownHtml += '</div>';
                    breakdownHtml += '</div>';
                    
                    // Service Type
                    breakdownHtml += '<div class="breakdown-item bg-purple-50 border-purple-200">';
                    breakdownHtml += '<div class="flex justify-between items-center">';
                    breakdownHtml += '<span class="text-sm font-bold text-purple-700">Service Type</span>';
                    breakdownHtml += '<span class="text-sm font-bold text-purple-900">' + data.breakdown.service_type + '</span>';
                    breakdownHtml += '</div>';
                    breakdownHtml += '</div>';
                    
                    // Add detailed breakdown with GST (use already calculated values)
                    breakdownHtml += '<div class="breakdown-item bg-yellow-50 border-yellow-200 mt-4">';
                    breakdownHtml += '<div class="font-semibold text-sm text-gray-900 mb-3">Detailed Breakdown</div>';
                    
                    // Subtotal (before GST) - use the displayRate already calculated
                    breakdownHtml += '<div class="flex justify-between items-center mb-2 pb-2 border-b border-yellow-200">';
                    breakdownHtml += '<span class="text-sm text-gray-700">Subtotal (Before GST)</span>';
                    breakdownHtml += '<span class="text-sm font-bold text-gray-900">₹' + displayRate.toFixed(2) + '</span>';
                    breakdownHtml += '</div>';
                    
                    // Formula breakdown
                    if (data.applied_formulas && data.applied_formulas.length > 0) {
                        breakdownHtml += '<div class="mb-2 pb-2 border-b border-yellow-200">';
                        breakdownHtml += '<div class="text-xs font-semibold text-gray-700 mb-1">Formulas Applied:</div>';
                        data.applied_formulas.forEach(function(formula, index) {
                            const formulaName = formula.name || formula.formula_name || ('Formula ' + (index + 1));
                            const calculatedCharge = formula.calculated_charge || 0;
                            breakdownHtml += '<div class="flex justify-between items-center text-xs text-gray-600 mb-1">';
                            breakdownHtml += '<span>' + formulaName + '</span>';
                            breakdownHtml += '<span>₹' + calculatedCharge.toFixed(2) + '</span>';
                            breakdownHtml += '</div>';
                        });
                        breakdownHtml += '</div>';
                    }
                    
                    // GST 18% - use the already calculated gstAmount
                    breakdownHtml += '<div class="flex justify-between items-center mb-2 pb-2 border-b border-yellow-200">';
                    breakdownHtml += '<span class="text-sm text-gray-700">GST (18%)</span>';
                    breakdownHtml += '<span class="text-sm font-bold text-gray-900">₹' + gstAmount.toFixed(2) + '</span>';
                    breakdownHtml += '</div>';
                    
                    // Total (with GST) - use the already calculated totalWithGST
                    breakdownHtml += '<div class="flex justify-between items-center pt-2">';
                    breakdownHtml += '<span class="text-sm font-bold text-gray-900">Total (Including GST)</span>';
                    breakdownHtml += '<span class="text-lg font-bold text-orange-600">₹' + totalWithGST.toFixed(2) + '</span>';
                    breakdownHtml += '</div>';
                    
                    breakdownHtml += '</div>';
                    
                    document.getElementById('rateBreakdown').innerHTML = breakdownHtml;
                    
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



