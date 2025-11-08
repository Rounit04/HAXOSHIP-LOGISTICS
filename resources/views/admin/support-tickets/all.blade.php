@extends('layouts.admin')

@section('title', 'Support Tickets')

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
        .ticket-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .ticket-table {
            width: 100%;
        }
        .ticket-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .ticket-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .ticket-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .ticket-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
            transform: translateX(2px);
        }
        .ticket-table tbody td {
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
        .status-badge.open {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }
        .status-badge.in_progress {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }
        .status-badge.resolved {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }
        .status-badge.closed {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #4b5563;
        }
        .priority-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .priority-badge.urgent {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }
        .priority-badge.high {
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
            color: #9a3412;
        }
        .priority-badge.medium {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }
        .priority-badge.low {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #4b5563;
        }
        .btn-view {
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
        .btn-view:hover {
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
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Support Tickets</h1>
                    <p class="text-xs text-gray-600">Manage all support tickets from users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="ticket-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                All Support Tickets
            </h2>
            <div class="text-sm text-gray-600 font-medium">
                Total: <span class="font-bold text-orange-600">{{ $tickets->count() }}</span> Tickets
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

        <!-- Search and Filter Form -->
        <form method="GET" action="{{ route('admin.support-tickets.all') }}" class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by subject, message, user name, or email..." class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                </div>
                <div>
                    <select name="status" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                        <option value="">All Status</option>
                        <option value="open" {{ $statusFilter == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $statusFilter == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $statusFilter == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $statusFilter == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <select name="priority" class="flex-1 px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                        <option value="">All Priority</option>
                        <option value="low" {{ $priorityFilter == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $priorityFilter == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $priorityFilter == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $priorityFilter == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    <button type="submit" class="admin-btn-primary px-6 py-2.5 text-sm font-semibold">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    @if($search || $statusFilter || $priorityFilter)
                        <a href="{{ route('admin.support-tickets.all') }}" class="px-4 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($tickets->count() > 0)
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="ticket-table min-w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Attachments</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td class="font-semibold text-gray-900">#{{ $ticket->id }}</td>
                                <td>
                                    <div class="font-semibold text-gray-900">{{ $ticket->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $ticket->user->email }}</div>
                                </td>
                                <td>
                                    <div class="font-semibold text-gray-900">{{ \Illuminate\Support\Str::limit($ticket->subject, 40) }}</div>
                                </td>
                                <td>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                        {{ $ticket->category }}
                                    </span>
                                </td>
                                <td>
                                    <span class="priority-badge {{ $ticket->priority }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ str_replace('-', '_', $ticket->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($ticket->attachments->count() > 0)
                                        <span class="text-sm text-gray-600 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $ticket->attachments->count() }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">No attachments</span>
                                    @endif
                                </td>
                                <td class="text-gray-500 text-sm">{{ $ticket->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.support-tickets.view', $ticket->id) }}" class="btn-view">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>
                                        <form action="{{ route('admin.support-tickets.delete', $ticket->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
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
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No Support Tickets Found</h3>
                <p class="text-gray-600 text-sm">Support tickets from users will appear here.</p>
            </div>
        @endif
    </div>
@endsection

