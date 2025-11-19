@extends('layouts.admin')

@section('title', 'User Roles & Permission')

@section('content')
    <style>
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid rgba(255, 117, 15, 0.1);
        }
        .tab-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .tab-header {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
            border-bottom: 2px solid #e5e7eb;
            padding: 0;
            display: flex;
            gap: 0;
        }
        .tab-button {
            padding: 16px 32px;
            border: none;
            background: transparent;
            color: #6b7280;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tab-button:hover {
            background: rgba(255, 117, 15, 0.05);
            color: #FF750F;
        }
        .tab-button.active {
            color: #FF750F;
            background: white;
        }
        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #FF750F 0%, #ff750f 100%);
        }
        .empty-state {
            padding: 80px 40px;
            text-align: center;
            background: linear-gradient(135deg, #fff5ed 0%, #fff5ed 100%);
            border-radius: 16px;
            border: 2px dashed #d1d5db;
        }
        .empty-state-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #fff5ed 0%, #e0ddff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(255, 117, 15, 0.15);
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 8px 24px rgba(255, 117, 15, 0.3);">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">User Roles & Permission</h1>
                    <p class="text-sm text-gray-600 font-medium">Manage user roles and assign permissions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Container -->
    <div class="tab-container">
        <!-- Tab Header -->
        <div class="tab-header">
            <button class="tab-button active" onclick="switchTab('users')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Add User</span>
            </button>
            <button class="tab-button" onclick="switchTab('roles')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Create Roles & Permission</span>
            </button>
        </div>

        <!-- User List Tab -->
        <div id="users-tab" class="tab-content active p-8">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">User List</h2>
                    <p class="text-sm text-gray-600">Manage and assign roles to users</p>
                </div>
                <button onclick="showCreateUserModal()" class="admin-btn-primary px-6 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add New Admin User</span>
                    </div>
                </button>
            </div>

            <!-- User Table -->
            <div class="overflow-x-auto rounded-2xl border-2 border-gray-200 shadow-lg bg-white">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 via-gray-50 to-gray-100 border-b-2 border-gray-200">
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Current Role</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Registered</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($users as $user)
                            <tr class="border-b border-gray-100 hover:bg-gradient-to-r hover:from-orange-50 hover:via-orange-50 hover:to-orange-50 transition-all duration-300">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="text-base font-bold text-gray-900">#{{ $user->id }}</span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg flex-shrink-0" style="background: var(--admin-gradient);">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-base font-bold text-gray-900 mb-1">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500 font-medium">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-gray-700">{{ $user->email }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex flex-col gap-2">
                                        @if($user->banned_at)
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Banned
                                            </span>
                                        @endif
                                        @if($user->email_verified_at)
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Verified
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Unverified
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="admin-badge px-5 py-2.5 text-sm font-bold">
                                        {{ $user->role ?? 'No Role' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3 text-sm text-gray-600">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-700">{{ $user->created_at->format('M d, Y') }}</span>
                                            <span class="text-xs text-gray-500 mt-0.5">{{ $user->created_at->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex flex-col gap-3">
                                        <!-- Role Assignment Dropdown -->
                                        <form action="{{ route('admin.roles.assign') }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <select name="role" onchange="this.form.submit()" class="admin-select px-4 py-2.5 min-w-[180px] font-semibold text-sm rounded-lg border-2 border-gray-200 hover:border-orange-400 focus:border-orange-500 transition-all shadow-sm hover:shadow-md">
                                                <option value="">Select Role</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role }}" {{ ($user->role ?? '') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex flex-wrap gap-2">
                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 text-xs font-bold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 border border-blue-200 transition-all flex items-center gap-1.5" title="Edit User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </a>
                                            
                                            <!-- Login As User Button -->
                                            <form action="{{ route('admin.users.login-as', $user->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 text-xs font-bold text-purple-600 bg-purple-50 rounded-lg hover:bg-purple-100 border border-purple-200 transition-all flex items-center gap-1.5" title="Login as this user">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    Login As
                                                </button>
                                            </form>
                                            
                                            <!-- Verify/Unverify Button -->
                                            @if($user->email_verified_at)
                                                <form action="{{ route('admin.users.unverify', $user->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 text-xs font-bold text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 border border-orange-200 transition-all flex items-center gap-1.5" title="Unverify User">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                        Unverify
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.verify', $user->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 text-xs font-bold text-green-600 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200 transition-all flex items-center gap-1.5" title="Verify User">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Verify
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <!-- Ban/Unban Button -->
                                            @if($user->banned_at)
                                                <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 text-xs font-bold text-green-600 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200 transition-all flex items-center gap-1.5" title="Unban User">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                        Unban
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 text-xs font-bold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 border border-red-200 transition-all flex items-center gap-1.5" title="Ban User" onclick="return confirm('Are you sure you want to ban this user?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                        Ban
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <svg class="w-16 h-16 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">No Users Found</h3>
                                        <p class="text-gray-600 mb-6 max-w-md mx-auto">Get started by adding your first user. Click the button above to add a new user to the system.</p>
                                        <button class="admin-btn-primary">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                <span>Add First User</span>
                                            </div>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Summary Stats -->
            @if(count($users) > 0)
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border-2 border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide mb-1">Total Users</p>
                                <p class="text-3xl font-bold text-blue-900">{{ count($users) }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-lg bg-blue-200 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border-2 border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-1">Verified</p>
                                <p class="text-3xl font-bold text-green-900">{{ $users->whereNotNull('email_verified_at')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-lg bg-green-200 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 border-2 border-orange-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-orange-700 uppercase tracking-wide mb-1">With Roles</p>
                                <p class="text-3xl font-bold text-orange-900">{{ $users->whereNotNull('role')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-lg bg-orange-200 flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mt-6 p-5 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl flex items-center gap-3 shadow-lg">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-green-700 font-bold">{{ session('success') }}</p>
                </div>
            @endif
        </div>

        <!-- Create Roles Tab -->
        <div id="roles-tab" class="tab-content p-8 lg:p-10">
            <div class="max-w-5xl mx-auto">
                <!-- Create Roles Section -->
                <div class="mb-10">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background: var(--admin-gradient);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-1">Create Roles & Permission</h2>
                            <p class="text-sm text-gray-600 font-medium">Define new roles and assign specific permissions to them</p>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('admin.roles.create') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Role Name Input -->
                    <div class="admin-card p-8 bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 shadow-lg">
                        <label class="block text-sm font-bold text-gray-900 mb-5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, #fff5ed 0%, #ffe8d6 100%);">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <span class="text-base">Role Name</span>
                        </label>
                        <input type="text" name="role_name" required class="admin-input w-full text-base px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all" placeholder="e.g., Editor, Moderator, Manager, Viewer">
                    </div>

                    <!-- Permissions Section -->
                    <div class="admin-card p-8 bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 shadow-lg">
                        <label class="block text-sm font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, #fff5ed 0%, #ffe8d6 100%);">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <span class="text-base">Permissions</span>
                        </label>
                        <div class="space-y-6">
                            @forelse($permissions ?? [] as $group => $groupPermissions)
                                <div class="border-2 border-gray-100 rounded-xl p-6 bg-gray-50">
                                    <h4 class="font-bold text-gray-900 mb-4 text-lg capitalize">{{ $group ?? 'Other' }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($groupPermissions as $permission)
                                            <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-xl hover:border-orange-400 hover:bg-gradient-to-br hover:from-orange-50 hover:to-orange-50 cursor-pointer transition-all duration-300 group shadow-sm hover:shadow-md">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->slug }}" class="mt-1 w-5 h-5 rounded border-2 border-gray-300 text-orange-600 focus:ring-orange-500 focus:ring-2 cursor-pointer">
                                                <div class="flex-1">
                                                    <span class="font-bold text-gray-900 block mb-1 group-hover:text-orange-700 transition text-sm">{{ $permission->name }}</span>
                                                    @if($permission->description)
                                                        <span class="text-xs text-gray-600 font-medium">{{ $permission->description }}</span>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-600 text-center py-8">No permissions available. Please run the permission seeder.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4 pt-4">
                        <button type="submit" class="admin-btn-primary px-10 py-4 text-base font-bold shadow-lg hover:shadow-xl transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Create Role</span>
                            </div>
                        </button>
                        <button type="reset" class="px-10 py-4 rounded-xl border-2 border-gray-300 text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-400 transition text-base shadow-sm hover:shadow-md">
                            Reset Form
                        </button>
                    </div>
                </form>

                @if(session('success'))
                    <div class="mt-8 p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl flex items-center gap-4 shadow-lg">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 shadow-md">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-green-700 font-bold text-base">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Existing Roles Section -->
                <div class="mt-16 pt-8 border-t-2 border-gray-200">
                    <div class="mb-8">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, #fff5ed 0%, #ffe8d6 100%);">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-1">Existing Roles</h3>
                                <p class="text-sm text-gray-600 font-medium">Manage your current roles and permissions</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($roles ?? [] as $role)
                            <div class="admin-card p-6 group hover:shadow-xl transition-all duration-300 border-2 border-gray-200 hover:border-orange-300 bg-gradient-to-br from-white to-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-5">
                                        <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg transition-transform group-hover:scale-110 group-hover:rotate-3" style="background: var(--admin-gradient);">
                                            {{ strtoupper(substr($role->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-xl mb-1">{{ $role->name }}</h4>
                                            <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">{{ $role->permissions->count() }} Permissions</p>
                                            @if($role->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ $role->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8 text-gray-600">
                                <p>No roles created yet. Create your first role above.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tab-content {
            display: none;
            animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
    </style>

    <!-- Create Admin User Modal -->
    <div id="createUserModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] hidden items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b-2 border-gray-200 p-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-900">Create New Admin User</h3>
                <button onclick="closeCreateUserModal()" class="w-10 h-10 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('admin.roles.create-admin-user') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Name</label>
                        <input type="text" name="name" required class="admin-input w-full" placeholder="Full Name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Email</label>
                        <input type="email" name="email" required class="admin-input w-full" placeholder="email@example.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Password</label>
                        <input type="password" name="password" required minlength="8" class="admin-input w-full" placeholder="Minimum 8 characters">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" required minlength="8" class="admin-input w-full" placeholder="Confirm password">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Assign Role (Optional)</label>
                    <select name="role_id" class="admin-select w-full">
                        <option value="">No Role</option>
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-4">Select Permissions</label>
                    <div class="space-y-4 max-h-96 overflow-y-auto border-2 border-gray-200 rounded-xl p-4">
                        @forelse($permissions ?? [] as $group => $groupPermissions)
                            <div class="mb-6">
                                <h5 class="font-bold text-gray-900 mb-3 capitalize text-base">{{ $group ?? 'Other' }}</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-start gap-2 p-3 border-2 border-gray-200 rounded-lg hover:border-orange-400 hover:bg-orange-50 cursor-pointer transition">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->slug }}" class="mt-1 w-4 h-4 rounded border-2 border-gray-300 text-orange-600 focus:ring-orange-500">
                                            <div class="flex-1">
                                                <span class="font-semibold text-gray-900 text-sm block">{{ $permission->name }}</span>
                                                @if($permission->description)
                                                    <span class="text-xs text-gray-600">{{ $permission->description }}</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600 text-center py-4">No permissions available.</p>
                        @endforelse
                    </div>
                </div>
                
                <div class="flex gap-4 pt-4 border-t-2 border-gray-200">
                    <button type="submit" class="admin-btn-primary px-8 py-3 flex-1">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Create Admin User</span>
                        </div>
                    </button>
                    <button type="button" onclick="closeCreateUserModal()" class="px-8 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Remove active class from all tabs and content
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab and content
            event.target.classList.add('active');
            document.getElementById(tab + '-tab').classList.add('active');
        }
        
        function showCreateUserModal() {
            const modal = document.getElementById('createUserModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }
        
        function closeCreateUserModal() {
            const modal = document.getElementById('createUserModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                document.body.style.overflow = ''; // Restore scrolling
            }
        }
        
        // Close modal on outside click
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('createUserModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeCreateUserModal();
                    }
                });
            }
            
            // Close modal on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('createUserModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeCreateUserModal();
                    }
                }
            });
        });
    </script>
@endsection



