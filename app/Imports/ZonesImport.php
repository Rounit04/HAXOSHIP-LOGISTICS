<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use App\Models\Network;
use App\Models\Service;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;

class ZonesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public $errors = [];
    public $importedCount = 0;
    public $rowNumber = 1;
    public $validRows = [];

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // Get all networks to validate against
        $dbNetworks = Network::all();
        $networkNames = $dbNetworks->pluck('name')->toArray();
        
        // Fallback to session networks if database is empty
        if (empty($networkNames) && session()->has('networks')) {
            $sessionNetworks = session('networks', []);
            $networkNames = collect($sessionNetworks)->pluck('name')->toArray();
        }

        // Get all services to validate against (from database first, then session)
        $dbServices = Service::all();
        $serviceNames = $dbServices->pluck('name')->toArray();
        
        // Fallback to session services if database is empty
        if (empty($serviceNames) && session()->has('services')) {
            $sessionServices = session('services', []);
            if (is_array($sessionServices)) {
                $serviceNames = collect($sessionServices)->pluck('name')->toArray();
            }
        }

        // STEP 1: Validate ALL rows first (don't import anything yet)
        foreach ($collection as $row) {
            $this->rowNumber++;
            $rowErrors = [];
            
            $pincode = $this->getValue($row, ['pincode', 'pin code', 'pin']);
            $pincode = $pincode ? trim($pincode) : '';
            
            if (empty($pincode)) {
                $rowErrors[] = "Pincode is required";
            } else {
                // Validate pincode format (should be alphanumeric and reasonable length)
                // Minimum 1 character is allowed (already checked by empty() above)
                if (strlen($pincode) > 20) {
                    $rowErrors[] = "Pincode is too long (maximum 20 characters)";
                }
            }

            $network = $this->getValue($row, ['network', 'network_name']);
            $network = $network ? trim($network) : '';
            
            // Validate network exists
            if (empty($network)) {
                $rowErrors[] = "Network is required";
            } elseif (!in_array($network, $networkNames)) {
                $rowErrors[] = "Network '{$network}' does not exist. Please create the network first";
            }

            $service = $this->getValue($row, ['service', 'service_name']);
            $service = $service ? trim($service) : '';
            
            // Validate service exists
            if (empty($service)) {
                $rowErrors[] = "Service is required";
            } elseif (!in_array($service, $serviceNames)) {
                $rowErrors[] = "Service '{$service}' does not exist. Please create the service first";
            }

            $country = $this->getValue($row, ['country', 'country_name']);
            $country = $country ? trim($country) : '';
            
            $zoneName = $this->getValue($row, ['zone', 'zone_name']);
            $zoneName = $zoneName ? trim($zoneName) : '';

            // If there are errors in this row, add them to the errors list
            if (!empty($rowErrors)) {
                $pincodeDisplay = !empty($pincode) ? "'{$pincode}'" : 'Unknown';
                $this->errors[] = "Row {$this->rowNumber} (Pincode: {$pincodeDisplay}): " . implode(', ', $rowErrors);
            } else {
                // Row is valid, store it for import
                $this->validRows[] = [
                    'row_number' => $this->rowNumber,
                    'pincode' => trim($pincode),
                    'country' => $country,
                    'zone' => $zoneName,
                    'network' => trim($network),
                    'service' => trim($service),
                    'status' => $this->getStatus($this->getValue($row, ['status'])),
                    'remark' => $this->getValue($row, ['remark', 'remarks']) ?? '',
                ];
            }
        }

        // STEP 2: Only import if there are NO errors (all-or-nothing approach)
        // If any errors were found in STEP 1, skip import entirely
        // This ensures errors are shown first, and import only happens when all data is valid
        if (empty($this->errors)) {
            // Use database transaction to ensure all-or-nothing import
            DB::beginTransaction();
            try {
                foreach ($this->validRows as $validRow) {
                    // Always create new zone records - allow duplicates
                    // This ensures all rows from the import file are stored in the database
                    Zone::create([
                        'pincode' => $validRow['pincode'],
                        'country' => $validRow['country'],
                        'zone' => $validRow['zone'],
                        'network' => $validRow['network'],
                        'service' => $validRow['service'],
                        'status' => $validRow['status'],
                        'remark' => $validRow['remark'],
                    ]);
                    $this->importedCount++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Database error during import: " . $e->getMessage();
            }
        }
        // If errors exist, import is skipped and errors will be displayed to user
    }

    private function getValue($row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key])) {
                return $row[$key];
            }
            foreach ($row as $rowKey => $value) {
                if (strcasecmp(trim($rowKey), trim($key)) === 0) {
                    return $value;
                }
            }
        }
        return null;
    }

    private function getStatus($value)
    {
        if (empty($value)) return 'Active';
        $status = strtolower(trim($value));
        if (in_array($status, ['active', '1', 'yes', 'true'])) {
            return 'Active';
        }
        return 'Inactive';
    }
}
