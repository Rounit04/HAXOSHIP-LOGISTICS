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
            overflow: hidden;
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
        }
        .search-input-group {
            flex: 1;
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

        <form method="POST" action="{{ route('admin.search-with-awb.search.submit') }}" class="search-form">
            @csrf
            <div class="search-input-group">
                <label class="form-label">
                    AWB Number <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    name="awb_number" 
                    class="form-input" 
                    placeholder="Search AWB" 
                    value="{{ old('awb_number', $awbNumber ?? '') }}"
                    required
                    autofocus
                >
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
                    @if(!empty($awb['destination']))
                    <div class="detail-row">
                        <div class="detail-label">Destination</div>
                        <div class="detail-value"><span class="badge">{{ $awb['destination'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['consignee_name']))
                    <div class="detail-row">
                        <div class="detail-label">Consignee Name</div>
                        <div class="detail-value">{{ $awb['consignee_name'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['origin_pin']))
                    <div class="detail-row">
                        <div class="detail-label">Origin Pin</div>
                        <div class="detail-value"><span class="badge">{{ $awb['origin_pin'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['destination_pin']))
                    <div class="detail-row">
                        <div class="detail-label">Destination Pin</div>
                        <div class="detail-value"><span class="badge">{{ $awb['destination_pin'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['pieces']))
                    <div class="detail-row">
                        <div class="detail-label">Pieces</div>
                        <div class="detail-value">{{ $awb['pieces'] }}</div>
                    </div>
                    @endif
                    @if(!empty($awb['weight']))
                    <div class="detail-row">
                        <div class="detail-label">Weight (KG)</div>
                        <div class="detail-value">{{ number_format($awb['weight'], 2) }} KG</div>
                    </div>
                    @endif
                    @if(!empty($awb['vel_weight']))
                    <div class="detail-row">
                        <div class="detail-label">Volumetric Weight (KG)</div>
                        <div class="detail-value">{{ number_format($awb['vel_weight'], 2) }} KG</div>
                    </div>
                    @endif
                    @if(!empty($awb['chr_weight']))
                    <div class="detail-row">
                        <div class="detail-label">Chargeable Weight (KG)</div>
                        <div class="detail-value">{{ number_format($awb['chr_weight'], 2) }} KG</div>
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
                    @if(!empty($awb['network']))
                    <div class="detail-row">
                        <div class="detail-label">Network</div>
                        <div class="detail-value"><span class="badge">{{ $awb['network'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($awb['service']))
                    <div class="detail-row">
                        <div class="detail-label">Service</div>
                        <div class="detail-value"><span class="badge">{{ $awb['service'] }}</span></div>
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
@endsection
