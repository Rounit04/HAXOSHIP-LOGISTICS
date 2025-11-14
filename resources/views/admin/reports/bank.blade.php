@extends('layouts.admin')

@section('title', 'Bank Report')

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
        .report-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .report-table {
            width: 100%;
        }
        .report-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .report-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .report-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .report-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .report-table tbody td {
            padding: 14px 16px;
            font-size: 13px;
            color: #374151;
        }
        .amount-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
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
        .badge-salary {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }
        .badge-expense {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        .badge-revenue {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }
        .summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 12px;
            padding: 16px;
            border: 2px solid rgba(0,0,0,0.06);
        }
        .form-input, .form-select {
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
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Bank Report</h1>
                    <p class="text-xs text-gray-600">Comprehensive bank reports with category, network, and expense breakdown</p>
                </div>
            </div>
            @php
                $exportParams = array_filter([
                    'date_from' => $dateFrom ?? request('date_from'),
                    'date_to' => $dateTo ?? request('date_to'),
                    'category' => $category ?? request('category'),
                    'network' => $networkName ?? request('network'),
                    'bank' => $bankId ?? request('bank'),
                    'search' => $search ?? request('search'),
                ]);
            @endphp
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.bank.export', $exportParams) }}" class="admin-btn-primary px-5 py-2.5 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export to Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="report-card p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filters
        </h2>
        <form method="GET" action="{{ route('admin.reports.bank') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Search</label>
                <input type="text" name="search" class="form-input" placeholder="Search by bank name, account..." value="{{ $search ?? request('search') }}">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">From Date</label>
                <input type="date" name="date_from" class="form-input" value="{{ $dateFrom ?? request('date_from') }}">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">To Date</label>
                <input type="date" name="date_to" class="form-input" value="{{ $dateTo ?? request('date_to') }}">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <option value="Salary" {{ ($category ?? request('category')) == 'Salary' ? 'selected' : '' }}>Salary</option>
                    <option value="Expense" {{ ($category ?? request('category')) == 'Expense' ? 'selected' : '' }}>Expense</option>
                    <option value="Revenue" {{ ($category ?? request('category')) == 'Revenue' ? 'selected' : '' }}>Revenue</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Network</label>
                <select name="network" class="form-select">
                    <option value="">All Networks</option>
                    @foreach($allNetworks ?? [] as $network)
                        <option value="{{ $network['name'] }}" {{ ($networkName ?? request('network')) == $network['name'] ? 'selected' : '' }}>
                            {{ $network['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Bank</label>
                <select name="bank" class="form-select">
                    <option value="">All Banks</option>
                    @foreach($allBanks ?? [] as $bank)
                        <option value="{{ $bank['id'] }}" {{ ($bankId ?? request('bank')) == $bank['id'] ? 'selected' : '' }}>
                            {{ $bank['bank_name'] }} - {{ $bank['account_number'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-6 flex items-center gap-2">
                <button type="submit" class="admin-btn-primary px-6 py-2.5 text-sm font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter</span>
                    </div>
                </button>
                    @if(($dateFrom ?? request('date_from')) || ($dateTo ?? request('date_to')) || ($category ?? request('category')) || ($networkName ?? request('network')) || ($bankId ?? request('bank')) || ($search ?? request('search')))
                    <a href="{{ route('admin.reports.bank') }}" class="px-4 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bank Reports -->
    <div class="report-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Bank Report Data
            </h2>
            <div class="text-sm text-gray-600 font-medium">
                Total: <span class="font-bold text-orange-600">{{ count($bankReports ?? []) }}</span> Banks
            </div>
        </div>

        @if(count($bankReports ?? []) > 0)
            @foreach($bankReports as $report)
                @php
                    $bank = $report['bank'];
                @endphp
                <div class="mb-6 border-2 border-gray-200 rounded-lg p-6">
                    <!-- Bank Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div class="summary-card">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Bank</p>
                            <p class="text-lg font-bold text-gray-900">{{ $bank['bank_name'] }}</p>
                            <p class="text-xs text-gray-600">{{ $bank['account_number'] }}</p>
                        </div>
                        <div class="summary-card">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Opening Balance</p>
                            <p class="text-lg font-bold text-blue-700">₹{{ number_format($bank['opening_balance'] ?? 0, 2) }}</p>
                        </div>
                        <div class="summary-card">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Current Balance</p>
                            <p class="text-lg font-bold text-green-700">₹{{ number_format($report['current_balance'] ?? 0, 2) }}</p>
                        </div>
                        <div class="summary-card">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Transactions</p>
                            <p class="text-lg font-bold text-orange-700">{{ $report['total_transactions'] ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="summary-card border-blue-300 bg-blue-50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-600 uppercase">Salary</span>
                                <span class="badge badge-salary">Salary</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Credits:</span>
                                    <span class="font-bold text-green-700">₹{{ number_format($report['salary_credits'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Debits:</span>
                                    <span class="font-bold text-red-700">₹{{ number_format($report['salary_debits'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm pt-1 border-t border-blue-200">
                                    <span class="font-semibold text-gray-900">Total:</span>
                                    <span class="font-bold text-blue-700">₹{{ number_format($report['salary_total'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="summary-card border-red-300 bg-red-50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-600 uppercase">Expense</span>
                                <span class="badge badge-expense">Expense</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Credits:</span>
                                    <span class="font-bold text-green-700">₹{{ number_format($report['expense_credits'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Debits:</span>
                                    <span class="font-bold text-red-700">₹{{ number_format($report['expense_debits'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm pt-1 border-t border-red-200">
                                    <span class="font-semibold text-gray-900">Total:</span>
                                    <span class="font-bold text-red-700">₹{{ number_format($report['expense_total'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="summary-card border-green-300 bg-green-50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-600 uppercase">Revenue</span>
                                <span class="badge badge-revenue">Revenue</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Credits:</span>
                                    <span class="font-bold text-green-700">₹{{ number_format($report['revenue_credits'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Debits:</span>
                                    <span class="font-bold text-red-700">₹{{ number_format($report['revenue_debits'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm pt-1 border-t border-green-200">
                                    <span class="font-semibold text-gray-900">Total:</span>
                                    <span class="font-bold text-green-700">₹{{ number_format($report['revenue_total'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    @if(count($report['transactions'] ?? []) > 0)
                        <div class="mt-4">
                            <h3 class="text-md font-bold text-gray-900 mb-3">Transactions</h3>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="report-table min-w-full">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Transaction No.</th>
                                            <th>Mode</th>
                                            <th>Category</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Remark</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($report['transactions'] as $transaction)
                                            <tr>
                                                <td class="font-bold text-gray-900">#{{ $transaction['id'] ?? '-' }}</td>
                                                <td>{{ $transaction['transaction_no'] ?? '-' }}</td>
                                                <td>{{ $transaction['mode_of_payment'] ?? '-' }}</td>
                                                <td>
                                                    @if(isset($transaction['category_bank']))
                                                        @if($transaction['category_bank'] == 'Salary')
                                                            <span class="badge badge-salary">{{ $transaction['category_bank'] }}</span>
                                                        @elseif($transaction['category_bank'] == 'Expense')
                                                            <span class="badge badge-expense">{{ $transaction['category_bank'] }}</span>
                                                        @elseif($transaction['category_bank'] == 'Revenue')
                                                            <span class="badge badge-revenue">{{ $transaction['category_bank'] }}</span>
                                                        @else
                                                            <span class="badge">{{ $transaction['category_bank'] }}</span>
                                                        @endif
                                                    @else
                                                        <span>-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($transaction['type']))
                                                        <span class="badge {{ $transaction['type'] == 'Credit' ? 'badge-credit' : 'badge-debit' }}">
                                                            {{ $transaction['type'] }}
                                                        </span>
                                                    @else
                                                        <span>-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="font-bold {{ isset($transaction['type']) && $transaction['type'] == 'Credit' ? 'text-green-700' : 'text-red-700' }}">
                                                        ₹{{ number_format($transaction['amount'] ?? 0, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-sm max-w-xs truncate">{{ $transaction['remark'] ?? '-' }}</td>
                                                <td>{{ isset($transaction['created_at']) ? \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y, h:i A') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Bank Data Found</h3>
                <p class="text-gray-600 text-sm">No bank data available to display in the report with the selected filters.</p>
            </div>
        @endif
    </div>

@endsection
