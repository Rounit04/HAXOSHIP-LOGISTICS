@extends('layouts.admin')

@section('title', 'All Shipping Charges')

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
        .charge-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .charge-table {
            width: 100%;
        }
        .charge-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .charge-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .charge-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .charge-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .charge-table tbody td {
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
        .rate-badge {
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
        .search-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            padding: 20px;
            margin-bottom: 20px;
        }
        .search-input, .search-select {
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
        .search-input:focus, .search-select:focus {
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">All Shipping Charges</h1>
                    <p class="text-xs text-gray-600">Manage all shipping charges - Single, bulk, and Update (Excel or Single update)</p>
                </div>
            </div>
            <a href="{{ route('admin.shipping-charges.create') }}" class="admin-btn-primary px-5 py-2.5 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Create Charge</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Search & Filter
        </h2>
        
        <form method="GET" action="{{ route('admin.shipping-charges.all') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Origin/Destination Search -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Origin/Destination
                </label>
                <input type="text" name="search" value="{{ $searchParams['search'] ?? '' }}" class="search-input" placeholder="Search by origin or destination">
            </div>
            
            <!-- Network Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                    Network
                </label>
                <select name="network" class="search-select">
                    <option value="">All Networks</option>
                    @foreach($networks ?? [] as $network)
                        <option value="{{ $network['name'] }}" {{ ($searchParams['network'] ?? '') == $network['name'] ? 'selected' : '' }}>{{ $network['name'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Shipment Type Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Shipment Type
                </label>
                <select name="shipment_type" class="search-select">
                    <option value="">All Types</option>
                    <option value="Dox" {{ ($searchParams['shipment_type'] ?? '') == 'Dox' ? 'selected' : '' }}>Dox</option>
                    <option value="Non-Dox" {{ ($searchParams['shipment_type'] ?? '') == 'Non-Dox' ? 'selected' : '' }}>Non-Dox</option>
                    <option value="Medicine" {{ ($searchParams['shipment_type'] ?? '') == 'Medicine' ? 'selected' : '' }}>Medicine</option>
                </select>
            </div>
            
            <!-- Search Button -->
            <div class="flex items-end gap-2">
                <button type="submit" class="admin-btn-primary px-6 py-2.5 text-sm font-semibold flex-1">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search</span>
                    </div>
                </button>
                @if($searchParams['search'] ?? '' || $searchParams['network'] ?? '' || $searchParams['shipment_type'] ?? '')
                    <a href="{{ route('admin.shipping-charges.all') }}" class="px-4 py-2.5 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Shipping Charges Table -->
    <div class="charge-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Shipping Charges List
            </h2>
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-600 font-medium">
                    Total: <span class="font-bold text-orange-600">{{ count($shippingCharges) }}</span> Charges
                </div>
                <button id="bulkDeleteBtn" onclick="bulkDelete()" class="admin-btn-primary px-4 py-2 text-sm font-semibold hidden">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Delete Selected</span>
                    </div>
                </button>
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

        <form id="bulkDeleteForm" method="POST" action="{{ route('admin.shipping-charges.bulk-delete') }}">
            @csrf
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="charge-table min-w-full">
                    <thead>
                        <tr>
                            <th class="w-12">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            </th>
                            <th>ID</th>
                            <th>Origin</th>
                            <th>Origin Zone</th>
                            <th>Destination</th>
                            <th>Dest. Zone</th>
                            <th>Type</th>
                            <th>Weight Range</th>
                            <th>Network</th>
                            <th>Service</th>
                            <th>Rate</th>
                            <th>Remark</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shippingCharges as $charge)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_ids[]" value="{{ $charge['id'] }}" class="row-checkbox w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500" onchange="updateBulkDeleteBtn()">
                                </td>
                                <td class="font-bold text-gray-900">#{{ $charge['id'] }}</td>
                            <td><span class="badge">{{ $charge['origin'] }}</span></td>
                            <td><span class="badge">{{ $charge['origin_zone'] }}</span></td>
                            <td><span class="badge">{{ $charge['destination'] }}</span></td>
                            <td><span class="badge">{{ $charge['destination_zone'] }}</span></td>
                            <td><span class="badge">{{ $charge['shipment_type'] }}</span></td>
                            <td class="text-xs">
                                <span class="font-semibold">{{ number_format($charge['min_weight'], 2) }}</span> - 
                                <span class="font-semibold">{{ number_format($charge['max_weight'], 2) }}</span> KG
                            </td>
                            <td><span class="badge">{{ $charge['network'] }}</span></td>
                            <td><span class="badge">{{ $charge['service'] }}</span></td>
                            <td>
                                <span class="rate-badge">₹{{ number_format($charge['rate'], 2) }}</span>
                            </td>
                            <td class="text-xs text-gray-500 max-w-xs truncate">{{ $charge['remark'] ?: '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.shipping-charges.edit', $charge['id']) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button onclick="deleteCharge({{ $charge['id'] }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">No Shipping Charges Found</h3>
                                    <p class="text-gray-600 mb-4 max-w-md mx-auto">Get started by creating your first shipping charge. Click the button above to add a new charge.</p>
                                    <a href="{{ route('admin.shipping-charges.create') }}" class="admin-btn-primary inline-block">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            <span>Create First Charge</span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </form>
    </div>

    <script>
        // Toggle select all checkboxes
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateBulkDeleteBtn();
        }

        // Update bulk delete button visibility
        function updateBulkDeleteBtn() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            if (checkboxes.length > 0) {
                bulkDeleteBtn.classList.remove('hidden');
                bulkDeleteBtn.querySelector('span').textContent = `Delete Selected (${checkboxes.length})`;
            } else {
                bulkDeleteBtn.classList.add('hidden');
            }
        }

        // Bulk delete function
        function bulkDelete() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one shipping charge to delete.');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${checkboxes.length} selected shipping charge(s)?`)) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }

        // Delete shipping charge
        function deleteCharge(id) {
            if (confirm('Are you sure you want to delete this shipping charge?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                const deleteUrl = '{{ route("admin.shipping-charges.delete", ":id") }}'.replace(':id', id);
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



