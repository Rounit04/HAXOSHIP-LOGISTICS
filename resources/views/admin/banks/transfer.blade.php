@extends('layouts.admin')

@section('title', 'Bank Transfer')

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
        .balance-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 8px;
        }
        .balance-info strong {
            color: #0369a1;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Bank Transfer</h1>
                    <p class="text-xs text-gray-600">Transfer money from one bank account to another</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transfer Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Transfer Money
                </h2>

                <form method="POST" action="{{ route('admin.banks.transfer.store') }}" id="transferForm">
                    @csrf

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

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
                        </div>
                    @endif

                    <!-- From Bank -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            From Bank (Source) <span class="required">*</span>
                        </label>
                        <select name="from_bank" id="from_bank" class="form-select" required>
                            <option value="">Select Source Bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank['bank_name'] }} - {{ $bank['account_number'] }}" data-balance="{{ $bank['opening_balance'] ?? 0 }}">
                                    {{ $bank['bank_name'] }} - {{ $bank['account_number'] }} ({{ $bank['account_holder_name'] }})
                                </option>
                            @endforeach
                        </select>
                        <div id="from_bank_balance" class="balance-info hidden">
                            <strong>Available Balance:</strong> <span id="from_balance_amount">₹0.00</span>
                        </div>
                    </div>

                    <!-- To Bank -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            To Bank (Destination) <span class="required">*</span>
                        </label>
                        <select name="to_bank" id="to_bank" class="form-select" required>
                            <option value="">Select Destination Bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank['bank_name'] }} - {{ $bank['account_number'] }}">
                                    {{ $bank['bank_name'] }} - {{ $bank['account_number'] }} ({{ $bank['account_holder_name'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount & Transaction Number -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Transfer Amount <span class="required">*</span>
                            </label>
                            <input type="number" name="amount" id="amount" class="form-input" step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Transaction Number <span class="required">*</span>
                            </label>
                            <input type="text" name="transaction_no" id="transaction_no" class="form-input" placeholder="Enter transaction/UTR number" required>
                        </div>
                    </div>

                    <!-- Remark -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Remark (Optional)
                        </label>
                        <textarea name="remark" id="remark" class="form-textarea" rows="3" placeholder="Add any additional notes about this transfer"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                <span>Transfer Money</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.banks.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
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
                    Transfer Guidelines
                </h3>
                <div class="space-y-3 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Source and destination banks must be different</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Source bank must have sufficient balance</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Transfer will create debit and credit transactions</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>Transaction number is required for tracking</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span>All transfers are recorded in payment history</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                    <a href="{{ route('admin.banks.transfer.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-orange-600 hover:bg-purple-50 rounded-lg transition text-center">
                        View All Transfers
                    </a>
                    <a href="{{ route('admin.banks.all') }}" class="w-full block px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 rounded-lg transition text-center">
                        View All Banks
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fromBankSelect = document.getElementById('from_bank');
            const toBankSelect = document.getElementById('to_bank');
            const fromBankBalance = document.getElementById('from_bank_balance');
            const fromBalanceAmount = document.getElementById('from_balance_amount');
            const allPayments = @json($payments ?? []);

            // Update balance when from bank changes
            fromBankSelect?.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const openingBalance = parseFloat(selectedOption.getAttribute('data-balance')) || 0;
                    const bankAccount = selectedOption.value;
                    
                    // Calculate current balance
                    const bankPayments = allPayments.filter(p => p.bank_account === bankAccount);
                    const credits = bankPayments.filter(p => p.type === 'Credit').reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0);
                    const debits = bankPayments.filter(p => p.type === 'Debit').reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0);
                    const currentBalance = openingBalance + credits - debits;
                    
                    fromBalanceAmount.textContent = '₹' + currentBalance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    fromBankBalance.classList.remove('hidden');
                } else {
                    fromBankBalance.classList.add('hidden');
                }
            });

            // Prevent selecting same bank for from and to
            fromBankSelect?.addEventListener('change', function() {
                const fromValue = this.value;
                if (fromValue && toBankSelect.value === fromValue) {
                    toBankSelect.value = '';
                }
            });

            toBankSelect?.addEventListener('change', function() {
                const toValue = this.value;
                if (toValue && fromBankSelect.value === toValue) {
                    fromBankSelect.value = '';
                    fromBankBalance.classList.add('hidden');
                }
            });
        });
    </script>
@endsection

