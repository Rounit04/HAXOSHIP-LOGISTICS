@extends('layouts.admin')

@section('title', 'Frontend Settings')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<style>
    .color-preview {
        width: 35px;
        height: 35px;
        border-radius: 6px;
        border: 2px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .color-preview:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-color: #FF750F;
    }
    .image-preview {
        max-width: 120px;
        max-height: 120px;
        border-radius: 6px;
        border: 2px solid #e5e7eb;
        object-fit: cover;
    }
    .image-preview-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 6px;
        border: 2px dashed #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    .form-input {
        width: 100%;
        padding: 0.5rem 0.625rem;
        font-size: 0.8125rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: white;
    }
    .form-input:focus {
        outline: none;
        border-color: #FF750F;
        box-shadow: 0 0 0 3px rgba(255, 117, 15, 0.1);
    }
    .file-input-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .file-input-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.8125rem;
    }
    .file-input-label:hover {
        border-color: #FF750F;
        background: #fff5ed;
    }
    .file-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .success-popup {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border: 2px solid #10b981;
        border-radius: 8px;
        padding: 12px 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s ease-out;
        min-width: 280px;
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
    .tab-button {
        position: relative;
        color: #6b7280;
        border-bottom: 2px solid transparent;
    }
    .tab-button:hover {
        color: #111827;
    }
    .tab-button.active {
        color: #111827;
        border-bottom-color: #FF750F;
    }
    .tab-content {
        display: block;
    }
</style>

<!-- Success Popup -->
@if(session('success'))
    <div id="success-popup" class="success-popup">
        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-green-700 font-semibold text-sm">{{ session('success') }}</p>
        </div>
        <button onclick="closePopup()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
@endif

<div class="max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="flex items-center gap-2 mb-1">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm" style="background: var(--admin-gradient);">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900">Frontend Settings</h1>
                <p class="text-xs text-gray-600">Customize your website appearance and manage blogs</p>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <div class="flex gap-2">
            <button onclick="switchTab('settings')" id="tab-settings" class="tab-button active px-6 py-3 text-sm font-semibold border-b-2 transition-all">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Settings</span>
                </div>
            </button>
            <button onclick="switchTab('about-us')" id="tab-about-us" class="tab-button px-6 py-3 text-sm font-semibold border-b-2 transition-all">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>About Us</span>
                </div>
            </button>
            <button onclick="switchTab('blogs')" id="tab-blogs" class="tab-button px-6 py-3 text-sm font-semibold border-b-2 transition-all">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Blogs</span>
                </div>
            </button>
            <button onclick="switchTab('why-haxo-section')" id="tab-why-haxo-section" class="tab-button px-6 py-3 text-sm font-semibold border-b-2 transition-all">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Why Haxo</span>
                </div>
            </button>
            <button onclick="switchTab('pricing-section')" id="tab-pricing-section" class="tab-button px-6 py-3 text-sm font-semibold border-b-2 transition-all">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Pricing</span>
                </div>
            </button>
            <button onclick="switchTab('stats-section')" id="tab-stats-section" class="tab-button px-6 py-3 text-sm font-semibold border-b-2 transition-all">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Stats</span>
                </div>
            </button>
            <button onclick="switchTab('contact')" id="tab-contact" class="tab-button px-6 py-3 text-sm font-semibold border-b-2 transition-all">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Contact</span>
                </div>
            </button>
        </div>
    </div>

    <!-- Settings Tab Content -->
    <div id="tab-content-settings" class="tab-content">
    <form action="{{ route('admin.frontend-settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
        @csrf
        
        <!-- Logo Field -->
        <div class="form-group">
            <label class="form-label">Website Logo</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
                <div class="file-input-wrapper">
                    <label for="logo" class="file-input-label">
                        <input type="file" name="logo" id="logo" accept="image/*" class="file-input" onchange="previewImage(this, 'logo-preview')">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-xs text-gray-700">Choose Logo</span>
                    </label>
                </div>
                <div class="flex items-center justify-center">
                    @if($settings->logo && Storage::disk('public')->exists($settings->logo))
                        <img src="{{ Storage::url($settings->logo) }}" alt="Current Logo" id="logo-preview" class="image-preview" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div id="logo-preview-placeholder" class="image-preview-placeholder" style="display: none;">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @else
                        <div id="logo-preview" class="image-preview-placeholder">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">PNG, JPG, SVG up to 2MB</p>
        </div>

        <hr class="my-4 border-gray-200">

        <!-- Banner Field -->
        <div class="form-group">
            <label class="form-label">Hero Banner (Landing Page)</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
                <div class="file-input-wrapper">
                    <label for="banner" class="file-input-label">
                        <input type="file" name="banner" id="banner" accept="image/*" class="file-input" onchange="previewImage(this, 'banner-preview')">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-xs text-gray-700">Choose Banner</span>
                    </label>
                </div>
                <div class="flex items-center justify-center">
                    @if($settings->banner && Storage::disk('public')->exists($settings->banner))
                        <img src="{{ Storage::url($settings->banner) }}" alt="Current Banner" id="banner-preview" class="image-preview" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div id="banner-preview-placeholder" class="image-preview-placeholder" style="display: none;">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @else
                        <div id="banner-preview" class="image-preview-placeholder">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">PNG, JPG, SVG up to 5MB</p>
        </div>

        <hr class="my-4 border-gray-200">

        <!-- Color Settings -->
        <div class="form-group">
            <label class="form-label mb-3">Color Scheme</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Primary Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" value="{{ $settings->primary_color }}" class="color-preview" onchange="updateColorInput(this, 'primary-color-input')">
                        <input type="text" name="primary_color" value="{{ $settings->primary_color }}" class="form-input text-xs py-1.5 px-2" id="primary-color-input" onchange="updateColorPicker(this, 'primary')" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#FF750F">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Secondary Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" value="{{ $settings->secondary_color }}" class="color-preview" onchange="updateColorInput(this, 'secondary-color-input')">
                        <input type="text" name="secondary_color" value="{{ $settings->secondary_color }}" class="form-input text-xs py-1.5 px-2" id="secondary-color-input" onchange="updateColorPicker(this, 'secondary')" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ff8c3a">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" value="{{ $settings->text_color }}" class="color-preview" onchange="updateColorInput(this, 'text-color-input')">
                        <input type="text" name="text_color" value="{{ $settings->text_color }}" class="form-input text-xs py-1.5 px-2" id="text-color-input" onchange="updateColorPicker(this, 'text')" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#1b1b18">
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        <!-- Hero Section Content -->
        <div class="form-group">
            <label class="form-label mb-3">Hero Section Content</label>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hero Title</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}" class="form-input" placeholder="Hassle Free Fastest Delivery">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hero Subtitle</label>
                    <textarea name="hero_subtitle" rows="2" class="form-input resize-none" placeholder="We Committed to delivery - Make easy Efficient and quality delivery.">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Button Text</label>
                    <input type="text" name="hero_button_text" value="{{ old('hero_button_text', $settings->hero_button_text) }}" class="form-input" placeholder="Track Now">
                </div>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        <!-- Footer Settings Section -->
        <div class="form-group">
            <label class="form-label mb-3">Footer Settings</label>
            <div class="space-y-4">
                <!-- Footer Logo -->
                <div>
                    <label class="form-label">Footer Logo</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
                        <div class="file-input-wrapper">
                            <label for="footer_logo" class="file-input-label">
                                <input type="file" name="footer_logo" id="footer_logo" accept="image/*" class="file-input" onchange="previewImage(this, 'footer-logo-preview')">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span class="text-xs text-gray-700">Choose Footer Logo</span>
                            </label>
                        </div>
                        <div class="flex items-center justify-center">
                            @if($settings->footer_logo && Storage::disk('public')->exists($settings->footer_logo))
                                <img src="{{ Storage::url($settings->footer_logo) }}" alt="Current Footer Logo" id="footer-logo-preview" class="image-preview" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div id="footer-logo-preview-placeholder" class="image-preview-placeholder" style="display: none;">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @else
                                <div id="footer-logo-preview" class="image-preview-placeholder">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, SVG up to 2MB</p>
                </div>

                <!-- Footer Description -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Footer Description</label>
                    <textarea name="footer_description" rows="3" class="form-input resize-none" placeholder="Fastest platform with all courier service features...">{{ old('footer_description', $settings->footer_description) }}</textarea>
                </div>

                <!-- Social Media Links -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Social Media Links</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Facebook URL</label>
                            <input type="url" name="footer_facebook_url" value="{{ old('footer_facebook_url', $settings->footer_facebook_url) }}" class="form-input" placeholder="https://facebook.com/...">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Instagram URL</label>
                            <input type="url" name="footer_instagram_url" value="{{ old('footer_instagram_url', $settings->footer_instagram_url) }}" class="form-input" placeholder="https://instagram.com/...">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Twitter URL</label>
                            <input type="url" name="footer_twitter_url" value="{{ old('footer_twitter_url', $settings->footer_twitter_url) }}" class="form-input" placeholder="https://twitter.com/...">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Skype URL</label>
                            <input type="url" name="footer_skype_url" value="{{ old('footer_skype_url', $settings->footer_skype_url) }}" class="form-input" placeholder="https://skype.com/...">
                        </div>
                    </div>
                </div>

                <!-- App Store Links -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">App Store Links</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Google Play Store URL</label>
                            <input type="url" name="footer_google_play_url" value="{{ old('footer_google_play_url', $settings->footer_google_play_url) }}" class="form-input" placeholder="https://play.google.com/...">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Apple App Store URL</label>
                            <input type="url" name="footer_app_store_url" value="{{ old('footer_app_store_url', $settings->footer_app_store_url) }}" class="form-input" placeholder="https://apps.apple.com/...">
                        </div>
                    </div>
                </div>

                <!-- Copyright Text -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Copyright Text</label>
                    <input type="text" name="footer_copyright_text" value="{{ old('footer_copyright_text', $settings->footer_copyright_text) }}" class="form-input" placeholder="Copyright © All rights reserved...">
                </div>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        <!-- Submit Buttons -->
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="admin-btn-primary px-5 py-2 text-xs font-semibold shadow-md hover:shadow-lg transition-all">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Save Settings</span>
                </div>
            </button>
            <a href="{{ route('home') }}" target="_blank" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition text-xs shadow-sm hover:shadow-md flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span>Preview Website</span>
            </a>
        </div>
    </form>
    </div>

    <!-- About Us Tab Content -->
    <div id="tab-content-about-us" class="tab-content" style="display: none;">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Manage About Us</h2>
                <a href="{{ route('admin.about-us.edit') }}" class="admin-btn-primary px-4 py-2 text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit About Us</span>
                    </div>
                </a>
            </div>

            @php
                use Illuminate\Support\Str;
                $settings = \App\Models\FrontendSetting::getSettings();
            @endphp

            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">About Us Content</h3>
                        @if($settings->about_us_content)
                            <div class="text-sm text-gray-700 whitespace-pre-wrap mb-4">{{ Str::limit($settings->about_us_content, 200) }}</div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Last updated: {{ $settings->updated_at->format('M d, Y') }}</span>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mb-4">No content has been set yet. Click "Edit About Us" to add content.</p>
                        @endif
                        <a href="{{ route('admin.about-us.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-colors mt-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Content
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-green-700 font-semibold text-sm">{{ session('success') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Blogs Tab Content -->
    <div id="tab-content-blogs" class="tab-content" style="display: none;">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Manage Blogs</h2>
                <a href="{{ route('admin.blogs.create') }}" class="admin-btn-primary px-4 py-2 text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Create Blog</span>
                    </div>
                </a>
            </div>

            @php
                $blogs = \App\Models\Blog::latest()->get();
            @endphp

            @if($blogs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Image</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Author</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Views</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($blogs as $blog)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    @if($blog->image_url)
                                        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 max-w-xs truncate">{{ $blog->title }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $blog->formatted_date }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $blog->author }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $blog->views }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $blog->status == 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($blog->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="px-3 py-1.5 bg-blue-500 text-white text-xs font-semibold rounded-lg hover:bg-blue-600 transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.blogs.delete', $blog->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this blog?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-lg hover:bg-red-600 transition-colors">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Blogs Found</h3>
                    <p class="text-gray-600 text-sm mb-4">Get started by creating your first blog post.</p>
                    <a href="{{ route('admin.blogs.create') }}" class="admin-btn-primary px-6 py-2.5 text-sm inline-flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Blog
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Why Haxo Section Tab Content -->
    <div id="tab-content-why-haxo-section" class="tab-content" style="display: none;">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Manage Why Haxo Section</h2>
                <a href="{{ route('admin.why-haxo-section.edit') }}" class="admin-btn-primary px-4 py-2 text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit Why Haxo</span>
                    </div>
                </a>
            </div>

            @php
                $settings = \App\Models\FrontendSetting::getSettings();
            @endphp

            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Why Haxo Shipping Section</h3>
                        @if($settings->why_haxo_section_title || $settings->why_haxo_section_content)
                            <div class="text-sm text-gray-700 whitespace-pre-wrap mb-4">
                                <p class="font-semibold mb-2">{{ $settings->why_haxo_section_title ?? 'Why Haxo Shipping' }}</p>
                                <p>{{ Str::limit($settings->why_haxo_section_content ?? '', 200) }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mb-4">No content has been set yet. Click "Edit Why Haxo" to add content.</p>
                        @endif
                        <a href="{{ route('admin.why-haxo-section.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-colors mt-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Content
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Section Tab Content -->
    <div id="tab-content-pricing-section" class="tab-content" style="display: none;">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Manage Pricing Section</h2>
                <a href="{{ route('admin.pricing-section.edit') }}" class="admin-btn-primary px-4 py-2 text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit Pricing</span>
                    </div>
                </a>
            </div>

            @php
                $settings = \App\Models\FrontendSetting::getSettings();
            @endphp

            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Haxo Shipping Pricing Section</h3>
                        @if($settings->pricing_section_title || $settings->pricing_section_content)
                            <div class="text-sm text-gray-700 whitespace-pre-wrap mb-4">
                                <p class="font-semibold mb-2">{{ $settings->pricing_section_title ?? 'Haxo Shipping Pricing' }}</p>
                                <p>{{ Str::limit($settings->pricing_section_content ?? '', 200) }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mb-4">No content has been set yet. Click "Edit Pricing" to add content.</p>
                        @endif
                        <a href="{{ route('admin.pricing-section.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-colors mt-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Content
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section Tab Content -->
    <div id="tab-content-stats-section" class="tab-content" style="display: none;">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Manage Stats Section</h2>
                <a href="{{ route('admin.stats-section.edit') }}" class="admin-btn-primary px-4 py-2 text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit Stats</span>
                    </div>
                </a>
            </div>

            @php
                $settings = \App\Models\FrontendSetting::getSettings();
            @endphp

            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Stats Section</h3>
                        @if($settings->stats_section_content)
                            <div class="text-sm text-gray-700 whitespace-pre-wrap mb-4">
                                <p>{{ Str::limit($settings->stats_section_content ?? '', 200) }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mb-4">No content has been set yet. Click "Edit Stats" to add content.</p>
                        @endif
                        <a href="{{ route('admin.stats-section.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-colors mt-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Content
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Tab Content -->
    <div id="tab-content-contact" class="tab-content" style="display: none;">
        <form action="{{ route('admin.frontend-settings.update') }}" method="POST" class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            @csrf
            
            <div class="form-group">
                <label class="form-label mb-3">Contact Information</label>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Contact Email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}" class="form-input" placeholder="haxoshipping@gmail.com">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Contact Phone</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}" class="form-input" placeholder="8130465575">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Contact Address</label>
                        <input type="text" name="contact_address" value="{{ old('contact_address', $settings->contact_address) }}" class="form-input" placeholder="a-123, dellhi">
                    </div>
                </div>
            </div>

            <hr class="my-4 border-gray-200">

            <!-- Submit Buttons -->
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="admin-btn-primary px-5 py-2 text-xs font-semibold shadow-md hover:shadow-lg transition-all">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Save Contact Settings</span>
                    </div>
                </button>
                <a href="{{ route('contact') }}" target="_blank" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition text-xs shadow-sm hover:shadow-md flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Preview Contact Page</span>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const previewContainer = document.getElementById(previewId);
    if (!previewContainer) {
        console.error('Preview container not found:', previewId);
        return;
    }
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Check if preview is a div (placeholder) or img
            if (previewContainer.tagName === 'DIV') {
                // Replace div with img
                const img = document.createElement('img');
                img.id = previewId;
                img.src = e.target.result;
                img.className = 'image-preview';
                img.alt = 'Preview';
                img.style.display = 'block';
                previewContainer.parentNode.replaceChild(img, previewContainer);
            } else {
                // It's already an img, just update src
                previewContainer.src = e.target.result;
                previewContainer.style.display = 'block';
            }
        };
        reader.onerror = function() {
            console.error('Error reading file');
            alert('Error reading file. Please try again.');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updateColorInput(colorInput, inputId) {
    const textInput = document.getElementById(inputId);
    if (textInput) {
        textInput.value = colorInput.value;
    }
}

function updateColorPicker(textInput, colorType) {
    const colorInput = document.querySelector(`input[type="color"][name="${colorType}_color"]`);
    if (colorInput && /^#[0-9A-F]{6}$/i.test(textInput.value)) {
        colorInput.value = textInput.value;
    }
}

function closePopup() {
    const popup = document.getElementById('success-popup');
    if (popup) {
        popup.classList.add('closing');
        setTimeout(() => {
            popup.remove();
        }, 300);
    }
}

// Auto-close popup after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('success-popup');
    if (popup) {
        setTimeout(() => {
            closePopup();
        }, 5000);
    }
});

// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        button.style.color = '#6b7280';
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById('tab-content-' + tabName);
    if (selectedContent) {
        selectedContent.style.display = 'block';
    }
    
    // Add active class to selected tab button
    const selectedButton = document.getElementById('tab-' + tabName);
    if (selectedButton) {
        selectedButton.classList.add('active');
        selectedButton.style.color = '#111827';
    }
}
</script>
@endsection

