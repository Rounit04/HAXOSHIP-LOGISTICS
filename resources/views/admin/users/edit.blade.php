@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('admin.roles') }}" class="w-10 h-10 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-all">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">Edit User</h1>
                <p class="text-sm text-gray-600 font-medium">Update user information and settings</p>
            </div>
        </div>
    </div>

    <div class="admin-card p-8 bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 shadow-lg">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name Field -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Full Name
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="admin-input w-full text-base px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all" placeholder="Enter full name">
                    @error('name')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="admin-input w-full text-base px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all" placeholder="Enter email address">
                    @error('email')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Field -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Role
                    </label>
                    <select name="role" class="admin-select w-full text-base px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                        <option value="">No Role</option>
                        @foreach(['Admin', 'Manager', 'Staff', 'User', 'Viewer'] as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Status Information -->
                <div class="bg-gray-50 rounded-xl p-6 border-2 border-gray-200">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Account Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Email Verified:</span>
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Unverified
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Account Status:</span>
                            @if($user->banned_at)
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    Banned
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Active
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Member Since:</span>
                            <span class="text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Last Updated:</span>
                            <span class="text-sm text-gray-600">{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 pt-6 mt-6 border-t-2 border-gray-200">
                <button type="submit" class="admin-btn-primary px-10 py-4 text-base font-bold shadow-lg hover:shadow-xl transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Update User</span>
                    </div>
                </button>
                <a href="{{ route('admin.roles') }}" class="px-10 py-4 rounded-xl border-2 border-gray-300 text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-400 transition text-base shadow-sm hover:shadow-md">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

