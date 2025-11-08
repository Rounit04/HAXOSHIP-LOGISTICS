@extends('layouts.admin')

@section('title', 'Formula Report')

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
        .value-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            color: #5b21b6;
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Formula Report</h1>
                    <p class="text-xs text-gray-600">Different Report according to user</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.formula.export') }}" class="admin-btn-primary px-5 py-2.5 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export to Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="report-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Formula Report Data
            </h2>
            <div class="text-sm text-gray-600 font-medium">
                Total: <span class="font-bold text-orange-600">{{ count($formulas) }}</span> Formulas
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            @if(count($formulas) > 0)
                <table class="report-table min-w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Formula Name</th>
                            <th>Network</th>
                            <th>Service</th>
                            <th>Type</th>
                            <th>Scope</th>
                            <th>Priority</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formulas as $formula)
                            <tr>
                                <td class="font-bold text-gray-900">#{{ $formula['id'] }}</td>
                                <td class="font-semibold text-gray-700">{{ $formula['formula_name'] }}</td>
                                <td>{{ $formula['network'] }}</td>
                                <td>{{ $formula['service'] }}</td>
                                <td><span class="badge">{{ $formula['type'] }}</span></td>
                                <td><span class="badge">{{ $formula['scope'] }}</span></td>
                                <td><span class="priority-badge">{{ $formula['priority'] }}</span></td>
                                <td><span class="value-badge">₹{{ number_format($formula['value'], 2) }}</span></td>
                                <td>
                                    <span class="status-badge {{ strtolower($formula['status']) == 'active' ? 'active' : 'inactive' }}">
                                        {{ $formula['status'] }}
                                    </span>
                                </td>
                                <td class="text-sm text-gray-600">{{ $formula['remark'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Formula Data Found</h3>
                    <p class="text-gray-600 text-sm">No formula data available to display in the report.</p>
                </div>
            @endif
        </div>
    </div>

@endsection

