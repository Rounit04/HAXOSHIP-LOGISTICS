@extends('layouts.admin')

@section('title', 'Payroll List')

@section('content')
    <style>
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            border: 1px solid rgba(255, 117, 15, 0.1);
        }
        .search-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
            margin-bottom: 24px;
            padding: 24px;
        }
        .payroll-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .table {
            width: 100%;
        }
        .table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .table tbody td {
            padding: 14px 16px;
            font-size: 14px;
            color: #374151;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-approved {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
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
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Payroll List</h1>
                    <p class="text-xs text-gray-600">View and manage all payroll records</p>
                </div>
            </div>
            <a href="{{ route('admin.payroll.salary-generate.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Generate Salary
            </a>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <form method="GET" action="{{ route('admin.payroll.list') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ $searchParams['search'] ?? '' }}" class="form-input" placeholder="Name or Email">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">User Type</label>
                <select name="user_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="merchant" {{ ($searchParams['user_type'] ?? '') == 'merchant' ? 'selected' : '' }}>Merchant</option>
                    <option value="deliveryman" {{ ($searchParams['user_type'] ?? '') == 'deliveryman' ? 'selected' : '' }}>Deliveryman</option>
                    <option value="user" {{ ($searchParams['user_type'] ?? '') == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ ($searchParams['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ ($searchParams['status'] ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid" {{ ($searchParams['status'] ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ ($searchParams['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Period Start</label>
                <input type="date" name="period_start" value="{{ $searchParams['period_start'] ?? '' }}" class="form-input">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Period End</label>
                <input type="date" name="period_end" value="{{ $searchParams['period_end'] ?? '' }}" class="form-input">
            </div>
            <div class="md:col-span-5 flex gap-3">
                <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search</span>
                    </div>
                </button>
                <a href="{{ route('admin.payroll.list') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
                    Reset
                </a>
                <a href="{{ route('admin.payroll.export', $searchParams) }}" class="px-6 py-3 rounded-xl border-2 border-green-300 text-green-700 font-semibold hover:bg-green-50 transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Payroll Table -->
    <div class="payroll-table">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>User Email</th>
                        <th>User Type</th>
                        <th>Salary Amount</th>
                        <th>Period</th>
                        <th>Generation Type</th>
                        <th>Status</th>
                        <th>Generated At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                        <tr>
                            <td>{{ $payroll->id }}</td>
                            <td>{{ $payroll->user->name ?? 'N/A' }}</td>
                            <td>{{ $payroll->user->email ?? 'N/A' }}</td>
                            <td>
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700">
                                    {{ ucfirst($payroll->user_type) }}
                                </span>
                            </td>
                            <td class="font-bold">{{ currency($payroll->salary_amount) }}</td>
                            <td>
                                <div class="text-xs">
                                    <div>{{ $payroll->period_start->format('M d, Y') }}</div>
                                    <div class="text-gray-500">to</div>
                                    <div>{{ $payroll->period_end->format('M d, Y') }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-purple-100 text-purple-700">
                                    {{ ucfirst($payroll->generation_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $payroll->status }}">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td>
                                @if($payroll->generated_at)
                                    <div class="text-xs">
                                        <div>{{ $payroll->generated_at->format('M d, Y') }}</div>
                                        <div class="text-gray-500">{{ $payroll->generated_at->format('h:i A') }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-gray-500 font-semibold">No payroll records found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($payrolls->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>
@endsection



