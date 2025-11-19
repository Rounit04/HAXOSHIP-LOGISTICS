<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use App\Models\Network;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ServicesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
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

        // STEP 1: Validate ALL rows first (don't import anything yet)
        foreach ($collection as $row) {
            $this->rowNumber++;
            $rowErrors = [];
            
            $serviceName = $this->getValue($row, ['service_name', 'name', 'service']);
            $serviceName = $serviceName ? trim($serviceName) : '';
            
            if (empty($serviceName)) {
                $rowErrors[] = "Service name is required";
            } else {
                // Validate service name doesn't contain invalid keywords
                $invalidKeywords = ['wrong', 'error', 'invalid', 'test wrong', 'wrong test'];
                $serviceNameLower = strtolower($serviceName);
                foreach ($invalidKeywords as $keyword) {
                    if (strpos($serviceNameLower, $keyword) !== false) {
                        $rowErrors[] = "Service name contains invalid keyword '{$keyword}'. Please use a valid service name";
                        break;
                    }
                }
                
                // Validate service name format (should not be empty after trimming)
                if (strlen($serviceName) < 2) {
                    $rowErrors[] = "Service name is too short (minimum 2 characters)";
                }
                
                // Validate service name doesn't have only spaces or special characters
                if (preg_match('/^[\s\-_]+$/', $serviceName)) {
                    $rowErrors[] = "Service name contains only invalid characters";
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

            // Additional validation: Check for suspicious patterns in service name
            if (!empty($serviceName) && !empty($network)) {
                // Check for common data entry errors
                if (strlen($serviceName) > 100) {
                    $rowErrors[] = "Service name is too long (maximum 100 characters)";
                }
            }

            // Validate required fields
            $transitTime = $this->getValue($row, ['transit_time', 'transit time']);
            $transitTime = $transitTime ? trim($transitTime) : '';
            if (empty($transitTime)) {
                $rowErrors[] = "Transit time is required";
            }

            $itemsAllowed = $this->getValue($row, ['items_allowed', 'items allowed']);
            $itemsAllowed = $itemsAllowed ? trim($itemsAllowed) : '';
            if (empty($itemsAllowed)) {
                $rowErrors[] = "Items allowed is required";
            }

            // Check if service already exists in database (same name AND same network)
            if (!empty($serviceName) && !empty($network) && empty($rowErrors)) {
                $exists = Service::whereRaw('LOWER(name) = LOWER(?)', [$serviceName])
                    ->whereRaw('LOWER(network) = LOWER(?)', [$network])
                    ->first();

                if ($exists) {
                    $rowErrors[] = "Service '{$serviceName}' with network '{$network}' already exists (ID: {$exists->id})";
                }
            }

            // If there are errors in this row, add them to the errors list
            if (!empty($rowErrors)) {
                $serviceNameDisplay = !empty($serviceName) ? "'{$serviceName}'" : 'Unknown';
                $this->errors[] = "Row {$this->rowNumber} ({$serviceNameDisplay}): " . implode(', ', $rowErrors);
            } else {
                // Row is valid, store it for import
                $this->validRows[] = [
                    'row_number' => $this->rowNumber,
                    'service_name' => trim($serviceName),
                    'network' => trim($network),
                    'transit_time' => $this->getValue($row, ['transit_time', 'transit time']) ?? '',
                    'items_allowed' => $this->getValue($row, ['items_allowed', 'items allowed']) ?? '',
                    'status' => $this->getStatus($this->getValue($row, ['status'])),
                    'remark' => $this->getValue($row, ['remark', 'remarks']) ?? '',
                    'display_title' => $this->getValue($row, ['display_title', 'display title']) ?? $serviceName,
                    'description' => $this->getValue($row, ['description']) ?? '',
                    'icon_type' => $this->getValue($row, ['icon_type', 'icon type']) ?? 'truck',
                    'is_highlighted' => $this->getBoolean($this->getValue($row, ['is_highlighted', 'is highlighted', 'highlighted'])),
                ];
            }
        }

        // STEP 2: Only import if there are NO errors (all-or-nothing)
        if (empty($this->errors)) {
            // Use database transaction to ensure all-or-nothing import
            DB::beginTransaction();
            try {
                foreach ($this->validRows as $validRow) {
                    Service::create([
                        'name' => $validRow['service_name'],
                        'network' => $validRow['network'],
                        'transit_time' => $validRow['transit_time'],
                        'items_allowed' => $validRow['items_allowed'],
                        'status' => $validRow['status'],
                        'remark' => $validRow['remark'],
                        'display_title' => $validRow['display_title'],
                        'description' => $validRow['description'],
                        'icon_type' => $validRow['icon_type'],
                        'is_highlighted' => $validRow['is_highlighted'],
                    ]);
                    $this->importedCount++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Database error during import: " . $e->getMessage();
            }
        }
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

    private function getBoolean($value)
    {
        if (empty($value)) return false;
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'yes', 'true', 'on']);
    }
}

