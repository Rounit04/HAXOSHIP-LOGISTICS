@extends('layouts.admin')

@section('title', 'Search AWB')

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
        .search-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: visible !important;
            position: relative;
        }
        .search-card > * {
            overflow: visible;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .form-label .required {
            color: #ef4444;
            font-size: 12px;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            background: white;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #FF750F;
            box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
            background: #fff5ed;
        }
        .search-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
            position: relative;
            z-index: 1;
        }
        .search-input-group {
            flex: 1;
            position: relative;
            z-index: 10;
        }
        .awb-details-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
            margin-top: 20px;
        }
        .detail-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
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
        .amount-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
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
        .awb-input-wrapper {
            position: relative;
            width: 100%;
            z-index: 10;
        }
        .awb-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            width: 100%;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-top: 0;
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            z-index: 9999;
            animation: slideDown 0.2s ease-out;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .awb-dropdown-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .awb-dropdown-item:hover {
            background: #fff5ed;
            border-left: 3px solid #FF750F;
        }
        .awb-dropdown-item:last-child {
            border-bottom: none;
        }
        .awb-dropdown-item .awb-number {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }
        .awb-dropdown-item .awb-info {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .awb-dropdown-item .awb-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .awb-dropdown-empty {
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .detail-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            .search-form {
                flex-direction: column;
            }
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Search AWB</h1>
                    <p class="text-xs text-gray-600">Search with AWB - Info</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="search-card p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            AWB Info
        </h2>

        <form method="POST" action="{{ route('admin.search-with-awb.search.submit') }}" class="search-form" id="searchAWBForm">
            @csrf
            <div class="search-input-group">
                <label class="form-label">
                    AWB Number <span class="required">*</span>
                </label>
                <div class="awb-input-wrapper">
                    <input 
                        type="text" 
                        name="awb_number" 
                        id="awb_number_input"
                        class="form-input" 
                        placeholder="Type or select AWB number" 
                        value="{{ old('awb_number', $awbNumber ?? '') }}"
                        required
                        autofocus
                        autocomplete="off"
                        list="awb_numbers_list"
                    >
                    <datalist id="awb_numbers_list">
                        <!-- Options will be populated by JavaScript -->
                    </datalist>
                    <div id="awb_dropdown" class="awb-dropdown" style="display: none;">
                        <!-- Dropdown items will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            <button type="submit" class="admin-btn-primary px-6 py-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Search</span>
                </div>
            </button>
        </form>
    </div>

    <!-- AWB Details -->
    @if(!empty($awbNumber))
        @if($awb)
            <div class="awb-details-card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    AWB Details
                </h2>

                <div class="space-y-0">
                    <div class="detail-row">
                        <div class="detail-label">AWB ID</div>
                        <div class="detail-value">#{{ $awb['id'] }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">AWB Number</div>
                        <div class="detail-value">
                            <span class="text-orange-600 font-bold">{{ $awb['awb_no'] }}</span>
                        </div>
                    </div>
                    @if(!empty($awb['date_of_sale']))
                    <div class="detail-row">
                        <div class="detail-label">Date of Sale</div>
                        <div class="detail-value">{{ $awb['date_of_sale'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['branch']))
                    <div class="detail-row">
                        <div class="detail-label">Branch</div>
                        <div class="detail-value"><span class="badge">{{ $awb['branch'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['status']))
                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="badge" style="background: {{ $awb['status'] == 'Active' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $awb['status'] == 'Active' ? '#059669' : '#dc2626' }};">
                                {{ $awb['status'] }}
                            </span>
                        </div>
                    </div>
                    @endif
                    @if(!empty($awb['booking_type']))
                    <div class="detail-row">
                        <div class="detail-label">Booking Type</div>
                        <div class="detail-value"><span class="badge">{{ $awb['booking_type'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['shipment_type']))
                    <div class="detail-row">
                        <div class="detail-label">Shipment Type</div>
                        <div class="detail-value"><span class="badge">{{ $awb['shipment_type'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['origin']))
                    <div class="detail-row">
                        <div class="detail-label">Origin</div>
                        <div class="detail-value"><span class="badge">{{ $awb['origin'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['destination']))
                    <div class="detail-row">
                        <div class="detail-label">Destination</div>
                        <div class="detail-value"><span class="badge">{{ $awb['destination'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['consignee']) || !empty($awb['consignee_name']))
                    <div class="detail-row">
                        <div class="detail-label">Consignee</div>
                        <div class="detail-value">{{ $awb['consignee'] ?? $awb['consignee_name'] ?? 'N/A' }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['consignor']) || !empty($awb['consignor_name']))
                    <div class="detail-row">
                        <div class="detail-label">Consignor</div>
                        <div class="detail-value">{{ $awb['consignor'] ?? $awb['consignor_name'] ?? 'N/A' }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['origin_zone_pincode']) || !empty($awb['origin_pin']))
                    <div class="detail-row">
                        <div class="detail-label">Origin Pincode</div>
                        <div class="detail-value"><span class="badge">{{ $awb['origin_zone_pincode'] ?? $awb['origin_pin'] ?? 'N/A' }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['destination_zone_pincode']) || !empty($awb['destination_pin']))
                    <div class="detail-row">
                        <div class="detail-label">Destination Pincode</div>
                        <div class="detail-value"><span class="badge">{{ $awb['destination_zone_pincode'] ?? $awb['destination_pin'] ?? 'N/A' }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['origin_zone']))
                    <div class="detail-row">
                        <div class="detail-label">Origin Zone</div>
                        <div class="detail-value"><span class="badge">{{ $awb['origin_zone'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['destination_zone']))
                    <div class="detail-row">
                        <div class="detail-label">Destination Zone</div>
                        <div class="detail-value"><span class="badge">{{ $awb['destination_zone'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['pk']) || !empty($awb['pieces']))
                    <div class="detail-row">
                        <div class="detail-label">Pieces</div>
                        <div class="detail-value">{{ $awb['pk'] ?? $awb['pieces'] ?? 'N/A' }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['actual_weight']) || !empty($awb['weight']))
                    <div class="detail-row">
                        <div class="detail-label">Actual Weight (KG)</div>
                        <div class="detail-value">{{ number_format($awb['actual_weight'] ?? $awb['weight'] ?? 0, 2) }} KG</div>
                    </div>
                    @endif
                    @if(!empty($awb['volumetric_weight']) || !empty($awb['vel_weight']))
                    <div class="detail-row">
                        <div class="detail-label">Volumetric Weight (KG)</div>
                        <div class="detail-value">{{ number_format($awb['volumetric_weight'] ?? $awb['vel_weight'] ?? 0, 2) }} KG</div>
                    </div>
                    @endif
                    @if(!empty($awb['chargeable_weight']) || !empty($awb['chr_weight']))
                    <div class="detail-row">
                        <div class="detail-label">Chargeable Weight (KG)</div>
                        <div class="detail-value">{{ number_format($awb['chargeable_weight'] ?? $awb['chr_weight'] ?? 0, 2) }} KG</div>
                    </div>
                    @endif
                    @if(!empty($awb['amour']))
                    <div class="detail-row">
                        <div class="detail-label">Amount</div>
                        <div class="detail-value">
                            <span class="amount-badge">₹{{ number_format($awb['amour'], 2) }}</span>
                        </div>
                    </div>
                    @endif
                    @if(!empty($awb['clearance']))
                    <div class="detail-row">
                        <div class="detail-label">Clearance</div>
                        <div class="detail-value">{{ $awb['clearance'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['operation_remark']))
                    <div class="detail-row">
                        <div class="detail-label">Operation Remark</div>
                        <div class="detail-value">{{ $awb['operation_remark'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['network_name']) || !empty($awb['network']))
                    <div class="detail-row">
                        <div class="detail-label">Network</div>
                        <div class="detail-value"><span class="badge">{{ $awb['network_name'] ?? $awb['network'] ?? 'N/A' }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['service_name']) || !empty($awb['service']))
                    <div class="detail-row">
                        <div class="detail-label">Service</div>
                        <div class="detail-value"><span class="badge">{{ $awb['service_name'] ?? $awb['service'] ?? 'N/A' }}</span></div>
                    </div>
                    @endif
                    @if(!empty($zoneInfo))
                        @if(!empty($zoneInfo['origin']))
                        <div class="detail-row">
                            <div class="detail-label">Origin Zone</div>
                            <div class="detail-value">
                                <span class="badge">{{ $zoneInfo['origin']['zone'] ?? 'N/A' }}</span>
                                @if(!empty($zoneInfo['origin']['country']))
                                    <span class="text-gray-500 text-xs ml-2">({{ $zoneInfo['origin']['country'] }})</span>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if(!empty($zoneInfo['destination']))
                        <div class="detail-row">
                            <div class="detail-label">Destination Zone</div>
                            <div class="detail-value">
                                <span class="badge">{{ $zoneInfo['destination']['zone'] ?? 'N/A' }}</span>
                                @if(!empty($zoneInfo['destination']['country']))
                                    <span class="text-gray-500 text-xs ml-2">({{ $zoneInfo['destination']['country'] }})</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endif
                    @if(!empty($networkTransactions) && count($networkTransactions) > 0)
                    <div class="detail-row">
                        <div class="detail-label">Total Transaction Amount</div>
                        <div class="detail-value">
                            <span class="amount-badge">₹{{ number_format($totalAmount, 2) }}</span>
                        </div>
                    </div>
                    @endif
                    @if($supportTicketCount > 0)
                    <div class="detail-row">
                        <div class="detail-label">Support Tickets/Queries</div>
                        <div class="detail-value">
                            <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #dc2626;">
                                {{ $supportTicketCount }} {{ $supportTicketCount == 1 ? 'Query' : 'Queries' }}
                            </span>
                        </div>
                    </div>
                    @endif
                    @if(!empty($awb['display_service_name']))
                    <div class="detail-row">
                        <div class="detail-label">Display Service Name</div>
                        <div class="detail-value">{{ $awb['display_service_name'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['remark_1']))
                    <div class="detail-row">
                        <div class="detail-label">Remark 1</div>
                        <div class="detail-value">{{ $awb['remark_1'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['remark_2']))
                    <div class="detail-row">
                        <div class="detail-label">Remark 2</div>
                        <div class="detail-value">{{ $awb['remark_2'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['remark_3']))
                    <div class="detail-row">
                        <div class="detail-label">Remark 3</div>
                        <div class="detail-value">{{ $awb['remark_3'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['remark_4']))
                    <div class="detail-row">
                        <div class="detail-label">Remark 4</div>
                        <div class="detail-value">{{ $awb['remark_4'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['remark_5']))
                    <div class="detail-row">
                        <div class="detail-label">Remark 5</div>
                        <div class="detail-value">{{ $awb['remark_5'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['remark_6']))
                    <div class="detail-row">
                        <div class="detail-label">Remark 6</div>
                        <div class="detail-value">{{ $awb['remark_6'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['remark_7']))
                    <div class="detail-row">
                        <div class="detail-label">Remark 7</div>
                        <div class="detail-value">{{ $awb['remark_7'] }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Network Transactions Section -->
            @if(!empty($networkTransactions) && count($networkTransactions) > 0)
            <div class="awb-details-card p-6 mt-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Network Transactions (Amounts)
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Network</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Transaction Type</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance After</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($networkTransactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y, h:i A') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="badge">{{ $transaction['network_name'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="badge" style="background: {{ $transaction['type'] == 'credit' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $transaction['type'] == 'credit' ? '#059669' : '#dc2626' }};">
                                        {{ ucfirst($transaction['type']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $transaction['transaction_type'])) }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-right {{ $transaction['type'] == 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction['type'] == 'credit' ? '+' : '-' }}₹{{ number_format($transaction['amount'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                    ₹{{ number_format($transaction['balance_after'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $transaction['description'] ?? 'N/A' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">Total Amount:</td>
                                <td class="px-4 py-3 text-sm font-bold text-right text-orange-600">
                                    ₹{{ number_format($totalAmount, 2) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            <!-- Support Tickets Section -->
            @if($supportTicketCount > 0)
            <div class="awb-details-card p-6 mt-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Support Tickets/Queries
                </h2>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold text-orange-600">{{ $supportTicketCount }}</span> 
                        {{ $supportTicketCount == 1 ? 'query has been' : 'queries have been' }} raised against this AWB number.
                    </p>
                </div>
            </div>
            @endif
        @else
            <div class="awb-details-card p-6">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">AWB Not Found</h3>
                    <p class="text-gray-600 mb-4 max-w-md mx-auto">No AWB found with the number "<strong>{{ $awbNumber }}</strong>". Please check the AWB number and try again.</p>
                </div>
            </div>
        @endif
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const awbInput = document.getElementById('awb_number_input');
            const awbDropdown = document.getElementById('awb_dropdown');
            const datalist = document.getElementById('awb_numbers_list');
            let searchTimeout;
            let allAwbNumbers = [];

            // Fetch AWB numbers on page load
            fetchAwbNumbers('');

            // Fetch AWB numbers from API
            function fetchAwbNumbers(search = '') {
                const url = '{{ route("admin.search-with-awb.awb-numbers") }}' + (search ? '?search=' + encodeURIComponent(search) : '');
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        allAwbNumbers = data;
                        populateDatalist(data);
                        if (awbInput.value) {
                            showDropdown(data);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching AWB numbers:', error);
                    });
            }

            // Populate datalist
            function populateDatalist(awbNumbers) {
                datalist.innerHTML = '';
                awbNumbers.forEach(awb => {
                    const option = document.createElement('option');
                    option.value = awb.awb_no;
                    option.textContent = awb.awb_no;
                    datalist.appendChild(option);
                });
            }

            // Show dropdown with AWB numbers
            function showDropdown(awbNumbers) {
                if (awbNumbers.length === 0) {
                    awbDropdown.innerHTML = '<div class="awb-dropdown-empty">No AWB numbers found</div>';
                    awbDropdown.style.display = 'block';
                    return;
                }

                awbDropdown.innerHTML = '';
                awbNumbers.slice(0, 20).forEach(awb => {
                    const item = document.createElement('div');
                    item.className = 'awb-dropdown-item';
                    item.innerHTML = `
                        <div>
                            <div class="awb-number">${awb.awb_no}</div>
                            <div class="awb-info">
                                ${awb.branch ? `<span>Branch: ${awb.branch}</span>` : ''}
                                ${awb.date_of_sale ? `<span>• ${awb.date_of_sale}</span>` : ''}
                            </div>
                        </div>
                        ${awb.status ? `<span class="awb-badge" style="background: ${awb.status === 'Active' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'}; color: ${awb.status === 'Active' ? '#059669' : '#dc2626'};">${awb.status}</span>` : ''}
                    `;
                    item.addEventListener('click', function() {
                        awbInput.value = awb.awb_no;
                        awbDropdown.style.display = 'none';
                        awbInput.focus();
                    });
                    awbDropdown.appendChild(item);
                });
                awbDropdown.style.display = 'block';
            }

            // Hide dropdown
            function hideDropdown() {
                awbDropdown.style.display = 'none';
            }

            // Handle input events
            awbInput.addEventListener('input', function() {
                const value = this.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (value.length === 0) {
                    hideDropdown();
                    return;
                }

                // Debounce search
                searchTimeout = setTimeout(() => {
                    const filtered = allAwbNumbers.filter(awb => 
                        awb.awb_no.toLowerCase().includes(value.toLowerCase()) ||
                        (awb.branch && awb.branch.toLowerCase().includes(value.toLowerCase()))
                    );
                    showDropdown(filtered);
                }, 300);
            });

            // Show dropdown on focus if there's a value
            awbInput.addEventListener('focus', function() {
                if (this.value.trim()) {
                    const value = this.value.trim();
                    const filtered = allAwbNumbers.filter(awb => 
                        awb.awb_no.toLowerCase().includes(value.toLowerCase()) ||
                        (awb.branch && awb.branch.toLowerCase().includes(value.toLowerCase()))
                    );
                    showDropdown(filtered);
                } else {
                    showDropdown(allAwbNumbers.slice(0, 20));
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const inputWrapper = awbInput.closest('.awb-input-wrapper');
                if (!inputWrapper.contains(e.target)) {
                    hideDropdown();
                }
            });

            // Handle keyboard navigation
            awbInput.addEventListener('keydown', function(e) {
                const items = awbDropdown.querySelectorAll('.awb-dropdown-item');
                const currentIndex = Array.from(items).findIndex(item => item.classList.contains('highlighted'));
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
                    items.forEach(item => item.classList.remove('highlighted'));
                    if (items[nextIndex]) {
                        items[nextIndex].classList.add('highlighted');
                        items[nextIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
                    items.forEach(item => item.classList.remove('highlighted'));
                    if (items[prevIndex]) {
                        items[prevIndex].classList.add('highlighted');
                        items[prevIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'Enter' && currentIndex >= 0) {
                    e.preventDefault();
                    items[currentIndex].click();
                } else if (e.key === 'Escape') {
                    hideDropdown();
                }
            });

            // Add highlight style
            const style = document.createElement('style');
            style.textContent = `
                .awb-dropdown-item.highlighted {
                    background: #fff5ed;
                    border-left: 3px solid #FF750F;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
@endsection
