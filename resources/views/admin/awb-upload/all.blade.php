@extends('layouts.admin')

@section('title', 'All AWB Upload')

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
        .upload-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .upload-table {
            width: 100%;
        }
        .upload-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .upload-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .upload-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .upload-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .upload-table tbody tr.status-cancelled {
            background: linear-gradient(90deg, #fee2e2 0%, #fecaca 100%);
            border-left: 4px solid #dc2626;
            position: relative;
        }
        .upload-table tbody tr.status-cancelled::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: calc(100% - 4px);
            margin-left: 4px;
            height: 2px;
            background: linear-gradient(90deg, rgba(220, 38, 38, 0.4) 0%, rgba(220, 38, 38, 0.2) 100%);
            pointer-events: none;
            z-index: 1;
        }
        .upload-table tbody tr.status-cancelled:hover {
            background: linear-gradient(90deg, #fecaca 0%, #fca5a5 100%);
            border-left-color: #b91c1c;
        }
        .upload-table tbody tr.status-cancelled td {
            color: #7f1d1d;
            position: relative;
            z-index: 2;
        }
        .upload-table tbody tr.status-cancelled td.font-semibold {
            color: #991b1b;
            font-weight: 700;
        }
        .upload-table tbody td {
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
        .status-badge.inactive, .status-badge.pending {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }
        .status-badge.completed {
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            color: #5b21b6;
        }
        .status-badge.cancelled {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #dc2626;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-badge.delivered {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }
        .status-badge.transit {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }
        .status-badge.publish {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">All AWB Upload</h1>
                    <p class="text-xs text-gray-600">Single and Bulk, Editable Required (no Duplicate/Special character allowed)</p>
                </div>
            </div>
            <a href="{{ route('admin.awb-upload.create') }}" class="admin-btn-primary px-5 py-2.5 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Create Upload</span>
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
        
        <form method="GET" action="{{ route('admin.awb-upload.all') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- AWB Number Search (Most Important) -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    AWB Number <span class="text-red-500">*</span>
                </label>
                <input type="text" name="awb_number" value="{{ $searchParams['awb_number'] ?? '' }}" class="search-input" placeholder="Enter AWB number">
            </div>
            
            <!-- Network Name Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                    Network Name
                </label>
                <select name="network_name" class="search-select">
                    <option value="">All Networks</option>
                    @foreach($networks as $network)
                        <option value="{{ $network['name'] }}" {{ ($searchParams['network_name'] ?? '') == $network['name'] ? 'selected' : '' }}>
                            {{ $network['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Service Name Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Service Name
                </label>
                <select name="service_name" class="search-select">
                    <option value="">All Services</option>
                    @foreach($services as $service)
                        <option value="{{ $service['name'] }}" {{ ($searchParams['service_name'] ?? '') == $service['name'] ? 'selected' : '' }}>
                            {{ $service['name'] }}
                        </option>
                    @endforeach
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
                @if($searchParams['awb_number'] || $searchParams['network_name'] || $searchParams['service_name'])
                    <a href="{{ route('admin.awb-upload.all') }}" class="px-4 py-2.5 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- AWB Upload Table -->
    <div class="upload-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                AWB Upload List
            </h2>
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-600 font-medium">
                    Total: <span class="font-bold text-orange-600">{{ count($awbUploads) }}</span> Uploads
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

        @if(session('error'))
            <div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 rounded-xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <p class="text-red-700 font-bold text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <form id="bulkDeleteForm" method="POST" action="{{ route('admin.awb-upload.bulk-delete') }}">
            @csrf
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="upload-table min-w-full">
                    <thead>
                        <tr>
                            <th class="w-12">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            </th>
                            <th>ID</th>
                            <th>AWB No.</th>
                            <th>Date of Sale</th>
                            <th>Branch</th>
                            <th>Network</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Destination</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($awbUploads as $upload)
                            @php
                                $uploadId = is_array($upload) ? $upload['id'] : $upload->id;
                                $uploadAwbNo = is_array($upload) ? $upload['awb_no'] : $upload->awb_no;
                                $uploadDateOfSale = is_array($upload) ? ($upload['date_of_sale'] ?? '') : ($upload->date_of_sale ? $upload->date_of_sale->format('Y-m-d') : '');
                                $uploadBranch = is_array($upload) ? ($upload['branch'] ?? '') : ($upload->branch ?? '');
                                $uploadNetwork = is_array($upload) ? ($upload['network'] ?? '') : ($upload->network_name ?? '');
                                $uploadService = is_array($upload) ? ($upload['service'] ?? '') : ($upload->service_name ?? '');
                                $uploadStatus = is_array($upload) ? ($upload['status'] ?? '') : ($upload->status ?? '');
                                $uploadDestination = is_array($upload) ? ($upload['destination'] ?? '') : ($upload->destination ?? '');
                                $isCancelled = strtolower($uploadStatus) === 'cancelled';
                            @endphp
                            <tr class="{{ $isCancelled ? 'status-cancelled' : '' }}">
                                <td>
                                    <input type="checkbox" name="selected_ids[]" value="{{ $uploadId }}" class="row-checkbox w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500" onchange="updateBulkDeleteBtn()">
                                </td>
                                <td class="font-bold text-gray-900">#{{ $uploadId }}</td>
                                <td class="font-semibold text-orange-600">{{ $uploadAwbNo }}</td>
                                <td class="text-xs">{{ $uploadDateOfSale ?: '-' }}</td>
                                <td><span class="badge">{{ $uploadBranch ?: '-' }}</span></td>
                                <td><span class="badge">{{ $uploadNetwork ?: '-' }}</span></td>
                                <td><span class="badge">{{ $uploadService ?: '-' }}</span></td>
                                <td>
                                    <span class="status-badge {{ strtolower($uploadStatus) }}">
                                        @if(strtolower($uploadStatus) == 'active' || strtolower($uploadStatus) == 'publish')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif(strtolower($uploadStatus) == 'cancelled')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif(strtolower($uploadStatus) == 'delivered')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif(strtolower($uploadStatus) == 'transit')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        {{ ucfirst($uploadStatus) }}
                                    </span>
                                </td>
                                <td><span class="badge">{{ $uploadDestination ?: '-' }}</span></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.awb-upload.edit', $uploadId) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button onclick="deleteUpload({{ $uploadId }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-0">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-2">No AWB Upload Found</h3>
                                        <p class="text-gray-600 mb-4 max-w-md mx-auto">Get started by creating your first AWB upload. Click the button above to add a new upload.</p>
                                        <a href="{{ route('admin.awb-upload.create') }}" class="admin-btn-primary inline-block">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                <span>Create First Upload</span>
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
                alert('Please select at least one AWB upload to delete.');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${checkboxes.length} selected AWB upload(s)?`)) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }

        // Delete single AWB upload
        function deleteUpload(id) {
            if (confirm('Are you sure you want to delete this AWB upload?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                const deleteUrl = '{{ route("admin.awb-upload.delete", ":id") }}'.replace(':id', id);
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
