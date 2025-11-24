<?php

namespace App\Imports;

use App\Models\AwbUpload;
use App\Models\Network;
use App\Models\Service;
use App\Models\Country;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AwbUploadsImport implements ToModel, WithHeadingRow, WithStartRow, SkipsEmptyRows, WithValidation
{
    /**
     * Start reading from row 2 (row 1 has indicators, row 2 has headers)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Validation rules for the import
     * 
     * @return array
     */
    public function rules(): array
    {
        return [
            // Basic validation rules - detailed validation is done in model() method
            'awb' => 'nullable',
            'awb_no' => 'nullable',
            'networknam' => 'nullable',
            'network_name' => 'nullable',
            'servicename' => 'nullable',
            'service_name' => 'nullable',
            'origin' => 'nullable',
            'destination' => 'nullable',
        ];
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

        // Clean AWB number (trim but preserve special characters)
        $awbNo = trim($awbNo);
        
        // Skip if AWB number is empty after trimming
        if (empty($awbNo)) {
            return null;
        }

        // REAL-TIME duplicate check - Direct database query (no caching)
        // Check if AWB number already exists in database (case-sensitive, exact match)
        // Use fresh query to ensure we're getting the latest data from database
        $existingAwb = AwbUpload::where('awb_no', $awbNo)->first();
        if ($existingAwb) {
            // Log for debugging
            \Log::info("Duplicate AWB check in import - Found existing AWB: ID={$existingAwb->id}, AWB_No={$awbNo}");
            // Throw exception with clear error message instead of silently skipping
            throw new \Exception("AWB No. '{$awbNo}' already exists in the system (ID: {$existingAwb->id}). Duplicate AWB numbers are not allowed. Please use a different AWB number or delete the existing one first.");
        }

        // Get values for validation
        $networkName = $this->getValue($row, ['networknam', 'network_name', 'network name']);
        $serviceName = $this->getValue($row, ['servicename', 'service_name', 'service name']);
        $origin = $this->getValue($row, ['origin']);
        $originZone = $this->getValue($row, ['origin_zone', 'origin zone']);
        $originZonePincode = $this->getValue($row, ['origin_zone_pincode', 'origin_zone_pincode', 'origin zone pincode']);
        $destination = $this->getValue($row, ['destination']);
        $destinationZone = $this->getValue($row, ['destination_zone', 'destination zone']);
        $destinationZonePincode = $this->getValue($row, ['destination_zone_pincode', 'destination zone pincode']);

        // ALL VALIDATIONS MUST PASS - if any fails, skip this row
        
        // Validate network exists (REQUIRED)
        if (empty($networkName)) {
            throw new \Exception("Network name is required for AWB '{$awbNo}'. Please provide a network name.");
        }
        $network = Network::whereRaw('LOWER(name) = LOWER(?)', [$networkName])->where('status', 'Active')->first();
        if (!$network) {
            throw new \Exception("Network '{$networkName}' does not exist or is not active for AWB '{$awbNo}'. Please create the network first.");
        }

        // Validate service exists and belongs to network (REQUIRED)
        if (empty($serviceName)) {
            throw new \Exception("Service name is required for AWB '{$awbNo}'. Please provide a service name.");
        }
        $service = Service::whereRaw('LOWER(name) = LOWER(?)', [$serviceName])
            ->where('status', 'Active')
            ->first();
        if (!$service) {
            throw new \Exception("Service '{$serviceName}' does not exist or is not active for AWB '{$awbNo}'. Please create the service first.");
        }
        // Check if service belongs to network (case-insensitive)
        if (isset($service->network) && strcasecmp(trim($service->network), trim($networkName)) !== 0) {
            throw new \Exception("Service '{$serviceName}' does not belong to network '{$networkName}' for AWB '{$awbNo}'. Service belongs to network '{$service->network}'.");
        }

        // Validate origin country exists (REQUIRED)
        if (empty($origin)) {
            throw new \Exception("Origin country is required for AWB '{$awbNo}'. Please provide an origin country.");
        }
        $originCountry = Country::whereRaw('LOWER(name) = LOWER(?)', [$origin])->where('status', 'Active')->first();
        if (!$originCountry) {
            throw new \Exception("Origin country '{$origin}' does not exist or is not active for AWB '{$awbNo}'. Please create the country first.");
        }

        // Validate destination country exists (REQUIRED)
        if (empty($destination)) {
            throw new \Exception("Destination country is required for AWB '{$awbNo}'. Please provide a destination country.");
        }
        $destinationCountry = Country::whereRaw('LOWER(name) = LOWER(?)', [$destination])->where('status', 'Active')->first();
        if (!$destinationCountry) {
            throw new \Exception("Destination country '{$destination}' does not exist or is not active for AWB '{$awbNo}'. Please create the country first.");
        }

        // Validate origin zone exists (REQUIRED)
        if (empty($originZone)) {
            throw new \Exception("Origin zone is required for AWB '{$awbNo}'. Please provide an origin zone.");
        }
        $originZoneExists = DB::table('zones')
            ->whereRaw('LOWER(country) = LOWER(?)', [$origin])
            ->whereRaw('LOWER(zone) = LOWER(?)', [$originZone])
            ->where('status', 'Active')
            ->exists();
        if (!$originZoneExists) {
            throw new \Exception("Origin zone '{$originZone}' does not exist for country '{$origin}' for AWB '{$awbNo}'. Please create the zone first.");
        }

        // Validate origin zone pincode exists (REQUIRED)
        if (empty($originZonePincode)) {
            throw new \Exception("Origin zone pincode is required for AWB '{$awbNo}'. Please provide an origin zone pincode.");
        }
        $originPincodeExists = DB::table('zones')
            ->whereRaw('LOWER(country) = LOWER(?)', [$origin])
            ->whereRaw('LOWER(zone) = LOWER(?)', [$originZone])
            ->where('pincode', $originZonePincode)
            ->where('status', 'Active')
            ->exists();
        if (!$originPincodeExists) {
            throw new \Exception("Origin zone pincode '{$originZonePincode}' does not exist for zone '{$originZone}' in country '{$origin}' for AWB '{$awbNo}'. Please create the pincode first.");
        }

        // Validate destination zone exists (REQUIRED)
        if (empty($destinationZone)) {
            throw new \Exception("Destination zone is required for AWB '{$awbNo}'. Please provide a destination zone.");
        }
        $destinationZoneExists = DB::table('zones')
            ->whereRaw('LOWER(country) = LOWER(?)', [$destination])
            ->whereRaw('LOWER(zone) = LOWER(?)', [$destinationZone])
            ->where('status', 'Active')
            ->exists();
        if (!$destinationZoneExists) {
            throw new \Exception("Destination zone '{$destinationZone}' does not exist for country '{$destination}' for AWB '{$awbNo}'. Please create the zone first.");
        }

        // Validate destination zone pincode exists (REQUIRED)
        if (empty($destinationZonePincode)) {
            throw new \Exception("Destination zone pincode is required for AWB '{$awbNo}'. Please provide a destination zone pincode.");
        }
        $destinationPincodeExists = DB::table('zones')
            ->whereRaw('LOWER(country) = LOWER(?)', [$destination])
            ->whereRaw('LOWER(zone) = LOWER(?)', [$destinationZone])
            ->where('pincode', $destinationZonePincode)
            ->where('status', 'Active')
            ->exists();
        if (!$destinationPincodeExists) {
            throw new \Exception("Destination zone pincode '{$destinationZonePincode}' does not exist for zone '{$destinationZone}' in country '{$destination}' for AWB '{$awbNo}'. Please create the pincode first.");
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
