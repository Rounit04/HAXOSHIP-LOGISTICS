<?php

namespace App\Imports;

use App\Models\AwbUpload;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Carbon\Carbon;

class AwbUploadsImport implements ToModel, WithHeadingRow, WithStartRow, SkipsEmptyRows
{
    /**
     * Start reading from row 2 (row 1 has indicators, row 2 has headers)
     */
    public function startRow(): int
    {
        return 2;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Helper function to parse date
        $parseDate = function($date) {
            if (empty($date)) return null;
            try {
                // Try to parse DD-MM-YYYY format
                if (is_string($date) && strpos($date, '-') !== false) {
                    $parts = explode('-', $date);
                    if (count($parts) === 3) {
                        return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                    }
                }
                // Try Excel date serial number
                if (is_numeric($date)) {
                    return Carbon::createFromTimestamp((($date - 25569) * 86400))->format('Y-m-d');
                }
                // Try standard format
                return Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        // Helper function to clean numeric values
        $cleanNumeric = function($value) {
            if (empty($value)) return null;
            if (is_numeric($value)) return (float)$value;
            // Remove non-numeric characters except decimal point
            $cleaned = preg_replace('/[^0-9.]/', '', (string)$value);
            return $cleaned !== '' ? (float)$cleaned : null;
        };

        // Map Excel columns to database fields
        // Excel headers might be: Hub, Branch, AWB, type, Origin, origin_zone, etc.
        // Try multiple variations of AWB column names (case-insensitive, with/without spaces, dots, etc.)
        $awbNo = $this->getValue($row, [
            'awb', 
            'awb_no', 
            'awb_no.', 
            'awb_no_', 
            'awb number', 
            'awbnumber',
            'awb.',
            'awb_',
            // Also check all row keys for AWB-related names
        ]);
        
        // If still not found, try to find any column containing 'awb' (case-insensitive)
        if (empty($awbNo)) {
            foreach ($row as $key => $value) {
                if (is_string($key) && stripos($key, 'awb') !== false && !empty($value)) {
                    $awbNo = $value;
                    break;
                }
            }
        }
        
        if (empty($awbNo)) {
            return null; // Skip rows without AWB number
        }

        // Clean AWB number (remove special characters)
        $awbNo = preg_replace('/[^a-zA-Z0-9]/', '', $awbNo);

        // Check if AWB already exists
        if (AwbUpload::where('awb_no', $awbNo)->exists()) {
            return null; // Skip duplicate
        }

        return new AwbUpload([
            'hub' => $this->getValue($row, ['hub']),
            'branch' => $this->getValue($row, ['branch']),
            'awb_no' => $awbNo,
            'type' => strtolower($this->getValue($row, ['type'])),
            'origin' => $this->getValue($row, ['origin']),
            'origin_zone' => $this->getValue($row, ['origin_zone', 'origin zone']),
            'origin_zone_pincode' => $this->getValue($row, ['origin_zone_pincode', 'origin_zone_pincode', 'origin zone pincode']),
            'destination' => $this->getValue($row, ['destination']),
            'destination_zone' => $this->getValue($row, ['destination_zone', 'destination zone']),
            'destination_zone_pincode' => $this->getValue($row, ['destination_zone_pincode', 'destination zone pincode']),
            'reference_no' => $this->getValue($row, ['reference_no', 'reference no', 'reference_no.']),
            'date_of_sale' => $parseDate($this->getValue($row, ['date', 'date_of_sale', 'date of sale'])),
            'invoice_date' => $parseDate($this->getValue($row, ['invoice_date', 'invoice date'])),
            'non_commercial' => $this->getValue($row, ['non_commercial', 'non-commercial', 'non commercial']),
            'consignor' => $this->getValue($row, ['consignor']),
            'consignor_attn' => $this->getValue($row, ['consignor_attn', 'consignor attn', 'consignor_attn.']),
            'consignee' => $this->getValue($row, ['consignee', 'consignee name']),
            'consignee_attn' => $this->getValue($row, ['consignee_attn', 'consignee attn', 'consignee_attn.']),
            'goods_type' => $this->getValue($row, ['goods_type', 'goods type']),
            'pk' => (int)($this->getValue($row, ['pk']) ?? 1),
            'actual_weight' => $cleanNumeric($this->getValue($row, ['actual_wt.', 'actual_wt', 'actual wt.', 'actual weight'])),
            'volumetric_weight' => $cleanNumeric($this->getValue($row, ['volumetric_wt.', 'volumetric_wt', 'volumetric wt.', 'volumetric weight'])),
            // Chargeable weight can contain text (e.g., "0.22 D1", "10.65 Sport Rate")
            'chargeable_weight' => $this->getValue($row, ['chargeable_wt.', 'chargeable_wt', 'chargeable wt.', 'chargeable weight']) ?? null,
            'network_name' => $this->getValue($row, ['networknam', 'network_name', 'network name']),
            'service_name' => $this->getValue($row, ['servicename', 'service_name', 'service name']),
            'amour' => $cleanNumeric($this->getValue($row, ['amour'])),
            'medical_shipment' => $this->getValue($row, ['medical_shipmen', 'medical_shipment', 'medical shipment']),
            'invoice_value' => $cleanNumeric($this->getValue($row, ['invoice_valu', 'invoice_value', 'invoice value'])),
            'is_coc' => (bool)($this->getValue($row, ['is_coc']) ?? false),
            'cod_amount' => $cleanNumeric($this->getValue($row, ['cod_amoun', 'cod_amount', 'cod amount'])) ?? 0,
            'clearance_required' => $this->getValue($row, ['clearance_required', 'clearance required']),
            'remark' => $this->getValue($row, ['remark']),
            'status' => strtolower($this->getValue($row, ['status']) ?? 'publish'),
            'payment_deduct' => $this->getValue($row, ['payment_deduct', 'payment deduct']),
            'location' => $this->getValue($row, ['location']),
            'forwarding_service' => $this->getValue($row, ['forwardser', 'forwarding_service', 'forwarding service']),
            'forwarding_number' => $this->getValue($row, ['forwardnumber', 'forwarding_number', 'forwarding number']),
            'transfer' => $this->getValue($row, ['transfe', 'transfer']),
            'transfer_on' => $this->getValue($row, ['transferon', 'transfer_on', 'transfer on']),
            'remark_1' => $this->getValue($row, ['remark_1', 'remark 1']),
            'remark_2' => $this->getValue($row, ['remark_2', 'remark 2']),
            'remark_3' => $this->getValue($row, ['remark_3', 'remark 3']),
            'remark_4' => $this->getValue($row, ['remark_4', 'remark 4']),
            'remark_5' => $this->getValue($row, ['remark_5', 'remark 5']),
            'remark_6' => $this->getValue($row, ['remark_6', 'remark 6']),
            'remark_7' => $this->getValue($row, ['remark_7', 'remark 7']),
            'display_service_name' => $this->getValue($row, ['display_service_name', 'display service name']),
            'operation_remark' => $this->getValue($row, ['operation_remark', 'operation remark']),
            // Note: date_of_sale, goods_type, transfer, transfer_on are already mapped above
            // These are backend-only fields (marked with 0 in Excel row 1) but still saved during bulk upload
        ]);
    }

    /**
     * Get value from row with multiple possible key variations
     * Handles Laravel Excel's header normalization (lowercase, spaces to underscores, etc.)
     */
    private function getValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            // Normalize the key (lowercase, replace spaces with underscores, remove special chars)
            $normalizedKey = strtolower(str_replace([' ', '.', '-'], '_', trim($key)));
            $normalizedKey = preg_replace('/[^a-z0-9_]/', '', $normalizedKey);
            $normalizedKey = trim($normalizedKey, '_');
            
            // Try exact match first
            if (isset($row[$key])) {
                return $row[$key];
            }
            
            // Try normalized key match
            if (isset($row[$normalizedKey])) {
                return $row[$normalizedKey];
            }
            
            // Try case-insensitive match
            foreach ($row as $rowKey => $value) {
                // Normalize row key for comparison
                $normalizedRowKey = strtolower(str_replace([' ', '.', '-'], '_', trim((string)$rowKey)));
                $normalizedRowKey = preg_replace('/[^a-z0-9_]/', '', $normalizedRowKey);
                $normalizedRowKey = trim($normalizedRowKey, '_');
                
                if (strcasecmp($rowKey, $key) === 0 || $normalizedRowKey === $normalizedKey) {
                    return $value;
                }
            }
        }
        return null;
    }

}
