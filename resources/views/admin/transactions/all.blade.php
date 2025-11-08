@extends('layouts.admin')

@section('title', 'All Transactions')

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
        .transaction-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
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
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .transaction-table tbody td {
            padding: 14px 16px;
            font-size: 13px;
            color: #374151;
        }
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
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
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(255, 117, 15, 0.1);
            color: #FF750F;
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
        .search-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            padding: 20px 24px;
            margin-bottom: 20px;
        }
        .search-section label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        .search-input, .search-select {
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
        .search-input:focus, .search-select:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
        .search-input:hover, .search-select:hover {
            border-color: #d1d5db;
        }
        .search-input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }
        .search-select {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23374151'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }
        .stats-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            padding: 20px;
            margin-bottom: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .stat-item {
            text-align: center;
            padding: 16px;
            border-radius: 8px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-value.credit {
            color: #10b981;
        }
        .stat-value.debit {
            color: #ef4444;
        }
        .stat-value.balance {
            color: #FF750F;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">All Transactions</h1>
                    <p class="text-xs text-gray-600">Manage and track all network transactions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-card">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value credit">₹{{ number_format($totalCredits, 2) }}</div>
                <div class="stat-label">Total Credits</div>
            </div>
            <div class="stat-item">
                <div class="stat-value debit">₹{{ number_format($totalDebits, 2) }}</div>
                <div class="stat-label">Total Debits</div>
            </div>
            <div class="stat-item">
                <div class="stat-value balance">₹{{ number_format($netBalance, 2) }}</div>
                <div class="stat-label">Net Balance</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" style="color: #6366f1;">{{ $transactions->total() }}</div>
                <div class="stat-label">Total Transactions</div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Search & Filter
        </h2>
        
        <form method="GET" action="{{ route('admin.transactions.all') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search (AWB/Booking ID/Description)
                </label>
                <input type="text" name="search" value="{{ $searchParams['search'] ?? '' }}" class="search-input" placeholder="Search transactions...">
            </div>
            
            <!-- Network Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                    Network
                </label>
                <select name="network_id" class="search-select">
                    <option value="">All Networks</option>
                    @foreach($networks as $network)
                        <option value="{{ $network->id }}" {{ ($searchParams['network_id'] ?? '') == $network->id ? 'selected' : '' }}>
                            {{ $network->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Type Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Type
                </label>
                <select name="type" class="search-select">
                    <option value="">All Types</option>
                    <option value="credit" {{ ($searchParams['type'] ?? '') == 'credit' ? 'selected' : '' }}>Credit</option>
                    <option value="debit" {{ ($searchParams['type'] ?? '') == 'debit' ? 'selected' : '' }}>Debit</option>
                </select>
            </div>
            
            <!-- Transaction Type Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Transaction Type
                </label>
                <select name="transaction_type" class="search-select">
                    <option value="">All Types</option>
                    <option value="booking" {{ ($searchParams['transaction_type'] ?? '') == 'booking' ? 'selected' : '' }}>Booking</option>
                    <option value="price_change" {{ ($searchParams['transaction_type'] ?? '') == 'price_change' ? 'selected' : '' }}>Price Change</option>
                    <option value="network_change" {{ ($searchParams['transaction_type'] ?? '') == 'network_change' ? 'selected' : '' }}>Network Change</option>
                </select>
            </div>
            
            <!-- Date From -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Date From
                </label>
                <input type="date" name="date_from" value="{{ $searchParams['date_from'] ?? '' }}" class="search-input">
            </div>
            
            <!-- Date To -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Date To
                </label>
                <input type="date" name="date_to" value="{{ $searchParams['date_to'] ?? '' }}" class="search-input">
            </div>
            
            <!-- Search Button -->
            <div class="flex items-end gap-2 md:col-span-2 lg:col-span-4">
                <button type="submit" class="admin-btn-primary px-6 py-2.5 text-sm font-semibold">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search</span>
                    </div>
                </button>
                @if($searchParams['search'] ?? '' || $searchParams['network_id'] ?? '' || $searchParams['type'] ?? '' || $searchParams['transaction_type'] ?? '' || $searchParams['date_from'] ?? '' || $searchParams['date_to'] ?? '')
                    <a href="{{ route('admin.transactions.all') }}" class="px-4 py-2.5 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="transaction-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Transaction List
            </h2>
            <div class="text-sm text-gray-600 font-medium">
                Total: <span class="font-bold text-orange-600">{{ $transactions->total() }}</span> Transactions
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

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="transaction-table min-w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Network</th>
                        <th>Type</th>
                        <th>Transaction Type</th>
                        <th>Amount</th>
                        <th>Balance Before</th>
                        <th>Balance After</th>
                        <th>AWB/Booking ID</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="font-bold text-gray-900">#{{ $transaction->id }}</td>
                            <td class="text-xs">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="transaction-type-badge">{{ $transaction->network->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="type-badge {{ $transaction->type }}">
                                    @if($transaction->type == 'credit')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Credit
                                    @else
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Debit
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="transaction-type-badge">{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</span>
                            </td>
                            <td class="text-xs font-bold {{ $transaction->type == 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs font-semibold">₹{{ number_format($transaction->balance_before, 2) }}</td>
                            <td class="text-xs font-bold text-orange-600">₹{{ number_format($transaction->balance_after, 2) }}</td>
                            <td class="text-xs">
                                @if($transaction->awb_no)
                                    <span class="font-semibold text-orange-600">{{ $transaction->awb_no }}</span>
                                    @if($transaction->booking_id && strpos($transaction->booking_id, 'DE-') === 0)
                                        <span class="text-xs text-gray-500">(Direct Entry)</span>
                                    @endif
                                @elseif($transaction->booking_id)
                                    @if(strpos($transaction->booking_id, 'DE-') === 0)
                                        <span class="font-semibold text-purple-600">Direct Entry #{{ str_replace('DE-', '', $transaction->booking_id) }}</span>
                                    @else
                                        <span class="font-semibold text-gray-600">Booking #{{ $transaction->booking_id }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="text-xs max-w-xs truncate" title="{{ $transaction->description }}">
                                {{ $transaction->description ?? '-' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-0">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">No Transactions Found</h3>
                                    <p class="text-gray-600 mb-4 max-w-md mx-auto">No transactions have been recorded yet. Transactions will appear here when bookings are created or updated.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection

