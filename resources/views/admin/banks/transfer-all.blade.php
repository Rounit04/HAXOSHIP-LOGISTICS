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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">All Bank Transfers</h1>
                    <p class="text-xs text-gray-600">View all money transfers between bank accounts</p>
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
        <form method="GET" action="{{ route('admin.banks.transfer.all') }}" class="flex gap-3">
            <input 
                type="text" 
                name="search" 
                value="{{ $searchParams['search'] ?? '' }}" 
                placeholder="Search by bank name, account number, or transaction number..." 
                class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"
            >
            <button type="submit" class="admin-btn-primary px-6 py-2">
                <svg class="w-4 h-4 text-white inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Search
            </button>
            @if($searchParams['search'] ?? '')
                <a href="{{ route('admin.banks.transfer.all') }}" class="px-6 py-2 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Transfers List -->
    <div class="form-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Transfers List
            </h2>
            <span class="text-sm text-gray-600 font-semibold">Total: {{ count($transfers) }} Transfer(s)</span>
        </div>

        @if(count($transfers) > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Transaction No.</th>
                            <th>From Bank</th>
                            <th>To Bank</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            <tr>
                                <td>
                                    <span class="badge badge-info">{{ $transfer['transaction_no'] ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="font-semibold text-gray-900">{{ $transfer['from_bank'] ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="font-semibold text-gray-900">{{ $transfer['to_bank'] ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <span class="font-bold text-green-600">₹{{ number_format($transfer['amount'] ?? 0, 2) }}</span>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($transfer['created_at'] ?? now())->format('d M Y, h:i A') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.banks.transfer.view', $transfer['debit_id']) }}" class="admin-btn-primary px-3 py-1.5 text-xs">
                                            <svg class="w-3.5 h-3.5 text-white inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View Details
                                        </a>
                                        <form action="{{ route('admin.banks.transfer.delete', $transfer['debit_id']) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this transfer? This will cancel the debit and credit transactions.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700 transition flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-white inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
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

