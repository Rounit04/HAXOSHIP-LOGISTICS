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
    public $errors = [];
    public $importedCount = 0;
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

        // STEP 1: Validate ALL rows first (don't import anything yet)
        foreach ($collection as $row) {
            $this->rowNumber++;
            $rowErrors = [];
            
            $origin = $this->getValue($row, ['origin', 'origin_country']);
            $origin = $origin ? trim($origin) : '';
            
            if (empty($origin)) {
                $rowErrors[] = "Origin is required";
            } else {
                // Case-insensitive country matching
                $originLower = strtolower($origin);
                if (!isset($countryMap[$originLower])) {
                    $rowErrors[] = "Origin country '{$origin}' does not exist. Please create the country first";
                } else {
                    // Use the original case from database
                    $origin = $countryMap[$originLower];
                }
            }

            $destination = $this->getValue($row, ['destination', 'destination_country']);
            $destination = $destination ? trim($destination) : '';
            
            if (empty($destination)) {
                $rowErrors[] = "Destination is required";
            } else {
                // Case-insensitive country matching
                $destinationLower = strtolower($destination);
                if (!isset($countryMap[$destinationLower])) {
                    $rowErrors[] = "Destination country '{$destination}' does not exist. Please create the country first";
                } else {
                    // Use the original case from database
                    $destination = $countryMap[$destinationLower];
                }
            }

            $network = $this->getValue($row, ['network', 'network_name']);
            $network = $network ? trim($network) : '';
            
            if (empty($network)) {
                $rowErrors[] = "Network is required";
            } else {
                // Case-insensitive network matching
                $networkLower = strtolower($network);
                if (!isset($networkMap[$networkLower])) {
                    $rowErrors[] = "Network '{$network}' does not exist. Please create the network first";
                } else {
                    // Use the original case from database
                    $network = $networkMap[$networkLower];
                }
            }

            $service = $this->getValue($row, ['service', 'service_name']);
            $service = $service ? trim($service) : '';
            
            if (empty($service)) {
                $rowErrors[] = "Service is required";
            } else {
                // Case-insensitive service matching
                $serviceLower = strtolower($service);
                if (!isset($serviceMap[$serviceLower])) {
                    $rowErrors[] = "Service '{$service}' does not exist. Please create the service first";
                } else {
                    // Use the original case from database
                    $service = $serviceMap[$serviceLower];
                    
                    // Validate that service belongs to the network
                    if (isset($network) && !empty($network) && isset($serviceNetworkMap[$serviceLower])) {
                        $serviceNetwork = $serviceNetworkMap[$serviceLower];
                        $networkLower = strtolower(trim($network));
                        $serviceNetworkLower = strtolower(trim($serviceNetwork));
                        if ($networkLower !== $serviceNetworkLower) {
                            $rowErrors[] = "Service '{$service}' does not belong to network '{$network}'. Service belongs to network '{$serviceNetwork}'";
                        }
                    }
                }
            }

            $originZone = $this->getValue($row, ['origin_zone', 'origin zone']) ?? '';
            $originZone = $originZone ? trim($originZone) : '';
            $destinationZone = $this->getValue($row, ['destination_zone', 'destination zone']) ?? '';
            $destinationZone = $destinationZone ? trim($destinationZone) : '';
            
            // Validate origin zone exists for origin country (if provided and not "No Zone")
            if (!empty($originZone) && strcasecmp($originZone, 'No Zone') !== 0 && isset($origin) && !empty($origin)) {
                $originLower = strtolower(trim($origin));
                $originZoneLower = strtolower($originZone);
                $networkLower = isset($network) ? strtolower(trim($network)) : '';
                $serviceLower = isset($service) ? strtolower(trim($service)) : '';
                
                $zoneExists = false;
                if (isset($zoneMap[$originLower][$originZoneLower])) {
                    // Check if zone exists for this network and service combination
                    if (!empty($networkLower) && !empty($serviceLower)) {
                        if (isset($zoneMap[$originLower][$originZoneLower][$networkLower][$serviceLower])) {
                            $zoneExists = true;
                        }
                    } else {
                        // If network/service not provided, just check if zone exists for country
                        $zoneExists = true;
                    }
                }
                
                if (!$zoneExists) {
                    $networkServiceInfo = '';
                    if (!empty($network) && !empty($service)) {
                        $networkServiceInfo = " for network '{$network}' and service '{$service}'";
                    }
                    $rowErrors[] = "Origin zone '{$originZone}' does not exist for origin country '{$origin}'{$networkServiceInfo}. Please create the zone first";
                }
            }
            
            // Validate destination zone exists for destination country (if provided and not "No Zone")
            if (!empty($destinationZone) && strcasecmp($destinationZone, 'No Zone') !== 0 && isset($destination) && !empty($destination)) {
                $destinationLower = strtolower(trim($destination));
                $destinationZoneLower = strtolower($destinationZone);
                $networkLower = isset($network) ? strtolower(trim($network)) : '';
                $serviceLower = isset($service) ? strtolower(trim($service)) : '';
                
                $zoneExists = false;
                if (isset($zoneMap[$destinationLower][$destinationZoneLower])) {
                    // Check if zone exists for this network and service combination
                    if (!empty($networkLower) && !empty($serviceLower)) {
                        if (isset($zoneMap[$destinationLower][$destinationZoneLower][$networkLower][$serviceLower])) {
                            $zoneExists = true;
                        }
                    } else {
                        // If network/service not provided, just check if zone exists for country
                        $zoneExists = true;
                    }
                }
                
                if (!$zoneExists) {
                    $networkServiceInfo = '';
                    if (!empty($network) && !empty($service)) {
                        $networkServiceInfo = " for network '{$network}' and service '{$service}'";
                    }
                    $rowErrors[] = "Destination zone '{$destinationZone}' does not exist for destination country '{$destination}'{$networkServiceInfo}. Please create the zone first";
                }
            }
            
            $shipmentType = $this->getValue($row, ['shipment_type', 'shipment type']) ?? 'Dox';
            $minWeight = $this->cleanNumeric($this->getValue($row, ['min_weight', 'min weight']));
            $maxWeight = $this->cleanNumeric($this->getValue($row, ['max_weight', 'max weight']));
            $rate = $this->cleanNumeric($this->getValue($row, ['rate', 'charge', 'price']));
            $remark = $this->getValue($row, ['remark', 'remarks']) ?? '';

            // Validate rate
            if (empty($rate) || $rate <= 0) {
                $rowErrors[] = "Rate is required and must be greater than 0";
            }

            // If there are errors in this row, add them to the errors list
            if (!empty($rowErrors)) {
                $originDisplay = !empty($origin) ? "'{$origin}'" : 'Unknown';
                $destinationDisplay = !empty($destination) ? "'{$destination}'" : 'Unknown';
                $this->errors[] = "Row {$this->rowNumber} (Origin: {$originDisplay}, Destination: {$destinationDisplay}): " . implode(', ', $rowErrors);
            } else {
                // Row is valid, store it for import (normalize zone placeholders)
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
        }

        // STEP 2: Only import if there are NO errors (all-or-nothing approach)
        // If any errors were found in STEP 1, skip import entirely
        // This ensures errors are shown first, and import only happens when all data is valid
        if (empty($this->errors)) {
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
                
                // If we have few existing records (< 5000), load all into memory for fast lookup
                // Otherwise, check in small chunks to avoid memory issues
                if ($existingCount < 5000) {
                    // Load all existing records into memory for fast lookup
                    $allExisting = ShippingCharge::all()->keyBy(function($item) use ($createKey) {
                        return $createKey(
                            $item->origin,
                            $item->destination,
                            $item->origin_zone,
                            $item->destination_zone,
                            $item->network,
                            $item->service
                        );
                    });
                    
                    // Separate into inserts and updates
                    $toInsert = [];
                    $toUpdate = [];
                    
                foreach ($this->validRows as $row) {
                        $key = $createKey(
                            $row['origin'],
                            $row['destination'],
                            $row['origin_zone'],
                            $row['destination_zone'],
                            $row['network'],
                            $row['service']
                        );
                        
                        if ($allExisting->has($key)) {
                            $toUpdate[] = ['record' => $allExisting[$key], 'data' => $row];
                        } else {
                            $toInsert[] = $row;
                        }
                    }
                } else {
                    // For large datasets, check in small chunks to avoid SQLite limits
                    $toInsert = [];
                    $toUpdate = [];
                    $checkChunks = array_chunk($this->validRows, $checkBatchSize);
                    
                    foreach ($checkChunks as $checkChunk) {
                        // Build a simple query for this small chunk (max 50 records)
                        // Handle NULL/empty zone values properly in the query
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
                            $insertData[] = [
                                'origin' => $normalizeValue($row['origin']),
                                'origin_zone' => $normalizeValue($row['origin_zone'], true),
                                'destination' => $normalizeValue($row['destination']),
                                'destination_zone' => $normalizeValue($row['destination_zone'], true),
                                'shipment_type' => $row['shipment_type'],
                                'min_weight' => $row['min_weight'],
                                'max_weight' => $row['max_weight'],
                                'network' => $normalizeValue($row['network']),
                                'service' => $normalizeValue($row['service']),
                                'rate' => $row['rate'],
                                'remark' => $row['remark'],
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        \DB::table('shipping_charges')->insert($insertData);
                        $this->importedCount += count($chunk);
                    }
                }
                
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                // Re-throw to be caught by the controller
                throw $e;
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
