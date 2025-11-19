@extends('layouts.admin')

@section('title', 'All Zones')

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
        .zone-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .zone-table {
            width: 100%;
        }
        .zone-table thead {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        }
        .zone-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .zone-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }
        .zone-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5ed 0%, #fff5ed 100%);
        }
        .zone-table tbody td {
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
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(255, 117, 15, 0.1);
            color: #FF750F;
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">All Zones</h1>
                    <p class="text-xs text-gray-600">Manage and edit all zones - Single, bulk, editable Status (Active/Inactive)</p>
                </div>
            </div>
            <a href="{{ route('admin.zones.create') }}" class="admin-btn-primary px-5 py-2.5 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Create Zone</span>
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
        
        <form method="GET" action="{{ route('admin.zones.all') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Pincode Search -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Pincode
                </label>
                <input type="text" name="search" value="{{ $searchParams['search'] ?? '' }}" class="search-input" placeholder="Search by pincode">
            </div>
            
            <!-- Network Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                    Network
                </label>
                <select name="network" class="search-select">
                    <option value="">All Networks</option>
                    @foreach($networks ?? [] as $network)
                        <option value="{{ $network['name'] }}" {{ ($searchParams['network'] ?? '') == $network['name'] ? 'selected' : '' }}>{{ $network['name'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Country Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 002 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Country
                </label>
                <select name="country" class="search-select">
                    <option value="">All Countries</option>
                    @foreach($countries ?? [] as $country)
                        <option value="{{ $country['name'] }}" {{ ($searchParams['country'] ?? '') == $country['name'] ? 'selected' : '' }}>{{ $country['name'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <svg class="w-3.5 h-3.5 text-orange-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Status
                </label>
                <select name="status" class="search-select">
                    <option value="">All Status</option>
                    <option value="Active" {{ ($searchParams['status'] ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ ($searchParams['status'] ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
                @if($searchParams['search'] ?? '' || $searchParams['network'] ?? '' || $searchParams['country'] ?? '' || $searchParams['status'] ?? '')
                    <a href="{{ route('admin.zones.all') }}" class="px-4 py-2.5 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Zones Table -->
    <div class="zone-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Zone List
            </h2>
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-600 font-medium">
                    Total: <span class="font-bold text-orange-600">{{ count($zones) }}</span> Zones
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
            @php
                $errorText = session('error');
                $errors = explode("\n", $errorText);
                $errors = array_filter($errors, function($error) {
                    return !empty(trim($error));
                });
                $summary = '';
                $errorList = [];
                
                // Separate summary from error list
                foreach ($errors as $error) {
                    $trimmed = trim($error);
                    if (strpos($trimmed, 'Found') !== false && strpos($trimmed, 'error(s)') !== false) {
                        $summary = $trimmed;
                    } else {
                        $errorList[] = $trimmed;
                    }
                }
                $totalErrors = count($errorList);
            @endphp
            
            <div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: flex; position: fixed; top: 0; left: 0; right: 0; bottom: 0; animation: fadeIn 0.2s ease-out;">
                <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 flex flex-col" style="max-height: 80vh; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); position: relative; animation: slideUp 0.3s ease-out;">
                    <!-- Modal Header - Fixed at top -->
                    <div class="bg-gradient-to-r from-red-500 to-red-600 px-5 py-4 flex items-center justify-between rounded-t-lg flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-white bg-opacity-20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Import Validation Failed</h3>
                                <p class="text-xs text-red-100">Found {{ $totalErrors }} error(s) - Please review and fix</p>
                            </div>
                        </div>
                        <button onclick="closeErrorModal()" class="text-white hover:text-red-200 transition p-1.5 rounded-lg hover:bg-white hover:bg-opacity-10 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Modal Body - Scrollable middle section -->
                    <div class="flex-1 overflow-hidden flex flex-col bg-gray-50 min-h-0">
                        <!-- Summary Banner - Fixed -->
                        <div class="bg-red-50 border-b border-red-200 px-5 py-3 flex-shrink-0">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <p class="text-sm font-semibold text-red-800">No zones were imported. All errors must be resolved before importing.</p>
                            </div>
                        </div>
                        
                        <!-- Error List Container - Scrollable -->
                        <div class="flex-1 overflow-hidden px-5 py-4 min-h-0">
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm h-full flex flex-col">
                                <!-- Error List Header - Fixed -->
                                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between flex-shrink-0">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Error Details ({{ $totalErrors }} total)</span>
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium">
                                        <span id="visibleCount">{{ min($totalErrors, 20) }}</span> / {{ $totalErrors }} visible
                                    </div>
                                </div>
                                
                                <!-- Scrollable Error List -->
                                <div class="flex-1 overflow-y-auto min-h-0" id="errorScrollContainer">
                                    <div class="divide-y divide-gray-100">
                                        @foreach($errorList as $index => $error)
                                            <div class="px-4 py-3 hover:bg-red-50 transition-colors">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0 mt-0.5">
                                                        <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center">
                                                            <span class="text-xs font-bold text-red-600">{{ $index + 1 }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm text-gray-800 leading-relaxed break-words">{{ $error }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- Scroll Indicator - Fixed at bottom of scroll area -->
                                @if($totalErrors > 20)
                                <div class="px-4 py-2 border-t border-gray-200 bg-gray-50 text-center flex-shrink-0">
                                    <p class="text-xs text-gray-500">Scroll down to see all {{ $totalErrors }} errors</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer - Fixed at bottom -->
                    <div class="bg-white border-t border-gray-200 px-5 py-4 rounded-b-lg flex items-center justify-between flex-shrink-0">
                        <div class="text-xs text-gray-500">
                            <span class="font-medium">{{ $totalErrors }}</span> error(s) found
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="closeErrorModal()" class="px-5 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                Close
                            </button>
                            <button onclick="closeErrorModal()" class="px-5 py-2 text-sm font-semibold text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition shadow-sm">
                                I Understand
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <style>
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }
                    to {
                        opacity: 1;
                    }
                }
                
                @keyframes slideUp {
                    from {
                        transform: translateY(20px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }
                
                @keyframes fadeOut {
                    from {
                        opacity: 1;
                    }
                    to {
                        opacity: 0;
                    }
                }
                
                #errorModal {
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    z-index: 9999 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    background: rgba(0, 0, 0, 0.5) !important;
                    backdrop-filter: blur(2px);
                    pointer-events: auto !important;
                    overflow: auto !important;
                    padding: 20px !important;
                }
                
                #errorModal.closing {
                    animation: fadeOut 0.2s ease-out forwards;
                }
                
                #errorModal > div {
                    position: relative !important;
                    transform: translateZ(0) !important;
                    backface-visibility: hidden !important;
                    pointer-events: auto !important;
                    will-change: auto !important;
                    max-height: 80vh !important;
                    display: flex !important;
                    flex-direction: column !important;
                    width: 100% !important;
                    max-width: 42rem !important;
                    margin: auto !important;
                }
                
                /* Prevent any movement or flickering */
                #errorModal * {
                    pointer-events: auto;
                }
                
                /* Ensure modal has proper height constraints */
                #errorModal > div {
                    height: auto !important;
                    max-height: 85vh !important;
                }
                
                /* Error scroll container - main scrollable area */
                #errorScrollContainer {
                    scrollbar-width: thin;
                    scrollbar-color: #cbd5e1 #f1f1f1;
                    overflow-y: auto !important;
                    overflow-x: hidden !important;
                    flex: 1 1 0% !important;
                    min-height: 0 !important;
                    max-height: 100% !important;
                    height: 0 !important; /* Force flexbox to calculate height */
                }
                
                #errorScrollContainer::-webkit-scrollbar {
                    width: 10px;
                }
                
                #errorScrollContainer::-webkit-scrollbar-track {
                    background: #f1f5f9;
                    border-radius: 6px;
                    margin: 4px;
                }
                
                #errorScrollContainer::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 6px;
                    border: 2px solid #f1f5f9;
                }
                
                #errorScrollContainer::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                }
                
                /* Prevent flickering on hover */
                #errorModal .hover\:bg-red-50:hover {
                    background-color: #fef2f2;
                    transition: background-color 0.15s ease-in-out;
                }
                
                /* Ensure header and footer are always visible and fixed */
                #errorModal > div > div:first-child,
                #errorModal > div > div:last-child {
                    flex-shrink: 0 !important;
                    position: relative !important;
                }
                
                /* Ensure modal body section is properly constrained */
                #errorModal > div > div:nth-child(2) {
                    flex: 1 1 0% !important;
                    min-height: 0 !important;
                    max-height: 100% !important;
                    overflow: hidden !important;
                    display: flex !important;
                    flex-direction: column !important;
                }
                
                /* Ensure error list wrapper has proper constraints */
                #errorModal > div > div:nth-child(2) > div:last-child {
                    flex: 1 1 0% !important;
                    min-height: 0 !important;
                    max-height: 100% !important;
                    overflow: hidden !important;
                    display: flex !important;
                    flex-direction: column !important;
                }
                
                /* Ensure white container inside has proper height */
                #errorModal > div > div:nth-child(2) > div:last-child > div {
                    height: 100% !important;
                    display: flex !important;
                    flex-direction: column !important;
                    min-height: 0 !important;
                }
            </style>
            
            <script>
                (function() {
                    'use strict';
                    
                    let isScrolling = false;
                    let scrollTimeout = null;
                    
                    function closeErrorModal() {
                        const modal = document.getElementById('errorModal');
                        if (modal) {
                            // Add closing animation
                            modal.classList.add('closing');
                            // Remove modal after animation
                            setTimeout(function() {
                                modal.style.display = 'none';
                                modal.classList.remove('closing');
                                // Clean up event listeners
                                document.removeEventListener('keydown', handleEscapeKey);
                                // Remove modal from DOM
                                modal.remove();
                            }, 200);
                        }
                    }
                    
                    function handleEscapeKey(e) {
                        if (e.key === 'Escape') {
                            closeErrorModal();
                        }
                    }
                    
                    function handleOutsideClick(e) {
                        const modal = document.getElementById('errorModal');
                        if (modal && e.target === modal) {
                            closeErrorModal();
                        }
                    }
                    
                    function updateVisibleCount() {
                        if (isScrolling) return;
                        
                        const scrollContainer = document.getElementById('errorScrollContainer');
                        const visibleCountEl = document.getElementById('visibleCount');
                        
                        if (!scrollContainer || !visibleCountEl) return;
                        
                        const container = scrollContainer;
                        const scrollTop = container.scrollTop;
                        const containerHeight = container.clientHeight;
                        const itemHeight = 60; // Approximate height per error item
                        const visibleItems = Math.ceil((scrollTop + containerHeight) / itemHeight);
                        const totalItems = {{ $totalErrors }};
                        
                        visibleCountEl.textContent = Math.min(Math.max(visibleItems, 1), totalItems);
                    }
                    
                    // Throttled scroll handler
                    function handleScroll() {
                        if (!isScrolling) {
                            isScrolling = true;
                            updateVisibleCount();
                            
                            if (scrollTimeout) {
                                clearTimeout(scrollTimeout);
                            }
                            
                            scrollTimeout = setTimeout(function() {
                                isScrolling = false;
                            }, 100);
                        }
                    }
                    
                    // Initialize modal
                    document.addEventListener('DOMContentLoaded', function() {
                        const modal = document.getElementById('errorModal');
                        if (modal) {
                            // Add event listeners
                            document.addEventListener('keydown', handleEscapeKey);
                            modal.addEventListener('click', handleOutsideClick, { passive: true });
                            
                            const scrollContainer = document.getElementById('errorScrollContainer');
                            if (scrollContainer) {
                                scrollContainer.addEventListener('scroll', handleScroll, { passive: true });
                                updateVisibleCount();
                            }
                        }
                    });
                    
                    // Make closeErrorModal available globally
                    window.closeErrorModal = closeErrorModal;
                })();
            </script>
        @endif

        <form id="bulkDeleteForm" method="POST" action="{{ route('admin.zones.bulk-delete') }}">
            @csrf
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="zone-table min-w-full">
                    <thead>
                        <tr>
                            <th class="w-12">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            </th>
                            <th>ID</th>
                            <th>Pincode</th>
                            <th>Country</th>
                            <th>Zone</th>
                            <th>Network</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Remark</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($zones as $zone)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_ids[]" value="{{ $zone['id'] }}" class="row-checkbox w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500" onchange="updateBulkDeleteBtn()">
                                </td>
                                <td class="font-bold text-gray-900">#{{ $zone['id'] }}</td>
                            <td class="font-semibold text-gray-700">{{ $zone['pincode'] }}</td>
                            <td><span class="badge">{{ $zone['country'] }}</span></td>
                            <td><span class="badge">{{ $zone['zone'] }}</span></td>
                            <td><span class="badge">{{ $zone['network'] }}</span></td>
                            <td><span class="badge">{{ $zone['service'] }}</span></td>
                            <td>
                                <span class="status-badge {{ strtolower($zone['status']) }}">
                                    @if($zone['status'] == 'Active')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ $zone['status'] }}
                                </span>
                            </td>
                            <td class="text-xs text-gray-500 max-w-xs truncate">{{ $zone['remark'] ?: '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.zones.edit', $zone['id']) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button onclick="deleteZone({{ $zone['id'] }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">No Zones Found</h3>
                                    <p class="text-gray-600 mb-4 max-w-md mx-auto">Get started by creating your first zone. Click the button above to add a new zone.</p>
                                    <a href="{{ route('admin.zones.create') }}" class="admin-btn-primary inline-block">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            <span>Create First Zone</span>
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
                alert('Please select at least one zone to delete.');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${checkboxes.length} selected zone(s)?`)) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }

        // Delete zone
        function deleteZone(id) {
            if (confirm('Are you sure you want to delete this zone?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                const deleteUrl = '{{ route("admin.zones.delete", ":id") }}'.replace(':id', id);
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



