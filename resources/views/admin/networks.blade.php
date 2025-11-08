@extends('layouts.admin')

@section('title', 'Networks')

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
        .network-table {
            width: 100%;
        }
        .network-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .network-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .network-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .network-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .network-table tbody td {
            padding: 14px 16px;
            font-size: 14px;
            color: #374151;
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
        .type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(255, 117, 15, 0.1);
            color: #FF750F;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Networks</h1>
                    <p class="text-xs text-gray-600">Manage networks - Single, bulk, editable status (Active/Inactive)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Network Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add New Network
                </h2>

                <form id="networkForm" method="POST" action="{{ route('admin.networks.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="network_id" id="network_id">

                    <!-- Network Name -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Network Name <span class="required">*</span>
                        </label>
                        <input type="text" name="network_name" id="network_name" class="form-input" placeholder="e.g., DTDC" required>
                    </div>

                    <!-- Network Type -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                            </svg>
                            Network Type <span class="required">*</span>
                        </label>
                        <select name="network_type" id="network_type" class="form-select" required>
                            <option value="">Select Network Type</option>
                            <option value="Domestic">Domestic</option>
                            <option value="International">International</option>
                        </select>
                    </div>

                    <!-- Opening Balance -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Network Opening Balance <span class="required">*</span>
                        </label>
                        <input type="number" name="opening_balance" id="opening_balance" step="0.01" min="0" class="form-input" placeholder="0.00" required>
                    </div>

                    <!-- Bank Details -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Network Bank Details
                        </label>
                        <input type="text" name="bank_details" id="bank_details" class="form-input" placeholder="Enter bank account details">
                    </div>

                    <!-- UPI Scanner -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            Network UPI Scanner
                        </label>
                        <div class="flex flex-col gap-3">
                            <!-- Toggle between UPI ID and File Upload -->
                            <div class="flex gap-3 mb-2">
                                <button type="button" onclick="switchUpiMode('text')" id="upiTextBtn" class="px-4 py-2 text-sm font-medium rounded-lg border-2 border-orange-600 bg-orange-600 text-white transition-all">
                                    Enter UPI ID
                                </button>
                                <button type="button" onclick="switchUpiMode('file')" id="upiFileBtn" class="px-4 py-2 text-sm font-medium rounded-lg border-2 border-gray-300 text-gray-700 bg-white hover:border-orange-600 hover:text-orange-600 transition-all">
                                    Upload Scanner
                                </button>
                            </div>
                            <!-- UPI ID Input -->
                            <div id="upiTextInput" class="upi-input-section">
                                <input type="text" name="upi_scanner" id="upi_scanner" class="form-input" placeholder="e.g., dtdc@paytm">
                            </div>
                            <!-- File Upload Input -->
                            <div id="upiFileInput" class="upi-input-section hidden">
                                <input type="file" name="upi_scanner_file" id="upi_scanner_file" class="form-input" accept="image/*" onchange="previewUpiImage(this)">
                                <input type="hidden" name="upi_scanner" id="upi_scanner_file_value">
                                <div id="upiImagePreview" class="mt-3 hidden">
                                    <img id="upiPreviewImg" src="" alt="UPI Scanner Preview" class="max-w-xs rounded-lg border-2 border-gray-300">
                                    <button type="button" onclick="removeUpiImage()" class="mt-2 px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">Remove Image</button>
                                </div>
                            </div>
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
                        <textarea name="remark" id="remark" rows="3" class="form-textarea resize-none" placeholder="Enter any remarks or notes"></textarea>
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
                                <span id="submitButtonText">Create Network</span>
                            </div>
                        </button>
                        <button type="button" onclick="resetForm()" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                            Reset
                        </button>
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

        <!-- Network List -->
        <div class="lg:col-span-1">
            <div class="form-card p-5 sticky top-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Network List
                </h3>
                <div class="space-y-2 max-h-[600px] overflow-y-auto">
                    @forelse($networks as $network)
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-purple-50 transition cursor-pointer border border-gray-200" onclick="editNetwork({{ $network['id'] }})">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-gray-900">{{ $network['name'] }}</span>
                                <span class="status-badge {{ strtolower($network['status']) }}">
                                    @if($network['status'] == 'Active')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ $network['status'] }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="type-badge">{{ $network['type'] }}</span>
                                <span class="text-xs text-gray-600">Balance: ₹{{ number_format($network['opening_balance'], 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No networks found</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Networks Table -->
    <div class="form-card p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Manage Networks
            </h3>
            <button onclick="bulkAction()" class="px-4 py-2 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition">
                Bulk Actions
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="network-table min-w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Opening Balance</th>
                        <th>Bank Details</th>
                        <th>UPI Scanner</th>
                        <th>Status</th>
                        <th>Remark</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($networks as $network)
                        <tr>
                            <td class="font-bold text-gray-900">{{ $network['name'] }}</td>
                            <td><span class="type-badge">{{ $network['type'] }}</span></td>
                            <td class="font-semibold">₹{{ number_format($network['opening_balance'], 2) }}</td>
                            <td class="text-sm text-gray-600">{{ $network['bank_details'] ?? '-' }}</td>
                            <td class="text-sm text-gray-600">{{ $network['upi_scanner'] ?? '-' }}</td>
                            <td>
                                <span class="status-badge {{ strtolower($network['status']) }}">
                                    @if($network['status'] == 'Active')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ $network['status'] }}
                                </span>
                            </td>
                            <td class="text-xs text-gray-500 max-w-xs truncate">{{ $network['remark'] ?? '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button onclick="editNetwork({{ $network['id'] }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button onclick="deleteNetwork({{ $network['id'] }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                    </svg>
                                    <p class="text-gray-500 font-medium text-sm">No networks found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Sample network data
        const networks = @json($networks);

        // Status toggle
        document.getElementById('status').addEventListener('change', function() {
            document.getElementById('statusLabel').textContent = this.checked ? 'Active' : 'Inactive';
        });

        // Edit network
        function editNetwork(id) {
            const network = networks.find(n => n.id == id);
            if (!network) return;

            document.getElementById('network_id').value = network.id;
            document.getElementById('network_name').value = network.name;
            document.getElementById('network_type').value = network.type;
            document.getElementById('opening_balance').value = network.opening_balance;
            document.getElementById('bank_details').value = network.bank_details || '';
            document.getElementById('upi_scanner').value = network.upi_scanner || '';
            document.getElementById('remark').value = network.remark || '';
            document.getElementById('status').checked = network.status === 'Active';
            document.getElementById('statusLabel').textContent = network.status;
            
            // Update form action
            const updateUrl = '{{ route("admin.networks.update", ":id") }}'.replace(':id', id);
            document.getElementById('networkForm').action = updateUrl;
            document.getElementById('networkForm').method = 'POST';
            
            // Remove existing method input if any
            const existingMethod = document.getElementById('networkForm').querySelector('input[name="_method"]');
            if (existingMethod) {
                existingMethod.remove();
            }
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            document.getElementById('networkForm').appendChild(methodInput);
            
            document.getElementById('submitButtonText').textContent = 'Update Network';
            
            // Scroll to form
            document.getElementById('networkForm').scrollIntoView({ behavior: 'smooth' });
        }

        // Reset form
        function resetForm() {
            document.getElementById('networkForm').reset();
            document.getElementById('network_id').value = '';
            document.getElementById('status').checked = true;
            document.getElementById('statusLabel').textContent = 'Active';
            
            // Reset form action
            document.getElementById('networkForm').action = '{{ route("admin.networks.store") }}';
            document.getElementById('networkForm').method = 'POST';
            
            // Remove existing method input if any
            const existingMethod = document.getElementById('networkForm').querySelector('input[name="_method"]');
            if (existingMethod) {
                existingMethod.remove();
            }
            
            document.getElementById('submitButtonText').textContent = 'Create Network';
        }

        // Delete network
        function deleteNetwork(id) {
            if (confirm('Are you sure you want to delete this network?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                const deleteUrl = '{{ route("admin.networks.delete", ":id") }}'.replace(':id', id);
                form.action = deleteUrl;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Bulk action
        function bulkAction() {
            alert('Bulk action feature - Select multiple networks to perform bulk operations');
        }

        // UPI Scanner Mode Toggle
        function switchUpiMode(mode) {
            const textInput = document.getElementById('upiTextInput');
            const fileInput = document.getElementById('upiFileInput');
            const textBtn = document.getElementById('upiTextBtn');
            const fileBtn = document.getElementById('upiFileBtn');
            const preview = document.getElementById('upiImagePreview');
            const previewImg = document.getElementById('upiPreviewImg');

            if (mode === 'text') {
                textInput.classList.remove('hidden');
                fileInput.classList.add('hidden');
                textBtn.classList.add('bg-orange-600', 'text-white', 'hover:bg-orange-700');
                textBtn.classList.remove('border-gray-300', 'text-gray-700', 'hover:border-orange-600', 'hover:text-orange-600');
                fileBtn.classList.remove('bg-orange-600', 'text-white', 'hover:bg-orange-700');
                fileBtn.classList.add('border-gray-300', 'text-gray-700', 'hover:border-orange-600', 'hover:text-orange-600');
                preview.classList.add('hidden');
                previewImg.src = ''; // Clear preview
            } else if (mode === 'file') {
                textInput.classList.add('hidden');
                fileInput.classList.remove('hidden');
                textBtn.classList.remove('bg-orange-600', 'text-white', 'hover:bg-orange-700');
                textBtn.classList.add('border-gray-300', 'text-gray-700', 'hover:border-orange-600', 'hover:text-orange-600');
                fileBtn.classList.add('bg-orange-600', 'text-white', 'hover:bg-orange-700');
                fileBtn.classList.remove('border-gray-300', 'text-gray-700', 'hover:border-orange-600', 'hover:text-orange-600');
                preview.classList.remove('hidden');
                previewImg.src = ''; // Clear preview
            }
        }

        // Preview UPI Image
        function previewUpiImage(input) {
            const preview = document.getElementById('upiImagePreview');
            const previewImg = document.getElementById('upiPreviewImg');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                previewImg.src = '';
            }
        }

        // Remove UPI Image
        function removeUpiImage() {
            const preview = document.getElementById('upiImagePreview');
            const previewImg = document.getElementById('upiPreviewImg');
            previewImg.src = '';
            preview.classList.add('hidden');
        }
    </script>
@endsection



