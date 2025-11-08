@extends('layouts.admin')

@section('title', 'All AWB')

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
        .debit-credit-section {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }
        .debit-credit-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .debit-card, .credit-card {
            padding: 16px 20px;
            border-radius: 10px;
            border: 2px solid;
        }
        .debit-card {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-color: #ef4444;
        }
        .credit-card {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-color: #10b981;
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">All AWB</h1>
                    <p class="text-xs text-gray-600">Search with AWB - Info</p>
                </div>
            </div>
            <a href="{{ route('admin.search-with-awb.create') }}" class="admin-btn-primary px-5 py-2.5 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Create AWB</span>
                </div>
            </a>
        </div>
    </div>

    <!-- AWB Table -->
    <div class="awb-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                AWB List
            </h2>
            <div class="text-sm text-gray-600 font-medium">
                Total: <span class="font-bold text-orange-600">{{ count($awbs) }}</span> AWBs
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
            <table class="awb-table min-w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Origin</th>
                        <th>Origin Pin</th>
                        <th>Destination</th>
                        <th>Dest. Pin</th>
                        <th>Weight</th>
                        <th>Pieces</th>
                        <th>Network</th>
                        <th>Service</th>
                        <th>Booking Amount</th>
                        <th>V.AWB</th>
                        <th>F.AWB</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($awbs as $awb)
                        <tr>
                            <td class="font-bold text-gray-900">#{{ $awb['id'] }}</td>
                            <td><span class="badge">{{ $awb['origin'] }}</span></td>
                            <td><span class="badge">{{ $awb['origin_pin'] }}</span></td>
                            <td><span class="badge">{{ $awb['destination'] }}</span></td>
                            <td><span class="badge">{{ $awb['destination_pin'] }}</span></td>
                            <td class="text-xs font-semibold">{{ number_format($awb['chr_weight'], 2) }} KG</td>
                            <td class="text-xs font-semibold">{{ $awb['pieces'] }}</td>
                            <td><span class="badge">{{ $awb['network'] }}</span></td>
                            <td><span class="badge">{{ $awb['services'] }}</span></td>
                            <td>
                                <span class="amount-badge">₹{{ number_format($awb['booking_amount'], 2) }}</span>
                            </td>
                            <td class="text-xs font-semibold text-orange-600">{{ $awb['v_awb'] }}</td>
                            <td class="text-xs font-semibold text-blue-600">{{ $awb['f_awb'] }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.search-with-awb.edit', $awb['id']) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button onclick="deleteAwb({{ $awb['id'] }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="p-0">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">No AWB Found</h3>
                                    <p class="text-gray-600 mb-4 max-w-md mx-auto">Get started by creating your first AWB. Click the button above to add a new AWB.</p>
                                    <a href="{{ route('admin.search-with-awb.create') }}" class="admin-btn-primary inline-block">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            <span>Create First AWB</span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- All Debits & Credit Entry Shows -->
        @if(count($awbs) > 0)
            <div class="debit-credit-section">
                <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    All Debits & Credit Entry
                </h3>
                <div class="debit-credit-grid">
                    <div class="debit-card">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-red-700 text-sm flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                </svg>
                                Total Debits
                            </h4>
                        </div>
                        <p class="text-xl font-bold text-red-800">
                            ₹{{ number_format(collect($awbs)->sum('booking_amount'), 2) }}
                        </p>
                        <p class="text-xs text-red-600 mt-1">From {{ count($awbs) }} AWB(s)</p>
                    </div>
                    <div class="credit-card">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-green-700 text-sm flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Total Credits
                            </h4>
                        </div>
                        <p class="text-xl font-bold text-green-800">
                            ₹{{ number_format(collect($awbs)->sum('booking_amount'), 2) }}
                        </p>
                        <p class="text-xs text-green-600 mt-1">From {{ count($awbs) }} AWB(s)</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Delete AWB
        function deleteAwb(id) {
            if (confirm('Are you sure you want to delete this AWB?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                const deleteUrl = '{{ route("admin.search-with-awb.delete", ":id") }}'.replace(':id', id);
                form.action = deleteUrl;
                
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



