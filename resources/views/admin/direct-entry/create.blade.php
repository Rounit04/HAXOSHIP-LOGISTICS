@extends('layouts.admin')

@section('title', 'Direct Entry - Add Booking')

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
        .form-label .required-double {
            color: #dc2626;
            font-size: 13px;
            font-weight: 700;
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Direct Entry - Add Booking</h1>
                    <p class="text-xs text-gray-600">Single and Bulk Both Required</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Booking Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Booking Information
                </h2>

                <form method="POST" action="{{ route('admin.direct-entry.store') }}">
                    @csrf

                    <!-- AWB No. (Text Input - Special characters allowed, no duplicate) -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            AWB No. <span class="required">*</span> <span class="text-xs text-gray-500">(No duplicate / Special characters allowed**)</span>
                        </label>
                        <input type="text" name="awb_no" id="awb_no" class="form-input" placeholder="Enter AWB No. (e.g., AWB-123_456)" required value="{{ old('awb_no') }}">
                        <p class="text-xs text-gray-500 mt-1">Special characters like hyphens and underscores are allowed</p>
                        @error('awb_no')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(session('error'))
                            <p class="mt-1 text-sm text-red-600">{{ session('error') }}</p>
                        @endif
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
                            <option value="Dox" {{ old('shipment_type') == 'Dox' ? 'selected' : '' }}>Dox</option>
                            <option value="Non-Dox" {{ old('shipment_type') == 'Non-Dox' ? 'selected' : '' }}>Non-Dox</option>
                            <option value="Other" {{ old('shipment_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('shipment_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Origin & Destination -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Origin <span class="required-double">**</span>
                            </label>
                            <select name="origin" id="origin" class="form-select" required>
                                <option value="">Select Origin</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country['name'] }}" {{ old('origin') == $country['name'] ? 'selected' : '' }}>{{ $country['name'] }}</option>
                                @endforeach
                            </select>
                            @error('origin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                    <option value="{{ $country['name'] }}" {{ old('destination') == $country['name'] ? 'selected' : '' }}>{{ $country['name'] }}</option>
                                @endforeach
                            </select>
                            @error('destination')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Origin Pin & Destination Pin -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Origin Pin <span class="required">*</span>
                            </label>
                            <input type="text" name="origin_pin" id="origin_pin" class="form-input" placeholder="e.g., 400001" required value="{{ old('origin_pin') }}">
                            @error('origin_pin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Destination Pin <span class="required">*</span>
                            </label>
                            <input type="text" name="destination_pin" id="destination_pin" class="form-input" placeholder="e.g., 10001" required value="{{ old('destination_pin') }}">
                            @error('destination_pin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                            <input type="number" name="chr_weight" id="chr_weight" class="form-input" step="0.01" min="0.01" placeholder="0.00" required value="{{ old('chr_weight') }}">
                            @error('chr_weight')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Pieces <span class="required">*</span>
                            </label>
                            <input type="number" name="pieces" id="pieces" class="form-input" min="1" placeholder="1" required value="{{ old('pieces', 1) }}">
                            @error('pieces')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Network & Service -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                Network <span class="required">*</span>
                            </label>
                            <select name="network" id="network" class="form-select" required>
                                <option value="">Select Network</option>
                                @foreach($networks as $network)
                                    <option value="{{ $network['name'] }}" {{ old('network') == $network['name'] ? 'selected' : '' }}>{{ $network['name'] }} ({{ $network['type'] }})</option>
                                @endforeach
                            </select>
                            @error('network')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Service <span class="required">*</span> (Network Wise)
                            </label>
                            <select name="service" id="service" class="form-select" required>
                                <option value="">Select Network First</option>
                            </select>
                            @error('service')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Booking Amount Manually & Forwarding Service -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Booking Amount Manually <span class="required">*</span>
                            </label>
                            <input type="number" name="booking_amount" id="booking_amount" class="form-input" step="0.01" min="0" placeholder="0.00" required value="{{ old('booking_amount') }}">
                            @error('booking_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Forwarding Service <span class="required">*</span>
                            </label>
                            <input type="text" name="forwarding_service" id="forwarding_service" class="form-input" placeholder="e.g., FedEx" required value="{{ old('forwarding_service') }}">
                            @error('forwarding_service')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- V.AWB & F.AWB -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                V.AWB
                            </label>
                            <input type="text" name="v_awb" id="v_awb" class="form-input" placeholder="V123456789" value="{{ old('v_awb') }}">
                            @error('v_awb')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                F.AWB
                            </label>
                            <input type="text" name="f_awb" id="f_awb" class="form-input" placeholder="F987654321" value="{{ old('f_awb') }}">
                            @error('f_awb')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Dummy Number -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            Dummy Number
                        </label>
                        <input type="text" name="dummy_number" id="dummy_number" class="form-input" placeholder="Enter dummy number" value="{{ old('dummy_number') }}">
                        @error('dummy_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remark -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remark
                        </label>
                        <textarea name="remark" id="remark" class="form-textarea" rows="3" placeholder="Enter any additional remarks...">{{ old('remark') }}</textarea>
                        @error('remark')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Create Booking</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.direct-entry.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
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
                    Direct Entry Guidelines
                </h3>
                <div class="space-y-3 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>AWB No. must be unique (no duplicates allowed)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Special characters are allowed in AWB No.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Origin field is highly mandatory (**)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Supports both Single and Bulk bookings</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>All fields marked with * are required</span>
                    </div>
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
                        // Check if this was the previously selected service
                        if (service.name === '{{ old("service") }}') {
                            option.selected = true;
                        }
                        serviceSelect.appendChild(option);
                    });
                } else {
                    serviceSelect.innerHTML = '<option value="">No services available for this network</option>';
                }
            } else {
                serviceSelect.innerHTML = '<option value="">Select Network First</option>';
            }
        });

        // Initialize service dropdown if network is already selected (e.g., from old input)
        @if(old('network'))
            const oldNetwork = '{{ old("network") }}';
            if (oldNetwork) {
                networkSelect.value = oldNetwork;
                networkSelect.dispatchEvent(new Event('change'));
            }
        @endif
    </script>
@endsection

