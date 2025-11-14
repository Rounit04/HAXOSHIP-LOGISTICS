@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <style>
        /* Dashboard Container */
        .dashboard-container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(255, 117, 15, 0.1);
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #111827 0%, #FF750F 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
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
            box-shadow: 0 12px 40px rgba(255, 117, 15, 0.25);
            transform: translateY(-6px);
        }
        
        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fff5ed 0%, #ffe8d6 100%);
            box-shadow: 0 4px 16px rgba(255, 117, 15, 0.25);
            margin-bottom: 1rem;
        }
        
        .stat-icon svg {
            width: 28px;
            height: 28px;
            color: #FF750F;
        }
        
        .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
        }
        
        /* Section Cards */
        .section-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 1.5rem;
        }
        
        .section-card:hover {
            box-shadow: 0 12px 40px rgba(255, 117, 15, 0.15);
            transform: translateY(-2px);
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f3f4f6;
        }
        
        .section-title svg {
            width: 24px;
            height: 24px;
            color: #FF750F;
        }
        
        /* Metric Items */
        .metric-item {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }
        
        .metric-item:hover {
            border-color: #FF750F;
            background: linear-gradient(135deg, #fff5ed 0%, #ffe8d6 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 117, 15, 0.2);
        }
        
        .metric-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75rem;
        }
        
        .metric-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #FF750F;
        }
        
        /* Total Card */
        .total-card {
            background: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
            border: none;
            border-radius: 16px;
            padding: 28px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 28px rgba(255, 117, 15, 0.35);
            height: 100%;
        }
        
        .total-card:hover {
            box-shadow: 0 12px 40px rgba(255, 117, 15, 0.45);
            transform: translateY(-3px);
        }
        
        .total-card .metric-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 700;
        }
        
        .total-card .metric-value {
            color: white;
            font-size: 2.25rem;
        }
        
        /* Chart Wrapper */
        .chart-wrapper {
            position: relative;
            height: 320px;
            width: 100%;
            background: white;
            border-radius: 14px;
            padding: 20px;
            border: 1px solid #f3f4f6;
        }
        
        .graph-card {
            flex: 1;
            min-width: 0;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f3f4f6;
        }
        
        .chart-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
        }
        
        .chart-controls {
            display: flex;
            gap: 8px;
        }
        
        .chart-control-btn {
            width: 32px;
            height: 32px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: white;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .chart-control-btn:hover {
            background: linear-gradient(135deg, #fff5ed 0%, #ffe8d6 100%);
            border-color: #FF750F;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(255, 117, 15, 0.25);
        }
        
        /* Statement Cards */
        .statement-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }
        
        .statement-item {
            padding: 20px;
            border-radius: 14px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .statement-item.income {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #10b981;
        }
        
        .statement-item.expense {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #ef4444;
        }
        
        .statement-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        
        /* Breadcrumb */
        .breadcrumb {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        
        .breadcrumb a {
            color: #6b7280;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .breadcrumb a:hover {
            color: #FF750F;
        }
        
        /* Date Filter */
        .date-filter-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        @media (max-width: 768px) {
            .stat-value {
                font-size: 2rem;
            }
            
            .section-card {
                padding: 24px;
            }
            
            .chart-wrapper {
                height: 250px;
            }
        }
    </style>

    <div class="dashboard-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Dashboard Overview</h1>
            <nav class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-medium">Haxo Shipping Analytics</span>
            </nav>
            <div class="date-filter-container">
                <input type="date" class="admin-input px-4 py-2.5 text-sm font-medium border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-100" value="{{ date('Y-m-d') }}">
                <button class="admin-btn-primary px-6 py-2.5 text-sm font-semibold shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
            </div>
        </div>

        <!-- Key Metrics Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Shipments Booked -->
            <div class="stat-card">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <p class="stat-label">Total Shipments Booked</p>
                <p class="stat-value">0</p>
                <p class="text-xs text-gray-400 mt-2">All time shipments</p>
            </div>

            <!-- Total Weight -->
            <div class="stat-card">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p class="stat-label">Total Weight</p>
                <p class="stat-value">0 <span class="text-lg font-normal text-gray-400">kg</span></p>
                <p class="text-xs text-gray-400 mt-2">Cumulative weight</p>
            </div>

            <!-- Total Users -->
            <div class="stat-card">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <p class="stat-label">Total Users</p>
                <p class="stat-value">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-400 mt-2">Registered users</p>
            </div>
        </div>

        <!-- Bank Account Balance Section -->
        <div class="section-card">
            <h3 class="section-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Bank Account Balance
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($bankBalances as $bank)
                <div class="metric-item">
                    <p class="metric-label">{{ $bank['name'] }}</p>
                    <p class="metric-value">{{ currency($bank['balance']) }}</p>
                </div>
                @endforeach
                <div class="total-card">
                    <p class="metric-label">Total Balance</p>
                    <p class="metric-value">{{ currency($totalBankBalance ?? 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Network Wallet Balance Section -->
        <div class="section-card">
            <h3 class="section-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Network Wallet Balance
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($networkBalances as $network)
                <div class="metric-item">
                    <p class="metric-label">{{ $network['name'] }}</p>
                    <p class="metric-value">{{ currency($network['balance']) }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Expense Category Wise Section -->
        <div class="section-card">
            <h3 class="section-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Expense Category Wise
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="metric-item">
                    <p class="metric-label">Fuel</p>
                    <p class="metric-value">{{ currency($expenseCategories['Fuel'] ?? 0) }}</p>
                </div>
                <div class="metric-item">
                    <p class="metric-label">Maintenance</p>
                    <p class="metric-value">{{ currency($expenseCategories['Maintenance'] ?? 0) }}</p>
                </div>
                <div class="metric-item">
                    <p class="metric-label">Salaries</p>
                    <p class="metric-value">{{ currency($expenseCategories['Salaries'] ?? 0) }}</p>
                </div>
                <div class="metric-item">
                    <p class="metric-label">Other</p>
                    <p class="metric-value">{{ currency($expenseCategories['Other'] ?? 0) }}</p>
                </div>
                <div class="total-card">
                    <p class="metric-label">Total Expense</p>
                    <p class="metric-value">{{ currency($totalExpense ?? 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Income/Expense and Courier Revenue Graphs Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Income / Expense Graph -->
            <div class="section-card graph-card">
                <div class="chart-header">
                    <h3 class="chart-title">Income / Expense</h3>
                    <div class="chart-controls">
                        <button class="chart-control-btn" title="Zoom In">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                        <button class="chart-control-btn" title="Zoom Out">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <button class="chart-control-btn" title="Reset">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="incomeExpenseChart"></canvas>
                </div>
            </div>

            <!-- Courier Revenue Graph -->
            <div class="section-card graph-card">
                <div class="chart-header">
                    <h3 class="chart-title">Courier Revenue</h3>
                    <div class="chart-controls">
                        <button class="chart-control-btn" title="Zoom In">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                        <button class="chart-control-btn" title="Zoom Out">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <button class="chart-control-btn" title="Reset">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="courierRevenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Statement Sections -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Delivery Man Statements -->
            <div class="statement-card">
                <h3 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Delivery Man Statements
                </h3>
                <div class="space-y-4">
                    <div class="statement-item income">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-green-500 flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 uppercase tracking-wide">Income</span>
                            </div>
                            <span class="text-2xl font-bold text-green-600">{{ currency(0) }}</span>
                        </div>
                    </div>
                    <div class="statement-item expense">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-red-500 flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 uppercase tracking-wide">Expense</span>
                            </div>
                            <span class="text-2xl font-bold text-red-600">{{ currency(0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Merchant Statements -->
            <div class="statement-card">
                <h3 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Merchant Statements
                </h3>
                <div class="space-y-4">
                    <div class="statement-item income">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-green-500 flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 uppercase tracking-wide">Income</span>
                            </div>
                            <span class="text-2xl font-bold text-green-600">{{ currency(0) }}</span>
                        </div>
                    </div>
                    <div class="statement-item expense">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-red-500 flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 uppercase tracking-wide">Expense</span>
                            </div>
                            <span class="text-2xl font-bold text-red-600">{{ currency(0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branch Statements -->
            <div class="statement-card">
                <h3 class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Branch Statements
                </h3>
                <div class="space-y-4">
                    <div class="statement-item income">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-green-500 flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 uppercase tracking-wide">Income</span>
                            </div>
                            <span class="text-2xl font-bold text-green-600">{{ currency(0) }}</span>
                        </div>
                    </div>
                    <div class="statement-item expense">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-red-500 flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 uppercase tracking-wide">Expense</span>
                            </div>
                            <span class="text-2xl font-bold text-red-600">{{ currency(0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Common chart options for line charts
        const lineChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { 
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        font: { size: 12, weight: '600' },
                        boxWidth: 14,
                        boxHeight: 14,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 11 },
                    padding: 12,
                    backgroundColor: 'rgba(0,0,0,0.9)',
                    cornerRadius: 8,
                    displayColors: true,
                    borderColor: '#FF750F',
                    borderWidth: 1
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    max: 5,
                    ticks: { 
                        stepSize: 1,
                        font: { size: 11, weight: '500' },
                        color: '#6B7280'
                    },
                    title: {
                        display: true,
                        text: 'Amount ($)',
                        font: { size: 12, weight: '600' },
                        color: '#6B7280'
                    },
                    grid: { 
                        color: 'rgba(0,0,0,0.08)',
                        drawBorder: false
                    }
                },
                x: { 
                    ticks: { 
                        font: { size: 11, weight: '500' },
                        color: '#6B7280',
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: { 
                        display: false
                    }
                }
            }
        };

        // Dynamic data from server
        const dates = @json($dates);
        const incomeData = @json($incomeData);
        const expenseData = @json($expenseData);

        // Income / Expense Graph
        const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
        const incomeExpenseChart = new Chart(incomeExpenseCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Income',
                        data: incomeData,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Expense',
                        data: expenseData,
                        borderColor: '#EC4899',
                        backgroundColor: 'rgba(236, 72, 153, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointBackgroundColor: '#EC4899',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: lineChartOptions
        });

        // Courier Revenue Graph
        const courierRevenueCtx = document.getElementById('courierRevenueChart').getContext('2d');
        const courierRevenueChart = new Chart(courierRevenueCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Revenue',
                        data: incomeData,
                        borderColor: '#FF750F',
                        backgroundColor: 'rgba(255, 117, 15, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointBackgroundColor: '#FF750F',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                ...lineChartOptions,
                scales: {
                    ...lineChartOptions.scales,
                    y: {
                        ...lineChartOptions.scales.y,
                        title: {
                            display: true,
                            text: 'Revenue ($)',
                            font: { size: 12, weight: '600' },
                            color: '#6B7280'
                        }
                    }
                }
            }
        });

        // Function to update charts dynamically
        window.updateCharts = function(newIncomeData, newExpenseData) {
            incomeExpenseChart.data.datasets[0].data = newIncomeData;
            incomeExpenseChart.data.datasets[1].data = newExpenseData;
            incomeExpenseChart.update();
            
            courierRevenueChart.data.datasets[0].data = newIncomeData;
            courierRevenueChart.update();
        };
    </script>
@endsection



