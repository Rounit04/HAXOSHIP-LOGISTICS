@extends('layouts.admin')

@section('title', 'All Formulas')

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
        .formula-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .formula-table {
            width: 100%;
        }
        .formula-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .formula-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .formula-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .formula-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .formula-table tbody td {
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
        .status-badge.inactive {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }
        /* Status Toggle Switch */
        .status-toggle-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }
        .status-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .status-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.4s;
            border-radius: 26px;
        }
        .status-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        input:checked + .status-slider {
            background-color: #10b981;
        }
        input:checked + .status-slider:before {
            transform: translateX(24px);
        }
        .status-switch:hover .status-slider {
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }
        input:checked + .status-slider:hover {
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        }
        .priority-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">All Formulas</h1>
                    <p class="text-xs text-gray-600">Manage all formulas - Single, bulk, editable Status (Active/Inactive)</p>
                </div>
            </div>
            <a href="{{ route('admin.formulas.create') }}" class="admin-btn-primary px-5 py-2.5 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Create Formula</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Formulas Table -->
    <div class="formula-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Formulas List
            </h2>
            <div class="text-sm text-gray-600 font-medium">
                Total: <span class="font-bold text-orange-600">{{ count($formulas) }}</span> Formulas
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

        <!-- Search & Import Section -->
        <div class="formula-card p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Search & Filter
            </h2>
            
            <form method="GET" action="{{ route('admin.formulas.all') }}" id="searchForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search
                    </label>
                    <input type="text" name="search" value="{{ $searchParams['search'] ?? '' }}" class="form-input" placeholder="Search by name, network, service">
                </div>
                
                <!-- Network Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                        </svg>
                        Network
                    </label>
                    <select name="network" class="form-select">
                        <option value="">All Networks</option>
                        @foreach($networks ?? [] as $network)
                            <option value="{{ $network['name'] }}" {{ ($searchParams['network'] ?? '') == $network['name'] ? 'selected' : '' }}>{{ $network['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Type Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Type
                    </label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="Fixed" {{ ($searchParams['type'] ?? '') == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="Percentage" {{ ($searchParams['type'] ?? '') == 'Percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                </div>
                
                <!-- Status Filter & Actions -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="Active" {{ ($searchParams['status'] ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ ($searchParams['status'] ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </form>

            <div class="flex items-center gap-3">
                <button type="submit" form="searchForm" class="admin-btn-primary px-6 py-2.5 text-sm font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search</span>
                    </div>
                </button>
                @if($searchParams['search'] ?? '' || $searchParams['network'] ?? '' || $searchParams['type'] ?? '' || $searchParams['status'] ?? '')
                    <a href="{{ route('admin.formulas.all') }}" class="px-4 py-2.5 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                        Clear
                    </a>
                @endif
                
                <!-- Bulk Actions -->
                <div class="ml-auto flex items-center gap-3">
                    <button onclick="bulkDelete()" id="bulkDeleteBtn" class="px-4 py-2.5 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition text-sm hidden flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Delete Selected (<span id="selectedCount">0</span>)</span>
                    </button>
                    
                    <!-- Import Section -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.formulas.template.download') }}" class="px-4 py-2.5 rounded-lg border-2 border-blue-600 text-blue-600 font-semibold hover:bg-blue-50 transition text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Download Template</span>
                            </div>
                        </a>
                        <form action="{{ route('admin.formulas.import') }}" method="POST" enctype="multipart/form-data" class="inline">
                            @csrf
                            <label class="px-4 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition text-sm cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Import Excel</span>
                                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" onchange="this.form.submit()">
                                </div>
                            </label>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <form id="bulkDeleteForm" method="POST" action="{{ route('admin.formulas.bulk-delete') }}" style="display: none;">
            @csrf
        </form>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="formula-table min-w-full">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        </th>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($formulas as $formula)
                        <tr>
                            <td>
                                <input type="checkbox" name="selected_ids[]" value="{{ $formula['id'] }}" class="form-checkbox w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500" onchange="updateBulkDeleteButton()">
                            </td>
                            <td class="font-bold text-gray-900">#{{ $formula['id'] }}</td>
                            <td class="font-semibold text-gray-700">{{ $formula['formula_name'] }}</td>
                            <td><span class="badge">{{ $formula['network'] }}</span></td>
                            <td><span class="badge">{{ $formula['service'] }}</span></td>
                            <td><span class="badge">{{ $formula['type'] }}</span></td>
                            <td><span class="badge">{{ $formula['scope'] }}</span></td>
                            <td><span class="priority-badge">{{ $formula['priority'] }}</span></td>
                            <td>
                                <span class="value-badge">{{ number_format($formula['value'], 2) }}</span>
                            </td>
                            <td>
                                <div class="status-toggle-container">
                                    <label class="status-switch" title="Toggle Status">
                                        <input type="checkbox" 
                                               class="status-toggle-checkbox" 
                                               data-id="{{ $formula['id'] }}"
                                               data-route="{{ route('admin.formulas.toggle-status', $formula['id']) }}"
                                               {{ $formula['status'] == 'Active' ? 'checked' : '' }}
                                               onchange="toggleStatus(this, 'formula')">
                                        <span class="status-slider"></span>
                                    </label>
                                    <span class="status-label text-xs font-semibold {{ $formula['status'] == 'Active' ? 'text-green-700' : 'text-gray-500' }}">
                                        {{ $formula['status'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-xs text-gray-500 max-w-xs truncate">{{ $formula['remark'] ?: '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.formulas.edit', $formula['id']) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button onclick="deleteFormula({{ $formula['id'] }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="p-0">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">No Formulas Found</h3>
                                    <p class="text-gray-600 mb-4 max-w-md mx-auto">Get started by creating your first formula. Click the button above to add a new formula.</p>
                                    <a href="{{ route('admin.formulas.create') }}" class="admin-btn-primary inline-block">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            <span>Create First Formula</span>
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
        // Delete formula
        function deleteFormula(id) {
            if (confirm('Are you sure you want to delete this formula?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                const deleteUrl = '{{ route("admin.formulas.delete", ":id") }}'.replace(':id', id);
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

        // Toggle select all
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateBulkDeleteButton();
        }

        // Update bulk delete button visibility
        function updateBulkDeleteButton() {
            const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]:checked');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectedCount = document.getElementById('selectedCount');
            
            if (checkboxes.length > 0) {
                bulkDeleteBtn.classList.remove('hidden');
                if (selectedCount) {
                    selectedCount.textContent = checkboxes.length;
                }
            } else {
                bulkDeleteBtn.classList.add('hidden');
                if (selectedCount) {
                    selectedCount.textContent = '0';
                }
            }
        }

        // Bulk delete
        function bulkDelete() {
            const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one formula to delete.');
                return;
            }

            if (confirm(`Are you sure you want to delete ${checkboxes.length} formula(s)?`)) {
                const form = document.getElementById('bulkDeleteForm');
                const ids = Array.from(checkboxes).map(cb => cb.value);
                
                // Add selected IDs as hidden inputs
                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                form.submit();
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateBulkDeleteButton();
        });

        // Toggle status function
        function toggleStatus(checkbox, type) {
            const id = checkbox.dataset.id;
            const route = checkbox.dataset.route;
            const originalChecked = checkbox.checked;
            const statusLabel = checkbox.closest('.status-toggle-container').querySelector('.status-label');
            
            checkbox.disabled = true;
            
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch(route, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                checkbox.disabled = false;
                if (data.success) {
                    statusLabel.textContent = data.status;
                    statusLabel.className = data.status === 'Active' 
                        ? 'status-label text-xs font-semibold text-green-700' 
                        : 'status-label text-xs font-semibold text-gray-500';
                } else {
                    checkbox.checked = !originalChecked;
                    alert(data.message || 'Error updating status. Please try again.');
                }
            })
            .catch(error => {
                checkbox.disabled = false;
                checkbox.checked = !originalChecked;
                console.error('Error:', error);
                alert('Error updating status. Please try again.');
            });
        }
    </script>
@endsection



