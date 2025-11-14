<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use App\Models\Network;

class ZonesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $zones = session('zones', []);
        if (!is_array($zones)) {
            $zones = [];
        }

        // Get all networks to validate against
        $dbNetworks = Network::all();
        $networkNames = $dbNetworks->pluck('name')->toArray();
        
        // Fallback to session networks if database is empty
        if (empty($networkNames) && session()->has('networks')) {
            $sessionNetworks = session('networks', []);
            $networkNames = collect($sessionNetworks)->pluck('name')->toArray();
        }

        // Get all services to validate against
        $services = session('services', []);
        if (!is_array($services)) {
            $services = [];
        }
        $serviceNames = collect($services)->pluck('name')->toArray();

        $maxId = count($zones) > 0 ? max(array_column($zones, 'id')) : 0;

        foreach ($collection as $row) {
            $pincode = $this->getValue($row, ['pincode', 'pin code', 'pin']);
            if (empty($pincode)) {
                continue;
            }

            $network = $this->getValue($row, ['network', 'network_name']);
            $service = $this->getValue($row, ['service', 'service_name']);
            
            // Skip if network doesn't exist
            if (empty($network) || !in_array($network, $networkNames)) {
                continue;
            }

            // Skip if service doesn't exist
            if (empty($service) || !in_array($service, $serviceNames)) {
                continue;
            }

            // Check if zone with same pincode already exists - update it instead of creating new
            $existingZoneIndex = null;
            foreach ($zones as $index => $existingZone) {
                if (strcasecmp($existingZone['pincode'] ?? '', $pincode) === 0) {
                    $existingZoneIndex = $index;
                    break;
                }
            }

            $country = $this->getValue($row, ['country', 'country_name']);
            $zoneName = $this->getValue($row, ['zone', 'zone_name']);
            $status = $this->getStatus($this->getValue($row, ['status']));
            $remark = $this->getValue($row, ['remark', 'remarks']);

            if ($existingZoneIndex !== null) {
                // Update existing zone with new network and service
                $existingZone = $zones[$existingZoneIndex];
                $zones[$existingZoneIndex] = [
                    'id' => $existingZone['id'],
                    'pincode' => $pincode,
                    'country' => $country ?? $existingZone['country'] ?? '',
                    'zone' => $zoneName ?? $existingZone['zone'] ?? '',
                    'network' => $network,
                    'service' => $service,
                    'status' => $status,
                    'remark' => $remark ?? $existingZone['remark'] ?? '',
                    'created_at' => $existingZone['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            } else {
                // Create new zone
                $maxId++;
                $zones[] = [
                    'id' => $maxId,
                    'pincode' => $pincode,
                    'country' => $country ?? '',
                    'zone' => $zoneName ?? '',
                    'network' => $network,
                    'service' => $service,
                    'status' => $status,
                    'remark' => $remark ?? '',
                    'created_at' => now()->toDateTimeString(),
                ];
            }
        }

        session(['zones' => $zones]);
        session()->save();
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
