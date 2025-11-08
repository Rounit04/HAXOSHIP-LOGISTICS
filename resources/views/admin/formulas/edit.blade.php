@extends('layouts.admin')

@section('title', 'Edit Formula')

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
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 320px;
            animation: slideInRight 0.3s ease-out;
        }
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge.active {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }
        .status-badge.inactive {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Edit Formula</h1>
                    <p class="text-xs text-gray-600">Update formula information - {{ $formula['formula_name'] }}</p>
                </div>
            </div>
            <a href="{{ route('admin.formulas.all') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to All
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formula Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Update Formula Information
                </h2>

                <form id="formulaForm" method="POST" action="{{ route('admin.formulas.update', $formula['id']) }}">
                    @csrf
                    @method('PUT')

                    <!-- Formula Name -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Formula Name <span class="required">*</span>
                        </label>
                        <input type="text" name="formula_name" id="formula_name" class="form-input" value="{{ $formula['formula_name'] }}" required>
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
                                    <option value="{{ $network['name'] }}" {{ $formula['network'] == $network['name'] ? 'selected' : '' }}>{{ $network['name'] }} ({{ $network['type'] }})</option>
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

                    <!-- Type & Scope -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Type <span class="required">*</span>
                            </label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">Select Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ $formula['type'] == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Scope <span class="required">*</span>
                            </label>
                            <select name="scope" id="scope" class="form-select" required>
                                <option value="">Select Scope</option>
                                @foreach($scopes as $scope)
                                    <option value="{{ $scope }}" {{ $formula['scope'] == $scope ? 'selected' : '' }}>{{ $scope }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Priority & Value -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                                Priority <span class="required">*</span>
                            </label>
                            <select name="priority" id="priority" class="form-select" required>
                                <option value="">Select Priority</option>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority }}" {{ $formula['priority'] == $priority ? 'selected' : '' }}>{{ $priority }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                Value <span class="required">*</span>
                            </label>
                            <input type="number" name="value" id="value" class="form-input" step="0.01" min="0" value="{{ $formula['value'] }}" required>
                        </div>
                    </div>

                    <!-- Remark -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remark
                        </label>
                        <textarea name="remark" id="remark" rows="3" class="form-textarea resize-none" placeholder="Any additional notes">{{ $formula['remark'] ?? '' }}</textarea>
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
                                <input type="checkbox" name="status" id="status" {{ $formula['status'] == 'Active' ? 'checked' : '' }}>
                                <span class="status-slider"></span>
                            </label>
                            <span class="text-sm font-medium text-gray-700" id="statusLabel">{{ $formula['status'] }}</span>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Update Formula</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.formulas.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
                </form>

    <!-- Success Popup -->
    <div id="success-popup" class="success-popup" style="display: none;">
        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-green-700 font-semibold text-sm" id="success-message">Formula updated successfully!</p>
        </div>
        <button onclick="closePopup()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="form-card p-5 sticky top-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Formula Details
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Formula ID</p>
                        <p class="text-sm font-bold text-gray-900">#{{ $formula['id'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Current Name</p>
                        <p class="text-sm font-bold text-gray-900">{{ $formula['formula_name'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Network & Service</p>
                        <p class="text-sm font-bold text-gray-900">{{ $formula['network'] }} - {{ $formula['service'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Type & Scope</p>
                        <p class="text-sm font-bold text-gray-900">{{ $formula['type'] }} ({{ $formula['scope'] }})</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Priority</p>
                        <p class="text-sm font-bold text-gray-900">{{ $formula['priority'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Current Value</p>
                        <p class="text-sm font-bold text-orange-600">{{ number_format($formula['value'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Current Status</p>
                        <span class="status-badge {{ strtolower($formula['status']) }}">
                            {{ $formula['status'] }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.formulas.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        Back to All Formulas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Network-wise service filter
        const networkSelect = document.getElementById('network');
        const serviceSelect = document.getElementById('service');
        const services = @json($services);
        const currentNetwork = '{{ $formula['network'] }}';
        const currentService = '{{ $formula['service'] }}';

        // Populate services when page loads
        function populateServices() {
            const selectedNetwork = networkSelect.value || currentNetwork;
            serviceSelect.innerHTML = '<option value="">Select Service</option>';
            
            if (selectedNetwork) {
                const filteredServices = services.filter(service => service.network === selectedNetwork);
                filteredServices.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.name;
                    option.textContent = service.name;
                    if (service.name === currentService && selectedNetwork === currentNetwork) {
                        option.selected = true;
                    }
                    serviceSelect.appendChild(option);
                });
            }
        }

        // Initial population
        populateServices();

        // Update services when network changes
        networkSelect.addEventListener('change', function() {
            populateServices();
        });

        // Status toggle
        document.getElementById('status').addEventListener('change', function() {
            document.getElementById('statusLabel').textContent = this.checked ? 'Active' : 'Inactive';
        });

        // Form submission with AJAX and success popup
        document.getElementById('formulaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const buttonText = submitButton.querySelector('span');
            const originalText = buttonText.textContent;
            
            // Disable button and show loading
            submitButton.disabled = true;
            buttonText.textContent = 'Updating...';
            
            try {
                // Ensure _method is set for PUT request
                formData.append('_method', 'PUT');
                
                // Ensure status checkbox is properly handled
                const statusCheckbox = form.querySelector('input[name="status"]');
                if (statusCheckbox && !statusCheckbox.checked) {
                    formData.delete('status');
                }
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                // Read response as text first to check if it's JSON
                const responseText = await response.text();
                let data;
                
                try {
                    // Try to parse as JSON
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    // If parsing fails, check status code
                    console.error('Failed to parse JSON response:', parseError);
                    console.error('Response text:', responseText.substring(0, 200));
                    console.error('Response status:', response.status);
                    
                    // If status is 422, it's likely a validation error
                    if (response.status === 422) {
                        alert('Validation failed. Please check all required fields are filled correctly.');
                    } else {
                        alert('Server returned an unexpected response. Please try again.');
                    }
                    submitButton.disabled = false;
                    buttonText.textContent = originalText;
                    return;
                }
                
                if (data.success) {
                    // Show success popup
                    document.getElementById('success-message').textContent = data.message || 'Formula updated successfully!';
                    document.getElementById('success-popup').style.display = 'flex';
                    
                    // Redirect after 1.5 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route("admin.formulas.all") }}';
                    }, 1500);
                } else {
                    // Show error message
                    const errorMsg = data.message || 'Error updating formula. Please try again.';
                    if (data.errors) {
                        // Display validation errors
                        const errorList = Object.values(data.errors).flat().join('\n');
                        alert(errorMsg + '\n\n' + errorList);
                    } else {
                        alert(errorMsg);
                    }
                    submitButton.disabled = false;
                    buttonText.textContent = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                // Check if it's a JSON parse error from a 422 response
                if (error.name === 'SyntaxError' || error.message.includes('JSON')) {
                    alert('Validation failed. Please check all required fields are filled correctly.');
                } else if (error.message.includes('422')) {
                    alert('Validation failed. Please check all required fields are filled correctly.');
                } else {
                    alert('Error updating formula: ' + (error.message || 'Please try again.'));
                }
                submitButton.disabled = false;
                buttonText.textContent = originalText;
            }
        });

        function closePopup() {
            const popup = document.getElementById('success-popup');
            popup.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                popup.style.display = 'none';
                popup.style.animation = 'slideInRight 0.3s ease-out';
            }, 300);
        }
    </script>
@endsection



