@extends('layouts.admin')

@section('title', 'Transfer Details')

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
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
        .balance-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 8px;
            padding: 16px;
            margin-top: 12px;
        }
        .balance-box h4 {
            font-size: 12px;
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .balance-amount {
            font-size: 20px;
            font-weight: 800;
            color: #0c4a6e;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Transfer Details</h1>
                    <p class="text-xs text-gray-600">View complete details of bank transfer transaction</p>
                </div>
            </div>
            <a href="{{ route('admin.banks.transfer.all') }}" class="px-4 py-2 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transfer Information -->
        <div class="form-card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Transfer Information
            </h2>

            <div class="space-y-1">
                <div class="info-row">
                    <span class="info-label">Transaction Number</span>
                    <span class="badge badge-info">{{ $transfer['transaction_no'] ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transfer Amount</span>
                    <span class="info-value text-green-600">₹{{ number_format($transfer['amount'] ?? 0, 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date & Time</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($transfer['created_at'] ?? now())->format('d M Y, h:i A') }}</span>
                </div>
                @if($transfer['remark'] ?? '')
                    <div class="info-row">
                        <span class="info-label">Remark</span>
                        <span class="info-value text-sm">{{ $transfer['remark'] }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Source Bank (Debit) -->
        <div class="form-card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Source Bank (Debit)
            </h2>

            <div class="space-y-1">
                <div class="info-row">
                    <span class="info-label">Bank Name</span>
                    <span class="info-value">{{ $fromBank['bank_name'] ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Account Number</span>
                    <span class="info-value">{{ $fromBank['account_number'] ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Account Holder</span>
                    <span class="info-value">{{ $fromBank['account_holder_name'] ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">IFSC Code</span>
                    <span class="info-value">{{ $fromBank['ifsc_code'] ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Debit Amount</span>
                    <span class="info-value text-red-600">- ₹{{ number_format($debitTransaction['amount'] ?? 0, 2) }}</span>
                </div>
            </div>

            <div class="balance-box">
                <h4>Balance Impact</h4>
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-xs text-gray-600">Before Transfer</div>
                        <div class="balance-amount">₹{{ number_format($fromBankBalanceBefore, 2) }}</div>
                    </div>
                    <div class="text-gray-400">→</div>
                    <div>
                        <div class="text-xs text-gray-600">After Transfer</div>
                        <div class="balance-amount">₹{{ number_format($fromBankBalanceAfter, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Destination Bank (Credit) -->
        <div class="form-card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Destination Bank (Credit)
            </h2>

            <div class="space-y-1">
                <div class="info-row">
                    <span class="info-label">Bank Name</span>
                    <span class="info-value">{{ $toBank['bank_name'] ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Account Number</span>
                    <span class="info-value">{{ $toBank['account_number'] ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Account Holder</span>
                    <span class="info-value">{{ $toBank['account_holder_name'] ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">IFSC Code</span>
                    <span class="info-value">{{ $toBank['ifsc_code'] ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Credit Amount</span>
                    <span class="info-value text-green-600">+ ₹{{ number_format($creditTransaction['amount'] ?? 0, 2) }}</span>
                </div>
            </div>

            <div class="balance-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #22c55e;">
                <h4 style="color: #15803d;">Balance Impact</h4>
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-xs text-gray-600">Before Transfer</div>
                        <div class="balance-amount" style="color: #14532d;">₹{{ number_format($toBankBalanceBefore, 2) }}</div>
                    </div>
                    <div class="text-gray-400">→</div>
                    <div>
                        <div class="text-xs text-gray-600">After Transfer</div>
                        <div class="balance-amount" style="color: #14532d;">₹{{ number_format($toBankBalanceAfter, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Summary -->
        <div class="form-card p-6 lg:col-span-2">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Transaction Summary
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-red-50 border-2 border-red-200 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                        <span class="font-bold text-red-900">Debit Transaction</span>
                    </div>
                    <div class="text-sm text-gray-700">
                        <div><strong>Bank:</strong> {{ $debitTransaction['bank_account'] ?? 'N/A' }}</div>
                        <div><strong>Amount:</strong> <span class="text-red-600 font-bold">₹{{ number_format($debitTransaction['amount'] ?? 0, 2) }}</span></div>
                        <div><strong>Type:</strong> <span class="badge badge-danger">Debit</span></div>
                        <div><strong>Category:</strong> {{ $debitTransaction['category_bank'] ?? 'N/A' }}</div>
                        <div><strong>Mode:</strong> {{ $debitTransaction['mode_of_payment'] ?? 'N/A' }}</div>
                        <div><strong>Remark:</strong> {{ $debitTransaction['remark'] ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="font-bold text-green-900">Credit Transaction</span>
                    </div>
                    <div class="text-sm text-gray-700">
                        <div><strong>Bank:</strong> {{ $creditTransaction['bank_account'] ?? 'N/A' }}</div>
                        <div><strong>Amount:</strong> <span class="text-green-600 font-bold">₹{{ number_format($creditTransaction['amount'] ?? 0, 2) }}</span></div>
                        <div><strong>Type:</strong> <span class="badge badge-success">Credit</span></div>
                        <div><strong>Category:</strong> {{ $creditTransaction['category_bank'] ?? 'N/A' }}</div>
                        <div><strong>Mode:</strong> {{ $creditTransaction['mode_of_payment'] ?? 'N/A' }}</div>
                        <div><strong>Remark:</strong> {{ $creditTransaction['remark'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

