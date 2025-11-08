@extends('layouts.admin')

@section('title', 'View Support Ticket')

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
        .form-input, .form-select, .form-textarea {
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
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
        .attachment-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .attachment-image:hover {
            transform: scale(1.05);
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Support Ticket #{{ $ticket->id }}</h1>
                    <p class="text-xs text-gray-600">View and manage support ticket</p>
                </div>
            </div>
            <a href="{{ route('admin.support-tickets.all') }}" class="admin-btn-primary px-5 py-2.5 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to Tickets</span>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ticket Details -->
        <div class="lg:col-span-2">
            <div class="ticket-card p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ticket Information
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-2 block">Subject</label>
                        <p class="text-gray-900 font-bold text-lg">{{ $ticket->subject }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-2 block">Message</label>
                        <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-200">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->message }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 mb-2 block">Category</label>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-700">
                                {{ $ticket->category }}
                            </span>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-700 mb-2 block">Created At</label>
                            <p class="text-gray-600">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    @if($ticket->attachments->count() > 0)
                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-2 block">Attachments</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($ticket->attachments as $attachment)
                                <div class="border-2 border-gray-200 rounded-lg p-2">
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="attachment-image w-full h-auto">
                                    </a>
                                    <p class="text-xs text-gray-600 mt-2 truncate">{{ $attachment->file_name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Admin Response Form -->
            <div class="ticket-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Update Ticket
                </h2>

                <form method="POST" action="{{ route('admin.support-tickets.update', $ticket->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" id="priority" class="form-select" required>
                                <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $ticket->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Admin Response
                        </label>
                        <textarea name="admin_response" id="admin_response" rows="5" class="form-textarea resize-none" placeholder="Enter your response to the user...">{{ old('admin_response', $ticket->admin_response) }}</textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="admin-btn-primary px-6 py-3 text-sm font-semibold flex-1">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Update Ticket</span>
                            </div>
                        </button>
                        <a href="{{ route('admin.support-tickets.all') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
                </form>

                <!-- Delete Button - Separate from Update Form -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <form action="{{ route('admin.support-tickets.delete', $ticket->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-3 rounded-xl border-2 border-red-300 text-red-700 font-semibold hover:bg-red-50 transition text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Ticket
                        </button>
                    </form>
                </div>

                @if(session('success'))
                    <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="ticket-card p-5 sticky top-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ticket Details
                </h3>
                <div class="space-y-3 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span><strong>Ticket ID:</strong> #{{ $ticket->id }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span><strong>User:</strong> {{ $ticket->user->name }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span><strong>Email:</strong> {{ $ticket->user->email }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span><strong>Status:</strong> 
                            <span class="status-badge {{ str_replace('-', '_', $ticket->status) }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span><strong>Priority:</strong> 
                            <span class="priority-badge {{ $ticket->priority }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span><strong>Created:</strong> {{ $ticket->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($ticket->resolved_at)
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-600 mt-1.5 flex-shrink-0"></div>
                        <span><strong>Resolved:</strong> {{ $ticket->resolved_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>

                @if($ticket->admin_response)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-bold text-gray-900 mb-2">Admin Response</h4>
                    <div class="bg-blue-50 rounded-lg p-3 border-2 border-blue-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $ticket->admin_response }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

