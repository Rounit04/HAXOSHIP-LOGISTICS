@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <div class="admin-card overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    All Users
                </h3>
                <button class="admin-btn-primary">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add New User</span>
                    </div>
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="admin-table min-w-full">
                <thead>
                    <tr>
                        <th class="text-left">ID</th>
                        <th class="text-left">User</th>
                        <th class="text-left">Email</th>
                        <th class="text-left">Registered</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="font-medium text-gray-900">#{{ $user->id }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm" style="background: var(--admin-gradient);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-900 block">{{ $user->name }}</span>
                                        @if($user->email_verified_at)
                                            <span class="text-xs text-green-600 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Verified
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Unverified</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-600">{{ $user->email }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $user->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs">at {{ $user->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <button class="px-4 py-2 text-sm font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition">
                                        Edit
                                    </button>
                                    <button class="px-4 py-2 text-sm font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-gray-500 font-medium">No users found</p>
                                    <p class="text-gray-400 text-sm">Users will appear here after they register or login</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-8 py-4 border-t bg-gray-50">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600 font-medium">
                    Showing <span class="font-bold text-gray-900">{{ count($users) }}</span> total users
                </p>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $users->whereNotNull('email_verified_at')->count() }} Verified
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $users->whereNull('email_verified_at')->count() }} Unverified
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection


