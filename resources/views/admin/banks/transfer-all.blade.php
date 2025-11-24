@extends('layouts.admin')

@section('title', 'All Bank Transfers')

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
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: linear-gradient(135deg, #fff5ed 0%, #ffe8d6 100%);
        }
        th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #FF750F;
        }
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            color: #374151;
        }
        tr:hover {
            background: #f9fafb;
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
        .text-green {
            color: #059669;
            font-weight: 600;
        }
        .text-red {
            color: #dc2626;
            font-weight: 600;
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Bank Transfer Report</h1>
                    <p class="text-xs text-gray-600">View all money transfers with credit, debit, and balance</p>
                </div>
            </div>
            <a href="{{ route('admin.banks.transfer') }}" class="admin-btn-primary px-4 py-2 text-sm">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Transfer
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="form-card p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Search & Filter
        </h2>
        <form method="GET" action="{{ route('admin.banks.transfer.all') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Search</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $searchParams['search'] ?? '' }}" 
                    placeholder="Bank name, transaction no..." 
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"
                >
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Date From</label>
                <input 
                    type="date" 
                    name="date_from" 
                    value="{{ $searchParams['date_from'] ?? '' }}" 
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"
                >
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Date To</label>
                <input 
                    type="date" 
                    name="date_to" 
                    value="{{ $searchParams['date_to'] ?? '' }}" 
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"
                >
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Or Select Month</label>
                <input 
                    type="month" 
                    name="month" 
                    value="{{ $searchParams['month'] ?? '' }}" 
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"
                >
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="admin-btn-primary px-6 py-2 flex-1">
                    <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search
                </button>
                @if($searchParams['search'] ?? '' || $searchParams['date_from'] ?? '' || $searchParams['date_to'] ?? '' || $searchParams['month'] ?? '')
                    <a href="{{ route('admin.banks.transfer.all') }}" class="px-6 py-2 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
        
        <!-- Export Button -->
        <div class="mt-4 flex justify-end">
            <a href="{{ route('admin.banks.transfer.export', $searchParams) }}" class="admin-btn-secondary px-6 py-2 text-sm">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export to Excel
            </a>
        </div>
    </div>

    <!-- Transfers List -->
    <div class="form-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Transfer Transactions
            </h2>
            <span class="text-sm text-gray-600 font-semibold">Total: {{ count($transfers) }} Transaction(s)</span>
        </div>

        @if(count($transfers) > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Transaction No.</th>
                            <th>Bank Account</th>
                            <th>Type</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            <tr>
                                <td>
                                    <div class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($transfer['date'] ?? now())->format('d M Y, h:i A') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $transfer['transaction_no'] ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="font-semibold text-gray-900">{{ $transfer['bank_account'] ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    @if(($transfer['type'] ?? '') === 'Credit')
                                        <span class="badge badge-success">Credit</span>
                                    @else
                                        <span class="badge badge-danger">Debit</span>
                                    @endif
                                </td>
                                <td>
                                    @if(($transfer['debit'] ?? 0) > 0)
                                        <span class="text-red">₹{{ number_format($transfer['debit'], 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(($transfer['credit'] ?? 0) > 0)
                                        <span class="text-green">₹{{ number_format($transfer['credit'], 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-bold text-gray-900">₹{{ number_format($transfer['balance'] ?? 0, 2) }}</span>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $transfer['remark'] ?? '' }}">
                                        {{ $transfer['remark'] ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <p class="text-gray-600 font-semibold mb-2">No transfers found</p>
                <p class="text-sm text-gray-500 mb-4">Start by creating your first bank transfer</p>
                <a href="{{ route('admin.banks.transfer') }}" class="admin-btn-primary px-6 py-2 text-sm inline-block">
                    Create Transfer
                </a>
            </div>
        @endif
    </div>
@endsection
