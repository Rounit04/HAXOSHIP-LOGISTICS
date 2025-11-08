@extends('layouts.admin')

@section('title', 'Transaction Details')

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
        .detail-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            padding: 24px;
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            padding: 16px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            width: 200px;
            flex-shrink: 0;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            flex: 1;
        }
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-badge.credit {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }
        .type-badge.debit {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }
        .transaction-type-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(255, 117, 15, 0.1);
            color: #FF750F;
        }
        .amount-display {
            font-size: 28px;
            font-weight: 700;
            margin: 16px 0;
        }
        .amount-display.credit {
            color: #10b981;
        }
        .amount-display.debit {
            color: #ef4444;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.transactions.all') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Transaction Details</h1>
                    <p class="text-xs text-gray-600">Transaction #{{ $transaction->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="detail-card">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900">Transaction Information</h2>
            <span class="type-badge {{ $transaction->type }}">
                @if($transaction->type == 'credit')
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Credit
                @else
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Debit
                @endif
            </span>
        </div>

        <div class="text-center py-6 border-b-2 border-gray-200 mb-6">
            <div class="amount-display {{ $transaction->type }}">
                {{ $transaction->type == 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
            </div>
            <p class="text-sm text-gray-600">{{ $transaction->description }}</p>
        </div>

        <div class="space-y-0">
            <div class="detail-row">
                <div class="detail-label">Transaction ID</div>
                <div class="detail-value font-bold">#{{ $transaction->id }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Date & Time</div>
                <div class="detail-value">{{ $transaction->created_at->format('F d, Y h:i A') }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Network</div>
                <div class="detail-value">
                    <span class="transaction-type-badge">{{ $transaction->network->name ?? 'N/A' }}</span>
                    @if($transaction->network)
                        <span class="text-xs text-gray-500 ml-2">({{ $transaction->network->type }})</span>
                    @endif
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Transaction Type</div>
                <div class="detail-value">
                    <span class="transaction-type-badge">{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Amount</div>
                <div class="detail-value font-bold {{ $transaction->type == 'credit' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type == 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Balance Before</div>
                <div class="detail-value">₹{{ number_format($transaction->balance_before, 2) }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Balance After</div>
                <div class="detail-value font-bold text-orange-600">₹{{ number_format($transaction->balance_after, 2) }}</div>
            </div>
            @if($transaction->awb_no)
                <div class="detail-row">
                    <div class="detail-label">AWB Number</div>
                    <div class="detail-value">
                        <span class="font-semibold text-orange-600">{{ $transaction->awb_no }}</span>
                    </div>
                </div>
            @endif
            @if($transaction->booking_id)
                <div class="detail-row">
                    <div class="detail-label">Booking ID</div>
                    <div class="detail-value">
                        <span class="font-semibold text-gray-600">#{{ $transaction->booking_id }}</span>
                    </div>
                </div>
            @endif
            <div class="detail-row">
                <div class="detail-label">Description</div>
                <div class="detail-value">{{ $transaction->description ?? '-' }}</div>
            </div>
            @if($transaction->notes)
                <div class="detail-row">
                    <div class="detail-label">Notes</div>
                    <div class="detail-value">{{ $transaction->notes }}</div>
                </div>
            @endif
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.transactions.all') }}" class="admin-btn-primary px-6 py-2.5 text-sm font-semibold inline-block">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to Transactions</span>
                </div>
            </a>
        </div>
    </div>
@endsection

