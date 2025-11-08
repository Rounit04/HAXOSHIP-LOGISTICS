@extends('layouts.admin')

@section('title', 'Create Country')

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
            <p class="text-green-700 font-semibold text-sm" id="success-message">Country created successfully!</p>
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Create Country</h1>
                    <p class="text-xs text-gray-600">Add a new country - Single, bulk, editable Status (Active/Inactive)</p>
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
                    <h2 class="text-lg font-bold text-gray-900">Bulk Import Countries</h2>
                    <p class="text-sm text-gray-600">Upload an Excel file to import multiple countries at once</p>
                </div>
            </div>
            <a href="{{ route('admin.countries.template.download') }}" class="admin-btn-primary px-4 py-2 text-sm">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Template
            </a>
        </div>
        <form method="POST" action="{{ route('admin.countries.import') }}" enctype="multipart/form-data" class="flex items-center gap-3">
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
        <!-- Country Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Country Information
                </h2>

                <form id="countryForm" method="POST" action="{{ route('admin.countries.store') }}">
                    @csrf

                    <!-- Country Name -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Country Name <span class="required">*</span>
                        </label>
                        <input type="text" name="country_name" id="country_name" class="form-input" placeholder="e.g., India, United States" required>
                    </div>

                    <!-- Country Code -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Country Code <span class="required">*</span>
                        </label>
                        <input type="text" name="country_code" id="country_code" class="form-input" placeholder="e.g., IN, US" required maxlength="10">
                    </div>

                    <!-- Country ISD No. -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Country ISD No. <span class="required">*</span>
                        </label>
                        <input type="text" name="country_isd_no" id="country_isd_no" class="form-input" placeholder="e.g., +91, +1" required maxlength="10">
                    </div>

                    <!-- Country Dialing Code -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Country Dialing Code
                        </label>
                        <input type="text" name="country_dialing_code" id="country_dialing_code" class="form-input" placeholder="e.g., 0091, 001" maxlength="10">
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
                                <span>Create Country</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.countries.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
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
                    Country Guidelines
                </h3>
                <div class="space-y-3 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Country Name must be unique</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Country Code is usually 2 letters (e.g., IN, US)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>ISD Number is the international dialing code (e.g., +91)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Dialing Code is optional (e.g., 0091)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Status can be changed later from All Countries</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.countries.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        View All Countries
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

        // Form submission with AJAX
        document.getElementById('countryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            
            // Validate required fields first
            const countryName = document.getElementById('country_name').value.trim();
            const countryCode = document.getElementById('country_code').value.trim();
            const countryIsdNo = document.getElementById('country_isd_no').value.trim();
            
            if (!countryName || !countryCode || !countryIsdNo) {
                alert('Please fill in all required fields (Country Name, Country Code, and Country ISD No.).');
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
                    showSuccessPopup('Country created successfully!');
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.countries.all") }}';
                    }, 1500);
                } else if (result.data) {
                    if (result.data.success) {
                        showSuccessPopup(result.data.message || 'Country created successfully!');
                        // Reset form
                        form.reset();
                        document.getElementById('statusLabel').textContent = 'Active';
                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = result.data.redirect || '{{ route("admin.countries.all") }}';
                        }, 1500);
                    } else {
                        // Handle validation errors
                        if (result.data.errors) {
                            let errorMessages = Object.values(result.data.errors).flat().join('\n');
                            alert('Validation errors:\n' + errorMessages);
                            console.error('Validation errors:', result.data.errors);
                        } else {
                            alert(result.data.message || 'An error occurred while creating the country.');
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
                alert('An error occurred while creating the country. Please check the console for details.');
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



