@extends('layouts.admin')

@section('title', 'Manage Wallet')

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
        .form-textarea {
            min-height: 100px;
            resize: vertical;
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
        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
            margin-top: 24px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table thead {
            background: #f9fafb;
        }
        .table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e5e7eb;
        }
        .table td {
            padding: 12px 16px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        .table tbody tr:hover {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-credit {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-debit {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Manage Wallet</h1>
                    <p class="text-xs text-gray-600">Bulk wallet transaction management</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Wallet Transaction Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Wallet Transaction
                </h2>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Bulk Upload Section -->
                <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Bulk Upload Wallet Transactions
                        </h3>
                        <a href="{{ route('admin.payments.wallet.template.download') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download Template
                        </a>
                    </div>
                    <form method="POST" action="{{ route('admin.payments.wallet.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-3">
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="flex-1 text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Upload
                            </button>
                        </div>
                    </form>
                    <p class="text-xs text-gray-600 mt-2">
                        <span class="font-semibold">Note:</span> Upload Excel/CSV file with wallet transaction data. Only valid AWBs and networks will be imported.
                    </p>
                </div>

                <div class="mb-4 flex items-center gap-4">
                    <div class="flex-1 border-t border-gray-300"></div>
                    <span class="text-xs font-semibold text-gray-500 uppercase">OR</span>
                    <div class="flex-1 border-t border-gray-300"></div>
                </div>

                <form method="POST" action="{{ route('admin.payments.wallet.bulk') }}">
                    @csrf

                    <!-- AWB Number -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            AWB Number <span class="required">*</span>
                        </label>
                        <div class="relative" id="awb_container">
                            <input type="text" id="awb_search" class="form-input mb-2" placeholder="Search AWB Number..." autocomplete="off">
                            <select name="awb_number" id="awb_number" class="form-select" required style="display: none;">
                                <option value="">Select AWB Number</option>
                                @foreach($awbUploads as $awb)
                                    <option value="{{ $awb['awb_no'] }}" data-destination="{{ $awb['destination'] ?? '' }}" {{ old('awb_number') == $awb['awb_no'] ? 'selected' : '' }}>
                                        {{ $awb['awb_no'] }} - {{ $awb['destination'] ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="awb_dropdown" class="absolute z-50 w-full mt-1 bg-white border-2 border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                                <!-- Options will be populated here -->
                            </div>
                        </div>
                        @error('awb_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Network -->
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
                                <option value="{{ $network['name'] }}" {{ old('network') == $network['name'] ? 'selected' : '' }}>
                                    {{ $network['name'] }} ({{ $network['type'] ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('network')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Type -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Transaction Type <span class="required">*</span>
                        </label>
                        <input type="text" name="transaction_type" id="transaction_type" class="form-input" placeholder="e.g., Payment, Refund, Adjustment" value="{{ old('transaction_type') }}" required>
                        @error('transaction_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mode & Type -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Mode <span class="required">*</span>
                            </label>
                            <select name="mode" id="mode" class="form-select" required onchange="updateTransactionType()">
                                <option value="">Select Mode</option>
                                <option value="UPI" {{ old('mode') == 'UPI' ? 'selected' : '' }}>UPI</option>
                                <option value="Cash" {{ old('mode') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Netf" {{ old('mode') == 'Netf' ? 'selected' : '' }}>Netf</option>
                            </select>
                            @error('mode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                                Type <span class="required">*</span>
                            </label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="Credit" {{ old('type') == 'Credit' ? 'selected' : '' }}>Credit</option>
                                <option value="Debit" {{ old('type') == 'Debit' ? 'selected' : '' }}>Debit</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Amount <span class="required">*</span>
                        </label>
                        <input type="number" name="amount" id="amount" class="form-input" placeholder="0.00" value="{{ old('amount') }}" step="0.01" min="0" required>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remark -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remark <span class="required">*</span>
                        </label>
                        <textarea name="remark" id="remark" class="form-textarea" rows="3" placeholder="Enter transaction remark" required>{{ old('remark') }}</textarea>
                        @error('remark')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition shadow-sm">
                            Add Transaction
                        </button>
                        <button type="reset" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>

            <!-- Wallet Transactions Table -->
            @if(count($walletTransactions) > 0)
                <div class="table-card p-6 mt-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Wallet Transactions
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>AWB Number</th>
                                    <th>Network</th>
                                    <th>Transaction Type</th>
                                    <th>Mode</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Remark</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($walletTransactions as $transaction)
                                    <tr>
                                        <td>#{{ $transaction['id'] ?? '' }}</td>
                                        <td>{{ $transaction['awb_number'] ?? '' }}</td>
                                        <td>{{ $transaction['network'] ?? '' }}</td>
                                        <td>{{ $transaction['transaction_type'] ?? '' }}</td>
                                        <td>{{ $transaction['mode'] ?? '' }}</td>
                                        <td>
                                            @if(isset($transaction['type']))
                                                <span class="badge {{ $transaction['type'] == 'Credit' ? 'badge-credit' : 'badge-debit' }}">
                                                    {{ $transaction['type'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="font-semibold">₹{{ number_format($transaction['amount'] ?? 0, 2) }}</td>
                                        <td>{{ $transaction['remark'] ?? '' }}</td>
                                        <td>{{ isset($transaction['created_at']) ? \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y, h:i A') : '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="table-card p-6 mt-6">
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">No wallet transactions yet</p>
                        <p class="text-gray-400 text-sm mt-1">Add your first transaction using the form above</p>
                    </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Info -->
            <div class="lg:col-span-1">
                <div class="form-card p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Information
                </h3>
                <div class="space-y-4 text-sm text-gray-600">
                    <div>
                        <p class="font-semibold text-gray-900 mb-1">Transaction Types</p>
                        <p>Enter the type of transaction such as Payment, Refund, Adjustment, etc.</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 mb-1">Payment Modes</p>
                        <ul class="list-disc list-inside space-y-1 ml-2">
                            <li>UPI - Unified Payments Interface</li>
                            <li>Cash - Cash transactions</li>
                            <li>Netf - NEFT/RTGS/IMPS transfers</li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 mb-1">Credit vs Debit</p>
                        <ul class="list-disc list-inside space-y-1 ml-2">
                            <li>Credit - Money added to wallet</li>
                            <li>Debit - Money deducted from wallet</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTransactionType() {
            const mode = document.getElementById('mode').value;
            const categories = @json($categories);
            
            if (mode && categories[mode]) {
                // You can use this to populate transaction type suggestions if needed
                console.log('Available categories for', mode, ':', categories[mode]);
            }
        }

        // AWB Searchable Dropdown
        (function() {
            const awbSearch = document.getElementById('awb_search');
            const awbSelect = document.getElementById('awb_number');
            const awbDropdown = document.getElementById('awb_dropdown');
            
            if (!awbSearch || !awbSelect || !awbDropdown) {
                return; // Exit if elements don't exist
            }
            
            const awbOptions = Array.from(awbSelect.options).slice(1); // Skip first empty option
            let selectedAwb = null;
            
            // Initialize with selected value if any
            if (awbSelect.value) {
                const selectedOption = awbSelect.options[awbSelect.selectedIndex];
                if (selectedOption) {
                    awbSearch.value = selectedOption.text;
                    selectedAwb = awbSelect.value;
                }
            }
            
            // Build dropdown options
            function buildDropdown(filterText = '') {
                const filter = filterText.toLowerCase();
                const filteredOptions = awbOptions.filter(option => {
                    const text = option.text.toLowerCase();
                    const destination = (option.getAttribute('data-destination') || '').toLowerCase();
                    return text.includes(filter) || destination.includes(filter);
                });
                
                if (filteredOptions.length === 0) {
                    awbDropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">No AWB found</div>';
                    return;
                }
                
                awbDropdown.innerHTML = filteredOptions.map(option => {
                    const isSelected = option.value === selectedAwb ? 'bg-orange-50 border-orange-300' : 'hover:bg-gray-50';
                    const destination = option.getAttribute('data-destination') || '';
                    const displayText = option.text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                    return `
                        <div class="px-4 py-2 cursor-pointer border-b border-gray-100 ${isSelected}" 
                             data-value="${option.value}" 
                             onclick="selectAwb('${option.value}', '${displayText}')">
                            <div class="font-semibold text-gray-900">${option.value}</div>
                            ${destination ? '<div class="text-xs text-gray-500">' + destination + '</div>' : ''}
                        </div>
                    `;
                }).join('');
            }
            
            // Show dropdown
            function showDropdown() {
                awbDropdown.style.display = 'block';
                buildDropdown(awbSearch.value);
            }
            
            // Hide dropdown
            function hideDropdown() {
                setTimeout(() => {
                    awbDropdown.style.display = 'none';
                }, 200);
            }
            
            // Select AWB
            window.selectAwb = function(value, text) {
                awbSelect.value = value;
                awbSearch.value = text;
                selectedAwb = value;
                hideDropdown();
            };
            
            // Search input events
            awbSearch.addEventListener('focus', showDropdown);
            awbSearch.addEventListener('input', function(e) {
                buildDropdown(e.target.value);
                if (e.target.value === '') {
                    awbSelect.value = '';
                    selectedAwb = null;
                }
            });
            
            // Click outside to close
            const awbContainer = document.getElementById('awb_container');
            document.addEventListener('click', function(e) {
                if (awbContainer && !awbContainer.contains(e.target)) {
                    hideDropdown();
                }
            });
            
            // Initialize dropdown
            buildDropdown();
        })();
    </script>
@endsection
