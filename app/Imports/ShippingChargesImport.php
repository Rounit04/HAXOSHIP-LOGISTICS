<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use App\Models\Network;
use App\Models\ShippingCharge;

class ShippingChargesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithChunkReading
{
    public $importedCount = 0;
    public $updatedCount = 0;
    public $insertedCount = 0;
    public $rowNumber = 1;
    public $validRows = [];
    
    /**
     * Chunk size for reading Excel file (processes 2000 rows at a time to reduce memory usage)
     */
    public function chunkSize(): int
    {
        return 2000;
    }

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
        
        // Create case-insensitive lookup map for networks
        $networkMap = [];
        foreach ($networkNames as $networkName) {
            $networkMap[strtolower(trim($networkName))] = $networkName; // Store original case
        }

        // Get all services to validate against (from database first, then session)
        $dbServices = \App\Models\Service::all();
        $servicesData = [];
        
        if ($dbServices->isNotEmpty()) {
            // Store services with their network information
            foreach ($dbServices as $service) {
                $servicesData[] = [
                    'name' => $service->name,
                    'network' => $service->network,
                ];
            }
        } else {
            // Fallback to session services if database is empty
            if (session()->has('services')) {
                $sessionServices = session('services', []);
                if (is_array($sessionServices)) {
                    foreach ($sessionServices as $service) {
                        $servicesData[] = [
                            'name' => $service['name'] ?? '',
                            'network' => $service['network'] ?? '',
                        ];
                    }
                }
            }
        }
        
        // Create case-insensitive lookup map for services
        $serviceMap = [];
        $serviceNetworkMap = []; // Map service name to network
        foreach ($servicesData as $serviceData) {
            $serviceName = $serviceData['name'];
            $serviceNameLower = strtolower(trim($serviceName));
            $serviceMap[$serviceNameLower] = $serviceName; // Store original case
            $serviceNetworkMap[$serviceNameLower] = $serviceData['network']; // Store network for service
        }
        
        $serviceNames = array_column($servicesData, 'name');

        // Get all countries to validate against
        $dbCountries = \App\Models\Country::all();
        $countryNames = $dbCountries->pluck('name')->toArray();
        
        // Fallback to session countries if database is empty
        if (empty($countryNames) && session()->has('countries')) {
            $sessionCountries = session('countries', []);
            if (is_array($sessionCountries)) {
                $countryNames = collect($sessionCountries)->pluck('name')->toArray();
            }
        }
        
        // Create case-insensitive lookup map for countries
        $countryMap = [];
        foreach ($countryNames as $countryName) {
            $countryMap[strtolower(trim($countryName))] = $countryName; // Store original case
        }
        
        // Get all zones to validate against (from database first, then session)
        $dbZones = \App\Models\Zone::all();
        $zonesData = [];
        
        if ($dbZones->isNotEmpty()) {
            // Store zones with their country, zone, network, and service information
            foreach ($dbZones as $zone) {
                $zonesData[] = [
                    'country' => $zone->country,
                    'zone' => $zone->zone,
                    'network' => $zone->network,
                    'service' => $zone->service,
                    'status' => $zone->status,
                ];
            }
        } else {
            // Fallback to session zones if database is empty
            if (session()->has('zones')) {
                $sessionZones = session('zones', []);
                if (is_array($sessionZones)) {
                    foreach ($sessionZones as $zone) {
                        $zonesData[] = [
                            'country' => $zone['country'] ?? '',
                            'zone' => $zone['zone'] ?? '',
                            'network' => $zone['network'] ?? '',
                            'service' => $zone['service'] ?? '',
                            'status' => $zone['status'] ?? 'Active',
                        ];
                    }
                }
            }
        }
        
        // Create lookup map for zones: country => [zone => [network => [service => true]]]
        // Only include active zones
        // Use case-insensitive matching for zone names
        $zoneMap = [];
        foreach ($zonesData as $zoneData) {
            if (($zoneData['status'] ?? '') == 'Active') {
                $country = strtolower(trim($zoneData['country'] ?? ''));
                $zone = strtolower(trim($zoneData['zone'] ?? '')); // Case-insensitive zone matching
                $network = strtolower(trim($zoneData['network'] ?? ''));
                $service = strtolower(trim($zoneData['service'] ?? ''));
                
                if (!isset($zoneMap[$country])) {
                    $zoneMap[$country] = [];
                }
                if (!isset($zoneMap[$country][$zone])) {
                    $zoneMap[$country][$zone] = [];
                }
                if (!isset($zoneMap[$country][$zone][$network])) {
                    $zoneMap[$country][$zone][$network] = [];
                }
                $zoneMap[$country][$zone][$network][$service] = true;
            }
        }

        // Process all rows directly without validation errors
        // Skip only completely empty rows, process everything else
        foreach ($collection as $row) {
            $this->rowNumber++;
            
            $origin = $this->getValue($row, ['origin', 'origin_country']);
            $origin = $origin ? trim($origin) : '';
            
            // Try to match country, use as-is if not found
            if (!empty($origin)) {
                $originLower = strtolower($origin);
                if (isset($countryMap[$originLower])) {
                    $origin = $countryMap[$originLower];
                }
            }

            $destination = $this->getValue($row, ['destination', 'destination_country']);
            $destination = $destination ? trim($destination) : '';
            
            // Try to match country, use as-is if not found
            if (!empty($destination)) {
                $destinationLower = strtolower($destination);
                if (isset($countryMap[$destinationLower])) {
                    $destination = $countryMap[$destinationLower];
                }
            }

            $network = $this->getValue($row, ['network', 'network_name']);
            $network = $network ? trim($network) : '';
            
            // Try to match network, use as-is if not found
            if (!empty($network)) {
                $networkLower = strtolower($network);
                if (isset($networkMap[$networkLower])) {
                    $network = $networkMap[$networkLower];
                }
            }

            $service = $this->getValue($row, ['service', 'service_name']);
            $service = $service ? trim($service) : '';
            
            // Try to match service, use as-is if not found
            if (!empty($service)) {
                $serviceLower = strtolower($service);
                if (isset($serviceMap[$serviceLower])) {
                    $service = $serviceMap[$serviceLower];
                }
            }

            $originZone = $this->getValue($row, ['origin_zone', 'origin zone']) ?? '';
            $originZone = $originZone ? trim($originZone) : '';
            $destinationZone = $this->getValue($row, ['destination_zone', 'destination zone']) ?? '';
            $destinationZone = $destinationZone ? trim($destinationZone) : '';
            
            $shipmentType = $this->getValue($row, ['shipment_type', 'shipment type']) ?? 'Dox';
            $minWeight = $this->cleanNumeric($this->getValue($row, ['min_weight', 'min weight']));
            $maxWeight = $this->cleanNumeric($this->getValue($row, ['max_weight', 'max weight']));
            $rate = $this->cleanNumeric($this->getValue($row, ['rate', 'charge', 'price']));
            $remark = $this->getValue($row, ['remark', 'remarks']) ?? '';

            // Skip only if all essential fields are empty
            if (empty($origin) && empty($destination) && empty($network) && empty($service)) {
                continue; // Skip completely empty rows
            }

            // Use default rate if not provided
            if (empty($rate) || $rate <= 0) {
                $rate = 0; // Allow zero rate
            }

            // Normalize zone placeholders and store row for import
            $normalizedOriginZone = $this->normalizeZoneValue($originZone);
            $normalizedDestinationZone = $this->normalizeZoneValue($destinationZone);
            
            $this->validRows[] = [
                'row_number' => $this->rowNumber,
                'origin' => $origin,
                'origin_zone' => $normalizedOriginZone,
                'destination' => $destination,
                'destination_zone' => $normalizedDestinationZone,
                'shipment_type' => $shipmentType,
                'min_weight' => $minWeight,
                'max_weight' => $maxWeight,
                'network' => $network,
                'service' => $service,
                'rate' => $rate,
                'remark' => $remark,
            ];
        }

        // Import all processed rows directly
        if (!empty($this->validRows)) {
            // Use database transactions for better performance with large datasets
            \DB::beginTransaction();
            try {
                // Process in smaller batches to avoid SQLite expression tree limit
                // For large datasets, check existing records in small chunks to avoid SQLite's 1000 expression limit
                $checkBatchSize = 50; // Small chunks to avoid SQLite expression tree limit
                $insertBatchSize = 1000;
                
                // Get count of existing records to decide strategy
                $existingCount = ShippingCharge::count();
                
                // Helper to normalize text/zone values
                $normalizeValue = function($value, $isZone = false) {
                    if ($isZone) {
                        return $this->normalizeZoneValue($value);
                    }
                    return $this->normalizeStringValue($value);
                };
                
                $keyPart = function($value, $isZone = false) use ($normalizeValue) {
                    $normalized = $normalizeValue($value, $isZone);
                    return $normalized === '' ? '' : mb_strtolower($normalized);
                };
                
                // Helper function to create a consistent key for duplicate checking (case-insensitive, trimmed)
                $createKey = function($origin, $destination, $originZone, $destinationZone, $network, $service) use ($keyPart) {
                    return $keyPart($origin) . '|' . 
                           $keyPart($destination) . '|' . 
                           $keyPart($originZone, true) . '|' . 
                           $keyPart($destinationZone, true) . '|' . 
                           $keyPart($network) . '|' . 
                           $keyPart($service);
                };
                
                // Always use chunked approach for better reliability with large datasets
                // This ensures we properly check for existing records even with 1973+ records
                $toInsert = [];
                $toUpdate = [];
                $checkChunks = array_chunk($this->validRows, $checkBatchSize);
                
                foreach ($checkChunks as $checkChunk) {
                    // Build a query to check for existing records in this chunk
                    $existingRecords = ShippingCharge::where(function($query) use ($checkChunk, $normalizeValue) {
                        foreach ($checkChunk as $row) {
                            $query->orWhere(function($q) use ($row, $normalizeValue) {
                                $q->where('origin', $normalizeValue($row['origin']))
                                  ->where('destination', $normalizeValue($row['destination']))
                                  ->where(function($zoneQ) use ($row, $normalizeValue) {
                                      $originZone = $normalizeValue($row['origin_zone'], true);
                                      if ($originZone === '') {
                                          $zoneQ->where(function($z) {
                                              $z->whereNull('origin_zone')->orWhere('origin_zone', '');
                                          });
                                      } else {
                                          $zoneQ->where('origin_zone', $originZone);
                                      }
                                  })
                                  ->where(function($zoneQ) use ($row, $normalizeValue) {
                                      $destZone = $normalizeValue($row['destination_zone'], true);
                                      if ($destZone === '') {
                                          $zoneQ->where(function($z) {
                                              $z->whereNull('destination_zone')->orWhere('destination_zone', '');
                                          });
                                      } else {
                                          $zoneQ->where('destination_zone', $destZone);
                                      }
                                  })
                                  ->where('network', $normalizeValue($row['network']))
                                  ->where('service', $normalizeValue($row['service']));
                            });
                        }
                    })->get()->keyBy(function($item) use ($createKey) {
                        return $createKey(
                            $item->origin,
                            $item->destination,
                            $item->origin_zone,
                            $item->destination_zone,
                            $item->network,
                            $item->service
                        );
                    });
                    
                    // Separate this chunk into inserts and updates
                    foreach ($checkChunk as $row) {
                        $key = $createKey(
                            $row['origin'],
                            $row['destination'],
                            $row['origin_zone'],
                            $row['destination_zone'],
                            $row['network'],
                            $row['service']
                        );
                        
                        if ($existingRecords->has($key)) {
                            $toUpdate[] = ['record' => $existingRecords[$key], 'data' => $row];
                        } else {
                            $toInsert[] = $row;
                        }
                    }
                }
                
                // Batch update existing records
                if (!empty($toUpdate)) {
                    $updateChunks = array_chunk($toUpdate, 500);
                    foreach ($updateChunks as $chunk) {
                        foreach ($chunk as $item) {
                            // Normalize zone values to empty strings instead of NULL for consistency
                            $item['record']->update([
                                'shipment_type' => $item['data']['shipment_type'],
                                'min_weight' => $item['data']['min_weight'],
                                'max_weight' => $item['data']['max_weight'],
                                'rate' => $item['data']['rate'],
                                'remark' => $item['data']['remark'],
                                'origin_zone' => $normalizeValue($item['data']['origin_zone'], true),
                                'destination_zone' => $normalizeValue($item['data']['destination_zone'], true),
                                'updated_at' => now(),
                            ]);
                            $this->importedCount++;
                            $this->updatedCount++;
                        }
                    }
                }
                
                // Batch insert new records
                if (!empty($toInsert)) {
                    $insertChunks = array_chunk($toInsert, $insertBatchSize);
                    foreach ($insertChunks as $chunk) {
                        $insertData = [];
                        $now = now();
                        foreach ($chunk as $row) {
                            // Ensure all required fields have values
                            $insertData[] = [
                                'origin' => $normalizeValue($row['origin']) ?: '',
                                'origin_zone' => $normalizeValue($row['origin_zone'], true),
                                'destination' => $normalizeValue($row['destination']) ?: '',
                                'destination_zone' => $normalizeValue($row['destination_zone'], true),
                                'shipment_type' => $row['shipment_type'] ?: 'Dox',
                                'min_weight' => $row['min_weight'] ?? 0,
                                'max_weight' => $row['max_weight'] ?? 0,
                                'network' => $normalizeValue($row['network']) ?: '',
                                'service' => $normalizeValue($row['service']) ?: '',
                                'rate' => $row['rate'] ?? 0,
                                'remark' => $row['remark'] ?? '',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        // Use insertOrIgnore to prevent duplicate key errors, but we've already checked
                        // So use regular insert for better performance
                        try {
                            \DB::table('shipping_charges')->insert($insertData);
                            $this->importedCount += count($chunk);
                            $this->insertedCount += count($chunk);
                        } catch (\Exception $e) {
                            // If insert fails (e.g., duplicate), try inserting one by one
                            \Log::info('Batch insert failed, trying individual inserts: ' . $e->getMessage());
                            foreach ($insertData as $singleInsert) {
                                try {
                                    \DB::table('shipping_charges')->insert($singleInsert);
                                    $this->importedCount++;
                                    $this->insertedCount++;
                                } catch (\Exception $singleE) {
                                    // Skip this record if it still fails
                                    \Log::info('Skipped duplicate record: ' . $singleE->getMessage());
                                }
                            }
                        }
                    }
                }
                
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                // Re-throw to be caught by the controller
                throw $e;
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

    private function cleanNumeric($value)
    {
        if (empty($value)) return 0;
        if (is_numeric($value)) return (float)$value;
        $cleaned = preg_replace('/[^0-9.]/', '', (string)$value);
        return $cleaned !== '' ? (float)$cleaned : 0;
    }

    /**
     * Normalize generic string values (trim + handle nulls)
     */
    private function normalizeStringValue($value)
    {
        if ($value === null) {
            return '';
        }
        $value = trim((string)$value);
        return $value;
    }

    /**
     * Normalize zone values so that placeholders like "No Zone" map to empty strings.
     */
    private function normalizeZoneValue($value)
    {
        $normalized = $this->normalizeStringValue($value);
        if ($normalized === '') {
            return '';
        }
        
        $valueLower = mb_strtolower($normalized);
        $placeholders = [
            'no zone',
            'no-zone',
            'nozone',
            'no pincode',
            'no-pincode',
            'nopincode',
            'no pin',
            'no-pin',
            'nopin',
            'n/a',
            'not applicable',
            'not-applicable',
            'not available',
            'notavailable',
        ];
        
        if (in_array($valueLower, $placeholders, true)) {
            return '';
        }
        
        return $normalized;
    }
}
