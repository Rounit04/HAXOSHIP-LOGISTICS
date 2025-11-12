@extends('layouts.admin')

@section('title', 'Payroll List')

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
        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .payroll-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payroll-table thead {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }
        .payroll-table th {
            padding: 14px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }
        .payroll-table td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            color: #374151;
        }
        .payroll-table tbody tr:hover {
            background: #f9fafb;
        }
        .payroll-table tbody tr:last-child td {
            border-bottom: none;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .badge-manual {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-calendar {
            background: #fef3c7;
            color: #92400e;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state svg {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            opacity: 0.3;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Payroll List</h1>
                    <p class="text-xs text-gray-600">View all generated payroll records</p>
                </div>
            </div>
            <a href="{{ route('admin.payroll.salary-generate.index') }}" class="px-5 py-2.5 rounded-xl text-white font-semibold transition text-sm flex items-center gap-2" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.3);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Generate Salary
            </a>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="table-card">
        @if(count($payrolls) > 0)
            <div class="overflow-x-auto">
                <table class="payroll-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>User Type</th>
                            <th>Salary Amount</th>
                            <th>Period Start</th>
                            <th>Period End</th>
                            <th>Generation Type</th>
                            <th>Remarks</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payrolls as $payroll)
                            <tr>
                                <td class="font-semibold text-gray-900">#{{ $payroll['id'] }}</td>
                                <td>
                                    @if(isset($payroll['user']) && $payroll['user'])
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm" style="background: var(--admin-gradient);">
                                                {{ strtoupper(substr($payroll['user']->name ?? 'N/A', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $payroll['user']->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">{{ $payroll['user']->email ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">User not found</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-manual">{{ ucfirst($payroll['user_type'] ?? 'N/A') }}</span>
                                </td>
                                <td class="font-semibold text-gray-900">
                                    ₹{{ number_format($payroll['salary_amount'] ?? 0, 2) }}
                                </td>
                                <td class="text-gray-700">
                                    {{ isset($payroll['period_start']) ? \Carbon\Carbon::parse($payroll['period_start'])->format('d M Y') : 'N/A' }}
                                </td>
                                <td class="text-gray-700">
                                    {{ isset($payroll['period_end']) ? \Carbon\Carbon::parse($payroll['period_end'])->format('d M Y') : 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $payroll['generation_type'] ?? 'manual' }}">
                                        {{ ucfirst($payroll['generation_type'] ?? 'Manual') }}
                                    </span>
                                </td>
                                <td class="text-gray-600 text-sm max-w-xs truncate" title="{{ $payroll['remarks'] ?? 'N/A' }}">
                                    {{ $payroll['remarks'] ?? 'N/A' }}
                                </td>
                                <td class="text-gray-500 text-sm">
                                    {{ isset($payroll['created_at']) ? \Carbon\Carbon::parse($payroll['created_at'])->format('d M Y, h:i A') : 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <svg class="text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Payroll Records Found</h3>
                <p class="text-gray-600 mb-6">Start by generating salary for employees</p>
                <a href="{{ route('admin.payroll.salary-generate.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold transition text-sm" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.3);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Generate Salary
                </a>
            </div>
        @endif
    </div>
@endsection
