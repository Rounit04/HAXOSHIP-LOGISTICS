@extends('layouts.admin')

@section('title', 'Network Report')

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
        .filter-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            padding: 20px;
            margin-bottom: 20px;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .filter-input, .filter-select {
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
        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
        .report-actions {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            position: relative;
            cursor: pointer;
        }
        .report-table thead th:hover {
            background: rgba(255, 117, 15, 0.1);
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
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge.active {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }
        .status-badge.inactive {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }
        .financial-amount {
            font-weight: 600;
            font-size: 13px;
        }
        .financial-amount.positive {
            color: #059669;
        }
        .financial-amount.negative {
            color: #dc2626;
        }
        .search-input {
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            width: 300px;
            transition: all 0.3s ease;
        }
        .search-input:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Network Report</h1>
                    <p class="text-xs text-gray-600">Different Report according to user</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.reports.network') }}" id="filterForm">
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">Select Category</label>
                    <select name="category" class="filter-select" onchange="updateBranches()">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $key => $label)
                            <option value="{{ $key }}" {{ ($category ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Select Hub</label>
                    <select name="hub" id="hubSelect" class="filter-select" onchange="updateBranches()">
                        <option value="">All Hubs</option>
                        @foreach($hubs ?? [] as $hubItem)
                            <option value="{{ $hubItem }}" {{ ($hub ?? '') == $hubItem ? 'selected' : '' }}>{{ $hubItem }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Select Branch</label>
                    <select name="branch" id="branchSelect" class="filter-select">
                        <option value="">All Branches</option>
                        @foreach($branches ?? [] as $branchItem)
                            <option value="{{ $branchItem }}" {{ ($branch ?? '') == $branchItem ? 'selected' : '' }}>{{ $branchItem }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">From Date</label>
                    <input type="date" name="date_from" class="filter-input" value="{{ $dateFrom ?? '' }}" placeholder="dd-mm-yyyy">
                </div>
                <div class="filter-group">
                    <label class="filter-label">To Date</label>
                    <input type="date" name="date_to" class="filter-input" value="{{ $dateTo ?? '' }}" placeholder="dd-mm-yyyy">
                </div>
                <div class="filter-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="admin-btn-primary px-6 py-2.5 text-sm font-semibold w-full">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <span>Show</span>
                        </div>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Report Actions -->
    <div class="report-actions">
        <h2 class="text-lg font-bold text-gray-900">View Reports</h2>
        <div class="flex items-center gap-4">
            <form method="GET" action="{{ route('admin.reports.network.export') }}" id="exportForm" style="display: inline;">
                <input type="hidden" name="category" value="{{ $category ?? '' }}">
                <input type="hidden" name="hub" value="{{ $hub ?? '' }}">
                <input type="hidden" name="branch" value="{{ $branch ?? '' }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom ?? '' }}">
                <input type="hidden" name="date_to" value="{{ $dateTo ?? '' }}">
                <input type="hidden" name="search" value="{{ $search ?? '' }}">
                <button type="submit" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export to Excel
                </button>
            </form>
            <form method="GET" action="{{ route('admin.reports.network') }}" class="flex items-center gap-2">
                <input type="hidden" name="category" value="{{ $category ?? '' }}">
                <input type="hidden" name="hub" value="{{ $hub ?? '' }}">
                <input type="hidden" name="branch" value="{{ $branch ?? '' }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom ?? '' }}">
                <input type="hidden" name="date_to" value="{{ $dateTo ?? '' }}">
                <label class="text-sm font-semibold text-gray-700">Search:</label>
                <input type="text" name="search" class="search-input" value="{{ $search ?? '' }}" placeholder="Search network name, remark, admin...">
                <button type="submit" class="admin-btn-primary px-4 py-2.5 text-sm font-semibold">
                    Search
                </button>
            </form>
        </div>
    </div>

    <!-- Report Table -->
    <div class="report-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Network Report Data
            </h2>
            <div class="text-sm text-gray-600 font-medium">
                Total: <span class="font-bold text-orange-600">{{ count($networks) }}</span> Networks
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            @if(count($networks) > 0)
                <table class="report-table min-w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Network Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Opening Balance</th>
                            <th>Spending</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Total Balance</th>
                            <th>Remark</th>
                            <th>Admin Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($networks as $network)
                            @php
                                $financial = $network['financial'] ?? [
                                    'spending' => 0,
                                    'credit' => 0,
                                    'debit' => 0,
                                    'total_balance' => 0,
                                    'opening_balance' => $network['opening_balance'] ?? 0,
                                ];
                            @endphp
                            <tr>
                                <td class="font-bold text-gray-900">#{{ $network['id'] ?? '-' }}</td>
                                <td class="font-semibold text-gray-700">{{ $network['name'] }}</td>
                                <td><span class="badge">{{ $network['type'] }}</span></td>
                                <td>
                                    <span class="status-badge {{ strtolower($network['status'] ?? 'active') == 'active' ? 'active' : 'inactive' }}">
                                        {{ $network['status'] ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="financial-amount">
                                    ₹{{ number_format($financial['opening_balance'], 2) }}
                                </td>
                                <td class="financial-amount negative">
                                    ₹{{ number_format($financial['spending'], 2) }}
                                </td>
                                <td class="financial-amount positive">
                                    ₹{{ number_format($financial['credit'], 2) }}
                                </td>
                                <td class="financial-amount negative">
                                    ₹{{ number_format($financial['debit'], 2) }}
                                </td>
                                <td class="financial-amount {{ $financial['total_balance'] >= 0 ? 'positive' : 'negative' }}">
                                    ₹{{ number_format($financial['total_balance'], 2) }}
                                </td>
                                <td class="text-sm text-gray-600">{{ $network['remark'] ?? '-' }}</td>
                                <td class="font-medium">{{ $network['admin_name'] ?? 'System' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Financial Summary -->
                @if(!empty($networkFinancials))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Financial Summary by Network</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($networkFinancials as $networkName => $financial)
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                                    <h4 class="font-bold text-gray-900 mb-3 text-sm">{{ $networkName }}</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Opening Balance:</span>
                                            <span class="font-semibold">₹{{ number_format($financial['opening_balance'], 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Spending:</span>
                                            <span class="font-semibold text-red-600">₹{{ number_format($financial['spending'], 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Credit:</span>
                                            <span class="font-semibold text-green-600">₹{{ number_format($financial['credit'], 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Debit:</span>
                                            <span class="font-semibold text-red-600">₹{{ number_format($financial['debit'], 2) }}</span>
                                        </div>
                                        <div class="flex justify-between pt-2 border-t border-gray-300">
                                            <span class="font-bold text-gray-900">Total Balance:</span>
                                            <span class="font-bold {{ $financial['total_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                ₹{{ number_format($financial['total_balance'], 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Network Data Found</h3>
                    <p class="text-gray-600 text-sm">No network data available to display in the report.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Store all branches for filtering
        const allBranches = @json($branches ?? []);
        
        function updateBranches() {
            const hubSelect = document.getElementById('hubSelect');
            const branchSelect = document.getElementById('branchSelect');
            const selectedHub = hubSelect.value;
            
            // Clear branch options except "All Branches"
            branchSelect.innerHTML = '<option value="">All Branches</option>';
            
            // Filter branches based on selected hub
            // Note: In a real implementation, you might want to fetch branches via AJAX
            // For now, we'll show all branches and let the server filter them
            allBranches.forEach(branch => {
                const option = document.createElement('option');
                option.value = branch;
                option.textContent = branch;
                @if(isset($branch) && $branch)
                    if (branch === '{{ $branch }}') {
                        option.selected = true;
                    }
                @endif
                branchSelect.appendChild(option);
            });
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateBranches();
        });
    </script>
@endsection

