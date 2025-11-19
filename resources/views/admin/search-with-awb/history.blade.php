@extends('layouts.admin')

@section('title', 'AWB History')

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
        .awb-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .awb-table {
            width: 100%;
        }
        .awb-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .awb-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .awb-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .awb-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .awb-table tbody td {
            padding: 14px 16px;
            font-size: 13px;
            color: #374151;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(255, 117, 15, 0.1);
            color: #FF750F;
        }
        .amount-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
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
        .timestamp {
            font-size: 12px;
            color: #6b7280;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">AWB History</h1>
                    <p class="text-xs text-gray-600">Search with AWB - Info</p>
                </div>
            </div>
            <a href="{{ route('admin.search-with-awb.search') }}" class="admin-btn-primary px-5 py-2.5 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Search AWB</span>
                </div>
            </a>
        </div>
    </div>

    <!-- History Table -->
    <div class="awb-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Viewed AWB History
            </h2>
            <div class="text-sm text-gray-600 font-medium">
                Total: <span class="font-bold text-orange-600">{{ count($history) }}</span> Viewed
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="awb-table min-w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>AWB No.</th>
                        <th>Branch</th>
                        <th>Destination</th>
                        <th>Weight</th>
                        <th>Pieces</th>
                        <th>Network</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Viewed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $awb)
                        <tr>
                            <td class="font-bold text-gray-900">#{{ $awb['id'] }}</td>
                            <td class="text-xs font-semibold text-orange-600">{{ $awb['awb_no'] }}</td>
                            <td><span class="badge">{{ $awb['branch'] ?? '-' }}</span></td>
                            <td><span class="badge">{{ $awb['destination'] ?? '-' }}</span></td>
                            <td class="text-xs font-semibold">{{ number_format($awb['chr_weight'] ?? $awb['weight'] ?? 0, 2) }} KG</td>
                            <td class="text-xs font-semibold">{{ $awb['pieces'] ?? '-' }}</td>
                            <td><span class="badge">{{ $awb['network'] ?? '-' }}</span></td>
                            <td><span class="badge">{{ $awb['service'] ?? '-' }}</span></td>
                            <td>
                                <span class="badge" style="background: {{ isset($awb['status']) && $awb['status'] == 'Active' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ isset($awb['status']) && $awb['status'] == 'Active' ? '#059669' : '#dc2626' }};">
                                    {{ $awb['status'] ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="timestamp">
                                    {{ isset($awb['viewed_at']) ? \Carbon\Carbon::parse($awb['viewed_at'])->format('M d, Y H:i') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.search-with-awb.search') }}?awb_number={{ $awb['awb_no'] }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <button onclick="deleteHistoryEntry({{ $awb['id'] }}, '{{ $awb['awb_no'] }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete from History">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-0">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">No History Found</h3>
                                    <p class="text-gray-600 mb-4 max-w-md mx-auto">You haven't viewed any AWB details yet. Search for an AWB to start building your history.</p>
                                    <a href="{{ route('admin.search-with-awb.search') }}" class="admin-btn-primary inline-block">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                            <span>Search AWB</span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function deleteHistoryEntry(id, awbNo) {
            if (confirm('Are you sure you want to remove this AWB (#' + id + ' - ' + awbNo + ') from history?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.search-with-awb.history.delete", ":id") }}'.replace(':id', id);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
