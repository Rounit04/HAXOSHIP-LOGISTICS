@extends('layouts.admin')

@section('title', 'All Payments')

@php
    use Illuminate\Support\Str;
@endphp

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
        .payment-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .payment-table {
            width: 100%;
        }
        .payment-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .payment-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .payment-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .payment-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .payment-table tbody td {
            padding: 14px 16px;
            font-size: 14px;
            color: #374151;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(255, 117, 15, 0.1);
            color: #FF750F;
        }
        .amount-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }
        .empty-state {
            padding: 60px 40px;
            text-align: center;
            background: linear-gradient(135deg, #fff5ed 0%, #fff5ed 100%);
            border-radius: 12px;
            border: 2px dashed #d1d5db;
        }
        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #fff5ed 0%, #e0ddff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(255, 117, 15, 0.15);
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">All Payments</h1>
                    <p class="text-xs text-gray-600">View and manage all payment records</p>
                </div>
            </div>
            <a href="{{ route('admin.payments.create') }}" class="admin-btn-primary px-4 py-2 text-sm font-semibold">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Add Payment</span>
                </div>
            </a>
        </div>
    </div>

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

    @if(session('error'))
        <div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-200 rounded-xl flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <p class="text-red-700 font-bold text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <div class="payment-card">
        @if(count($payments) > 0)
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="payment-table min-w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Network</th>
                            <th>Transaction No</th>
                            <th>Bank Account</th>
                            <th>Mode</th>
                            <th>Payment To</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Remark</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td class="font-bold text-gray-900">#{{ $payment['id'] ?? '-' }}</td>
                                <td><span class="badge">{{ $payment['network'] ?? '-' }}</span></td>
                                <td><span class="badge">{{ $payment['transaction_no'] ?? '-' }}</span></td>
                                <td class="font-semibold text-gray-700 text-sm">{{ Str::limit($payment['bank_account'] ?? '-', 25) }}</td>
                                <td><span class="badge">{{ $payment['mode_of_payment'] ?? '-' }}</span></td>
                                <td><span class="badge" style="text-transform: capitalize;">{{ $payment['payment_to'] ?? '-' }}</span></td>
                                <td class="text-sm">{{ $payment['category'] ?? '-' }}</td>
                                <td><span class="amount-badge">₹{{ number_format($payment['amount'] ?? 0, 2) }}</span></td>
                                <td class="text-sm">{{ Str::limit($payment['remark'] ?? '-', 30) }}</td>
                                <td class="text-xs text-gray-500">{{ isset($payment['created_at']) ? \Carbon\Carbon::parse($payment['created_at'])->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.payments.edit', $payment['id']) }}" class="p-2 rounded-lg bg-blue-50 hover:bg-blue-100 transition text-blue-600" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.payments.delete', $payment['id']) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-red-50 hover:bg-red-100 transition text-red-600" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No Payments Found</h3>
                <p class="text-gray-600 mb-4">Get started by adding your first payment record.</p>
                <a href="{{ route('admin.payments.create') }}" class="admin-btn-primary px-6 py-3 text-sm font-semibold inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Add Payment</span>
                </a>
            </div>
        @endif
    </div>
@endsection
