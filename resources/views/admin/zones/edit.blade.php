@extends('layouts.admin')

@section('title', 'Edit Zone')

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
    </style>

    <!-- Success Popup -->
    <div id="success-popup" class="success-popup" style="display: none;">
        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-green-700 font-semibold text-sm" id="success-message">Zone updated successfully!</p>
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Edit Zone</h1>
                    <p class="text-xs text-gray-600">Update zone information - Pincode: {{ $zone['pincode'] }}</p>
                </div>
            </div>
            <a href="{{ route('admin.zones.all') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to All
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Zone Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Update Zone Information
                </h2>

                <form id="zoneForm" method="POST" action="{{ route('admin.zones.update', $zone['id']) }}">
                    @csrf
                    @method('PUT')

                    <!-- Pincode -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Pincode <span class="required">*</span>
                        </label>
                        <input type="text" name="pincode" id="pincode" class="form-input" value="{{ $zone['pincode'] }}" required>
                        <p class="text-xs text-gray-500 mt-1">Numbers, text, and spaces allowed</p>
                    </div>

                    <!-- Country -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Country <span class="required">*</span>
                        </label>
                        <select name="country" id="country" class="form-select" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country['name'] }}" {{ $zone['country'] == $country['name'] ? 'selected' : '' }}>{{ $country['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Zone -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Zone <span class="required">*</span>
                        </label>
                        <select name="zone" id="zone" class="form-select" required>
                            <option value="">Select Zone</option>
                            <option value="No Zone" {{ $zone['zone'] == 'No Zone' ? 'selected' : '' }}>No Zone</option>
                            <option value="Remote" {{ $zone['zone'] == 'Remote' ? 'selected' : '' }}>Remote</option>
                            @for($i = 1; $i <= 60; $i++)
                                <option value="Zone {{ $i }}" {{ $zone['zone'] == "Zone {$i}" ? 'selected' : '' }}>Zone {{ $i }}</option>
                            @endfor
                        </select>
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
                                <option value="{{ $network['name'] }}" {{ $zone['network'] == $network['name'] ? 'selected' : '' }}>{{ $network['name'] }} ({{ $network['type'] }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Service -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Service <span class="required">*</span>
                        </label>
                        <select name="service" id="service" class="form-select" required>
                            <option value="">Select Service</option>
                            @foreach($services as $service)
                                @if($service['network'] == $zone['network'])
                                    <option value="{{ $service['name'] }}" {{ $zone['service'] == $service['name'] ? 'selected' : '' }}>{{ $service['name'] }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Remark -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remark
                        </label>
                        <textarea name="remark" id="remark" rows="3" class="form-textarea resize-none" placeholder="Any additional notes">{{ $zone['remark'] ?? '' }}</textarea>
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
                                <input type="checkbox" name="status" id="status" {{ $zone['status'] == 'Active' ? 'checked' : '' }}>
                                <span class="status-slider"></span>
                            </label>
                            <span class="text-sm font-medium text-gray-700" id="statusLabel">{{ $zone['status'] }}</span>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Update Zone</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.zones.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
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
                    Zone Details
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Zone ID</p>
                        <p class="text-sm font-bold text-gray-900">#{{ $zone['id'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Current Pincode</p>
                        <p class="text-sm font-bold text-gray-900">{{ $zone['pincode'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Current Country</p>
                        <p class="text-sm font-bold text-gray-900">{{ $zone['country'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Current Zone</p>
                        <p class="text-sm font-bold text-gray-900">{{ $zone['zone'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Current Status</p>
                        <span class="status-badge {{ strtolower($zone['status']) }}">
                            {{ $zone['status'] }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.zones.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        Back to All Zones
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Services data for filtering
        const allServices = @json($services);
        const currentZoneNetwork = '{{ $zone['network'] }}';
        const currentZoneService = '{{ $zone['service'] }}';
        
        // Status toggle
        document.getElementById('status').addEventListener('change', function() {
            document.getElementById('statusLabel').textContent = this.checked ? 'Active' : 'Inactive';
        });

        // Network change handler - filter services by network
        document.getElementById('network').addEventListener('change', function() {
            const selectedNetwork = this.value;
            const serviceSelect = document.getElementById('service');
            
            // Clear existing options
            serviceSelect.innerHTML = '<option value="">Select Service</option>';
            
            if (selectedNetwork) {
                // Filter services by selected network
                const filteredServices = allServices.filter(service => service.network === selectedNetwork);
                
                if (filteredServices.length > 0) {
                    filteredServices.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.name;
                        option.textContent = service.name;
                        // If this was the original service, select it
                        if (service.name === currentZoneService && selectedNetwork === currentZoneNetwork) {
                            option.selected = true;
                        }
                        serviceSelect.appendChild(option);
                    });
                } else {
                    serviceSelect.innerHTML = '<option value="">No services available for this network</option>';
                }
            }
        });

        // Form submission with AJAX
        document.getElementById('zoneForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            
            // Validate required fields first
            const pincode = document.getElementById('pincode').value.trim();
            const country = document.getElementById('country').value;
            const zone = document.getElementById('zone').value;
            const network = document.getElementById('network').value;
            const service = document.getElementById('service').value;
            
            if (!pincode || !country || !zone || !network || !service) {
                alert('Please fill in all required fields.');
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
                    showSuccessPopup('Zone updated successfully!');
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.zones.all") }}';
                    }, 1500);
                } else if (result.data) {
                    if (result.data.success) {
                        showSuccessPopup(result.data.message || 'Zone updated successfully!');
                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = result.data.redirect || '{{ route("admin.zones.all") }}';
                        }, 1500);
                    } else {
                        // Handle validation errors
                        if (result.data.errors) {
                            let errorMessages = Object.values(result.data.errors).flat().join('\n');
                            alert('Validation errors:\n' + errorMessages);
                            console.error('Validation errors:', result.data.errors);
                        } else {
                            alert(result.data.message || 'An error occurred while updating the zone.');
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
                alert('An error occurred while updating the zone. Please check the console for details.');
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



