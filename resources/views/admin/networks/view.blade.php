@extends('layouts.admin')

@section('title', 'View Network')

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
        .network-card {
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
        .stat-card.awb {
            border-color: rgba(255, 117, 15, 0.3);
            background: linear-gradient(135deg, #fff5ed 0%, #ffe4d6 100%);
        }
        .transaction-table, .awb-table {
            width: 100%;
        }
        .transaction-table thead, .awb-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .transaction-table thead th, .awb-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .transaction-table tbody tr, .awb-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .transaction-table tbody tr:hover, .awb-table tbody tr:hover {
            background: #fff5ed;
        }
        .transaction-table tbody td, .awb-table tbody td {
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
        .badge-type {
            background: rgba(255, 117, 15, 0.1);
            color: #FF750F;
        }
        .badge-status {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }
    </style>

    @php
        $networkName = is_object($network) && isset($network->name) ? $network->name : (isset($network['name']) ? $network['name'] : '');
        $networkType = is_object($network) && isset($network->type) ? $network->type : (isset($network['type']) ? $network['type'] : '');
        $networkStatus = is_object($network) && isset($network->status) ? $network->status : (isset($network['status']) ? $network['status'] : '');
        $bankDetails = is_object($network) && isset($network->bank_details) ? $network->bank_details : (isset($network['bank_details']) ? $network['bank_details'] : '');
        $upiScanner = is_object($network) && isset($network->upi_scanner) ? $network->upi_scanner : (isset($network['upi_scanner']) ? $network['upi_scanner'] : '');
        $remark = is_object($network) && isset($network->remark) ? $network->remark : (isset($network['remark']) ? $network['remark'] : '');
    @endphp

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.networks.all') }}" class="w-10 h-10 rounded-lg flex items-center justify-center hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">View Network</h1>
                    <p class="text-xs text-gray-600">{{ $networkName }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $networkId = is_object($network) && isset($network->id) ? $network->id : (isset($network['id']) ? $network['id'] : '');
                @endphp
                <a href="{{ route('admin.networks.edit', $networkId) }}" class="px-4 py-2 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Network
                </a>
            </div>
        </div>
    </div>

    <!-- Network Details -->
    <div class="network-card p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
            </svg>
            Network Information
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Network Name</label>
                <p class="text-gray-900 font-semibold mt-1">{{ $networkName }}</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Type</label>
                <p class="mt-1"><span class="badge badge-type">{{ $networkType }}</span></p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                <p class="mt-1"><span class="badge badge-status">{{ $networkStatus }}</span></p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Bank Details</label>
                <p class="text-gray-900 font-semibold mt-1">{{ $bankDetails ?: '-' }}</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">UPI Scanner</label>
                <p class="text-gray-900 font-semibold mt-1">{{ $upiScanner ?: '-' }}</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Remark</label>
                <p class="text-gray-900 font-semibold mt-1">{{ $remark ?: '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
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
        <div class="stat-card awb">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-600 uppercase">AWB Usage</span>
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-orange-700">{{ $totalAwbs }}</p>
            <p class="text-xs text-gray-600 mt-1">{{ $uniqueAwbCount }} unique AWBs</p>
        </div>
    </div>

    <!-- AWB Statistics -->
    @if($totalAwbs > 0)
        <div class="network-card p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                AWB Statistics
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Total AWB Amount</p>
                    <p class="text-xl font-bold text-orange-700">₹{{ number_format($totalAwbAmount, 2) }}</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Total Weight (KG)</p>
                    <p class="text-xl font-bold text-orange-700">{{ number_format($totalAwbWeight, 2) }} KG</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Average Amount/AWB</p>
                    <p class="text-xl font-bold text-orange-700">₹{{ $totalAwbs > 0 ? number_format($totalAwbAmount / $totalAwbs, 2) : '0.00' }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Credit Transactions -->
    @if(count($creditTransactions) > 0)
        <div class="network-card p-6 mb-6">
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
                            <th>AWB No.</th>
                            <th>Booking ID</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Balance Before</th>
                            <th>Balance After</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($creditTransactions as $transaction)
                            <tr>
                                <td class="font-bold text-gray-900">#{{ $transaction['id'] }}</td>
                                <td>{{ $transaction['awb_no'] ?: '-' }}</td>
                                <td>{{ $transaction['booking_id'] ?: '-' }}</td>
                                <td><span class="badge badge-credit">{{ $transaction['transaction_type'] ?? 'booking' }}</span></td>
                                <td><span class="font-bold text-green-700">₹{{ number_format($transaction['amount'], 2) }}</span></td>
                                <td>₹{{ number_format($transaction['balance_before'], 2) }}</td>
                                <td>₹{{ number_format($transaction['balance_after'], 2) }}</td>
                                <td class="text-sm">{{ $transaction['description'] ?: '-' }}</td>
                                <td>{{ isset($transaction['created_at']) ? \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y, h:i A') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Debit Transactions -->
    @if(count($debitTransactions) > 0)
        <div class="network-card p-6 mb-6">
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
                            <th>AWB No.</th>
                            <th>Booking ID</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Balance Before</th>
                            <th>Balance After</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($debitTransactions as $transaction)
                            <tr>
                                <td class="font-bold text-gray-900">#{{ $transaction['id'] }}</td>
                                <td>{{ $transaction['awb_no'] ?: '-' }}</td>
                                <td>{{ $transaction['booking_id'] ?: '-' }}</td>
                                <td><span class="badge badge-debit">{{ $transaction['transaction_type'] ?? 'booking' }}</span></td>
                                <td><span class="font-bold text-red-700">₹{{ number_format($transaction['amount'], 2) }}</span></td>
                                <td>₹{{ number_format($transaction['balance_before'], 2) }}</td>
                                <td>₹{{ number_format($transaction['balance_after'], 2) }}</td>
                                <td class="text-sm">{{ $transaction['description'] ?: '-' }}</td>
                                <td>{{ isset($transaction['created_at']) ? \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y, h:i A') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- AWB Uploads -->
    @if(count($awbUploads) > 0)
        <div class="network-card p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                AWB Uploads ({{ count($awbUploads) }})
            </h2>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="awb-table min-w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>AWB No.</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Service</th>
                            <th>Weight (KG)</th>
                            <th>Amount</th>
                            <th>Date of Sale</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($awbUploads as $awb)
                            <tr>
                                <td class="font-bold text-gray-900">#{{ $awb['id'] }}</td>
                                <td class="font-semibold">{{ $awb['awb_no'] }}</td>
                                <td>{{ $awb['origin'] ?: '-' }}</td>
                                <td>{{ $awb['destination'] ?: '-' }}</td>
                                <td>{{ $awb['service_name'] ?: '-' }}</td>
                                <td>{{ $awb['chargeable_weight'] ? number_format($awb['chargeable_weight'], 2) : '-' }}</td>
                                <td><span class="font-bold text-orange-700">₹{{ $awb['amour'] ? number_format($awb['amour'], 2) : '0.00' }}</span></td>
                                <td>{{ $awb['date_of_sale'] ? \Carbon\Carbon::parse($awb['date_of_sale'])->format('d M Y') : '-' }}</td>
                                <td>{{ isset($awb['created_at']) ? \Carbon\Carbon::parse($awb['created_at'])->format('d M Y, h:i A') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(count($creditTransactions) == 0 && count($debitTransactions) == 0 && count($awbUploads) == 0)
        <div class="network-card p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Transactions or AWBs Found</h3>
            <p class="text-gray-600 text-sm">This network has no transactions or AWB uploads yet.</p>
        </div>
    @endif
@endsection

