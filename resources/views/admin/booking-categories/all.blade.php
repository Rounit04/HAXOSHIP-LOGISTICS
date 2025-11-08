@extends('layouts.admin')

@section('title', 'All Booking Categories')

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
        .category-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .category-table {
            width: 100%;
        }
        .category-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .category-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .category-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .category-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
            transform: translateX(2px);
        }
        .category-table tbody td {
            padding: 14px 16px;
            font-size: 14px;
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
        .type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(255, 117, 15, 0.1);
            color: #FF750F;
        }
        .awb-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .awb-badge.yes {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }
        .awb-badge.no {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #4b5563;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .btn-edit {
            padding: 6px 12px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-edit:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-delete {
            padding: 6px 12px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-delete:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
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
        .success-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            min-width: 300px;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        .success-popup.closing {
            animation: slideOut 0.3s ease-out;
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">All Booking Categories</h1>
                    <p class="text-xs text-gray-600">Category Single & Bulk - Manage all booking categories</p>
                </div>
            </div>
            <a href="{{ route('admin.booking-categories.create') }}" class="admin-btn-primary px-5 py-2.5 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Create Category</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="category-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Category List
            </h2>
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-600 font-medium">
                    Total: <span class="font-bold text-orange-600">{{ $categories->count() }}</span> Categories
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

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.booking-categories.all') }}" class="mb-4">
            <div class="flex items-center gap-3">
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, type, or status..." class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                    <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="admin-btn-primary px-6 py-2.5 text-sm font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search</span>
                    </div>
                </button>
                @if($search)
                    <a href="{{ route('admin.booking-categories.all') }}" class="px-4 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>


        @if($categories->count() > 0)
            <form id="bulkDeleteForm" method="POST" action="{{ route('admin.booking-categories.bulk-delete') }}">
                @csrf
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="category-table min-w-full">
                        <thead>
                            <tr>
                                <th class="w-12">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Requires AWB</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_ids[]" value="{{ $category->id }}" class="row-checkbox w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500" onchange="updateBulkDeleteBtn()">
                                    </td>
                                    <td class="font-semibold text-gray-900">#{{ $category->id }}</td>
                                <td>
                                    <div class="font-semibold text-gray-900">{{ $category->name }}</div>
                                </td>
                                <td>
                                    <span class="type-badge">{{ $category->type }}</span>
                                </td>
                                <td>
                                    <span class="awb-badge {{ $category->requires_awb ? 'yes' : 'no' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($category->requires_awb)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            @endif
                                        </svg>
                                        {{ $category->requires_awb ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ strtolower($category->status) === 'active' ? 'active' : 'inactive' }}">
                                        {{ $category->status }}
                                    </span>
                                </td>
                                <td class="text-gray-500 text-sm">{{ $category->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.booking-categories.edit', $category->id) }}" class="btn-edit">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.booking-categories.delete', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No Categories Found</h3>
                <p class="text-gray-600 text-sm mb-4">Get started by creating your first booking category.</p>
                <a href="{{ route('admin.booking-categories.create') }}" class="admin-btn-primary px-6 py-2.5 text-sm inline-flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Category
                </a>
            </div>
        @endif
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
            const selectAll = document.getElementById('selectAll');
            
            if (checkboxes.length > 0) {
                bulkDeleteBtn.classList.remove('hidden');
                bulkDeleteBtn.querySelector('span').textContent = `Delete Selected (${checkboxes.length})`;
            } else {
                bulkDeleteBtn.classList.add('hidden');
            }
            
            // Update select all checkbox state
            const allCheckboxes = document.querySelectorAll('.row-checkbox');
            if (checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0) {
                selectAll.checked = true;
            } else {
                selectAll.checked = false;
            }
        }

        // Bulk delete function
        function bulkDelete() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one category to delete.');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${checkboxes.length} selected category(ies)?`)) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }
    </script>

    <!-- Success Popup -->
    @if(session('success'))
        <div id="success-popup" class="success-popup">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
            </div>
            <button onclick="closePopup()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <script>
            function closePopup() {
                const popup = document.getElementById('success-popup');
                if (popup) {
                    popup.classList.add('closing');
                    setTimeout(() => {
                        popup.remove();
                    }, 300);
                }
            }
            // Auto close after 5 seconds
            setTimeout(() => {
                closePopup();
            }, 5000);
        </script>
    @endif
@endsection



