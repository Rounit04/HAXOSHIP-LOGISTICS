@extends('layouts.admin')

@section('title', 'View Bank')

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
        .bank-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .stat-card.credit {
            border-color: rgba(34, 197, 94, 0.3);
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
        .stat-card.debit {
            border-color: rgba(239, 68, 68, 0.3);
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }
        .stat-card.balance {
            border-color: rgba(59, 130, 246, 0.3);
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }
        .transaction-table {
            width: 100%;
        }
        .transaction-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .transaction-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .transaction-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .transaction-table tbody tr:hover {
            background: #fff5ed;
        }
        .transaction-table tbody td {
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
        }
        .badge-credit {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }
        .badge-debit {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        .badge-ifsc {
            background: rgba(255, 117, 15, 0.1);
            color: #FF750F;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.banks.all') }}" class="w-10 h-10 rounded-lg flex items-center justify-center hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">View Bank</h1>
                    <p class="text-xs text-gray-600">{{ $bank['bank_name'] }} - {{ $bank['account_number'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.banks.edit', $bank['id']) }}" class="px-4 py-2 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Bank
                </a>
            </div>
        </div>
    </div>

    <!-- Bank Details -->
    <div class="bank-card p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Bank Information
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Bank Name</label>
                <p class="text-gray-900 font-semibold mt-1">{{ $bank['bank_name'] }}</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Account Holder Name</label>
                <p class="text-gray-900 font-semibold mt-1">{{ $bank['account_holder_name'] }}</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Account Number</label>
                <p class="text-gray-900 font-semibold mt-1">{{ $bank['account_number'] }}</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">IFSC Code</label>
                <p class="mt-1"><span class="badge badge-ifsc">{{ $bank['ifsc_code'] }}</span></p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card balance">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-600 uppercase">Opening Balance</span>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-blue-700">₹{{ number_format($openingBalance, 2) }}</p>
        </div>
        <div class="stat-card credit">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-600 uppercase">Total Credits</span>
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-700">₹{{ number_format($totalCredits, 2) }}</p>
            <p class="text-xs text-gray-600 mt-1">{{ count($creditTransactions) }} transactions</p>
        </div>
        <div class="stat-card debit">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-600 uppercase">Total Debits</span>
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-red-700">₹{{ number_format($totalDebits, 2) }}</p>
            <p class="text-xs text-gray-600 mt-1">{{ count($debitTransactions) }} transactions</p>
        </div>
        <div class="stat-card balance">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-600 uppercase">Current Balance</span>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-blue-700">₹{{ number_format($currentBalance, 2) }}</p>
            <p class="text-xs text-gray-600 mt-1">{{ $totalTransactions }} total transactions</p>
        </div>
    </div>

    <!-- Credit Transactions -->
    @if(count($creditTransactions) > 0)
        <div class="bank-card p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Credit Transactions ({{ count($creditTransactions) }})
            </h2>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="transaction-table min-w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Transaction No.</th>
                            <th>Mode</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Remark</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($creditTransactions as $transaction)
                            <tr>
                                <td class="font-bold text-gray-900">#{{ $transaction['id'] ?? '' }}</td>
                                <td>{{ $transaction['transaction_no'] ?? '' }}</td>
                                <td>{{ $transaction['mode_of_payment'] ?? '' }}</td>
                                <td>{{ $transaction['category_bank'] ?? '' }}</td>
                                <td><span class="badge badge-credit font-bold">₹{{ number_format($transaction['amount'] ?? 0, 2) }}</span></td>
                                <td>{{ $transaction['remark'] ?? '' }}</td>
                                <td>{{ isset($transaction['created_at']) ? \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y, h:i A') : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Debit Transactions -->
    @if(count($debitTransactions) > 0)
        <div class="bank-card p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
                Debit Transactions ({{ count($debitTransactions) }})
            </h2>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="transaction-table min-w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Transaction No.</th>
                            <th>Mode</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Remark</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($debitTransactions as $transaction)
                            <tr>
                                <td class="font-bold text-gray-900">#{{ $transaction['id'] ?? '' }}</td>
                                <td>{{ $transaction['transaction_no'] ?? '' }}</td>
                                <td>{{ $transaction['mode_of_payment'] ?? '' }}</td>
                                <td>{{ $transaction['category_bank'] ?? '' }}</td>
                                <td><span class="badge badge-debit font-bold">₹{{ number_format($transaction['amount'] ?? 0, 2) }}</span></td>
                                <td>{{ $transaction['remark'] ?? '' }}</td>
                                <td>{{ isset($transaction['created_at']) ? \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y, h:i A') : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(count($creditTransactions) == 0 && count($debitTransactions) == 0)
        <div class="bank-card p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Transactions Found</h3>
            <p class="text-gray-600 text-sm">This bank account has no credit or debit transactions yet.</p>
        </div>
    @endif
@endsection

