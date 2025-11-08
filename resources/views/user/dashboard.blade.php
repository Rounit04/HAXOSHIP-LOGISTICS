@extends('layouts.app')

@section('title', 'Dashboard - Haxo Shipping')

@section('content')
<style>
    .dashboard-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 2rem 0;
    }
    .welcome-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(255, 117, 15, 0.1);
        border: 2px solid rgba(255, 117, 15, 0.1);
        margin-bottom: 2rem;
    }
    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 2px solid rgba(255, 117, 15, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #FF750F 0%, #ff8c3a 100%);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(255, 117, 15, 0.2);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fff5ed 0%, #ffe8d6 100%);
        box-shadow: 0 4px 12px rgba(255, 117, 15, 0.2);
    }
    .action-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 2px solid rgba(255, 117, 15, 0.1);
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        color: inherit;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(255, 117, 15, 0.2);
        border-color: rgba(255, 117, 15, 0.3);
    }
    .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: bold;
        box-shadow: 0 8px 20px rgba(255, 117, 15, 0.3);
    }
</style>

<div class="dashboard-container">
    <div class="container mx-auto px-4 max-w-7xl">
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl flex items-center gap-3 shadow-lg">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-green-700 font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Notifications Section -->
        @if($notifications->count() > 0 || $unreadNotificationsCount > 0)
        <div class="mb-6">
            <div class="bg-white rounded-xl shadow-lg border-2 border-orange-100 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Notifications</h2>
                            @if($unreadNotificationsCount > 0)
                                <p class="text-sm text-orange-100">{{ $unreadNotificationsCount }} unread notification(s)</p>
                            @endif
                        </div>
                    </div>
                    @if($unreadNotificationsCount > 0)
                        <button onclick="markAllAsRead()" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-semibold rounded-lg transition">
                            Mark all as read
                        </button>
                    @endif
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    @foreach($notifications as $notification)
                        <div class="notification-item mb-3 p-4 rounded-lg border-2 transition-all {{ $notification->read ? 'bg-gray-50 border-gray-200' : 'bg-orange-50 border-orange-200' }}" data-notification-id="{{ $notification->id }}">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $notification->read ? 'bg-gray-200' : 'bg-orange-200' }}">
                                    @if($notification->type === 'support_ticket_status_update' || $notification->type === 'support_ticket_response')
                                        <svg class="w-5 h-5 {{ $notification->read ? 'text-gray-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 {{ $notification->read ? 'text-gray-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900 mb-1 {{ $notification->read ? '' : 'text-orange-900' }}">{{ $notification->title }}</h3>
                                            <p class="text-sm text-gray-700 mb-2">{{ $notification->message }}</p>
                                            <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(!$notification->read)
                                            <button onclick="markAsRead({{ $notification->id }})" class="text-xs text-orange-600 hover:text-orange-700 font-semibold px-2 py-1 rounded hover:bg-orange-100 transition">
                                                Mark as read
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-gray-600 mb-4">Manage your shipments, track packages, and access all your shipping services.</p>
                    <div class="flex flex-wrap items-center gap-4 justify-center md:justify-start">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $user->email }}</span>
                        </div>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Verified Account
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Unverified
                            </span>
                        @endif
                        @if($user->role)
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-semibold bg-orange-100 text-orange-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                {{ $user->role }}
                            </span>
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-bold rounded-lg hover:from-red-600 hover:to-red-700 transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="stat-icon">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Total Shipments</h3>
                <p class="text-3xl font-bold text-gray-900">0</p>
                <p class="text-xs text-gray-500 mt-2">All time shipments</p>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="stat-icon">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Delivered</h3>
                <p class="text-3xl font-bold text-gray-900">0</p>
                <p class="text-xs text-gray-500 mt-2">Successfully delivered</p>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="stat-icon">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">In Transit</h3>
                <p class="text-3xl font-bold text-gray-900">0</p>
                <p class="text-xs text-gray-500 mt-2">Currently shipping</p>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="stat-icon">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Total Spent</h3>
                <p class="text-3xl font-bold text-gray-900">{{ currency(0) }}</p>
                <p class="text-xs text-gray-500 mt-2">Lifetime spending</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('tracking') }}" class="action-card">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100">
                            <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Track Package</h3>
                            <p class="text-sm text-gray-600">Track your shipment status</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('pricing') }}" class="action-card">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100">
                            <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">View Pricing</h3>
                            <p class="text-sm text-gray-600">Check shipping rates</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('contact') }}" class="action-card">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100">
                            <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Contact Support</h3>
                            <p class="text-sm text-gray-600">Get help from our team</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Support Ticket Section -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-gray-100 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Support Ticket</h2>
            
            <form method="POST" action="{{ route('support-tickets.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200" required>
                            <option value="">Select Category</option>
                            @foreach($supportCategories as $cat)
                                <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Subject -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200" placeholder="Enter ticket subject" required>
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" id="message" rows="5" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 resize-none" placeholder="Describe your issue or question..." required>{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Attachments -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Attach Images (Optional)
                    </label>
                    <input type="file" name="attachments[]" id="attachments" multiple accept="image/*" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                    <p class="mt-2 text-xs text-gray-500">You can attach multiple images. Max 5MB per file.</p>
                    @error('attachments.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Submit Ticket
                    </button>
                </div>
            </form>
        </div>

        <!-- My Support Tickets -->
        @if($supportTickets->count() > 0)
        <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-gray-100 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">My Support Tickets</h2>
            <div class="space-y-4">
                @foreach($supportTickets as $ticket)
                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-orange-300 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $ticket->subject }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ \Illuminate\Support\Str::limit($ticket->message, 150) }}</p>
                            <div class="flex flex-wrap items-center gap-3 text-xs">
                                <span class="px-3 py-1 rounded-full font-semibold
                                    @if($ticket->status == 'open') bg-blue-100 text-blue-700
                                    @elseif($ticket->status == 'in_progress') bg-yellow-100 text-yellow-700
                                    @elseif($ticket->status == 'resolved') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                                <span class="px-3 py-1 rounded-full font-semibold
                                    @if($ticket->priority == 'urgent') bg-red-100 text-red-700
                                    @elseif($ticket->priority == 'high') bg-orange-100 text-orange-700
                                    @elseif($ticket->priority == 'medium') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($ticket->priority) }} Priority
                                </span>
                                <span class="text-gray-500">{{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                                @if($ticket->attachments->count() > 0)
                                    <span class="text-gray-500 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $ticket->attachments->count() }} image(s)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Recent Activity</h2>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 font-medium">No recent activity</p>
                <p class="text-gray-400 text-sm mt-2">Your shipping history will appear here</p>
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('bg-orange-50', 'border-orange-200');
                notificationItem.classList.add('bg-gray-50', 'border-gray-200');
                const icon = notificationItem.querySelector('.w-10.h-10');
                if (icon) {
                    icon.classList.remove('bg-orange-200');
                    icon.classList.add('bg-gray-200');
                }
                const svg = notificationItem.querySelector('svg');
                if (svg) {
                    svg.classList.remove('text-orange-600');
                    svg.classList.add('text-gray-600');
                }
                const title = notificationItem.querySelector('h3');
                if (title) {
                    title.classList.remove('text-orange-900');
                }
                const markAsReadBtn = notificationItem.querySelector('button');
                if (markAsReadBtn) {
                    markAsReadBtn.remove();
                }
                // Reload page after a short delay to update unread count
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection

