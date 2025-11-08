<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwbUpload extends Model
{
    protected $fillable = [
        'hub', 'branch', 'awb_no', 'type', 'origin', 'origin_zone', 'origin_zone_pincode',
        'destination', 'destination_zone', 'destination_zone_pincode', 'reference_no',
        'date_of_sale', 'invoice_date', 'non_commercial', 'consignor', 'consignor_attn',
        'consignee', 'consignee_attn', 'goods_type', 'pk', 'actual_weight', 'volumetric_weight',
        'chargeable_weight', 'network_name', 'service_name', 'amour', 'medical_shipment',
        'invoice_value', 'is_coc', 'cod_amount', 'clearance_required', 'remark', 'status',
        'payment_deduct', 'location', 'forwarding_service', 'forwarding_number', 'transfer',
        'transfer_on', 'remark_1', 'remark_2', 'booking_type', 'shipment_type',
        'display_service_name', 'operation_remark', 'remark_3', 'remark_4', 'remark_5',
        'remark_6', 'remark_7',
    ];

    protected $casts = [
        'date_of_sale' => 'date',
        'invoice_date' => 'date',
        'actual_weight' => 'decimal:2',
        'volumetric_weight' => 'decimal:2',
        'chargeable_weight' => 'decimal:2',
        'amour' => 'decimal:2',
        'invoice_value' => 'decimal:2',
        'cod_amount' => 'decimal:2',
        'is_coc' => 'boolean',
        'pk' => 'integer',
    ];
}
