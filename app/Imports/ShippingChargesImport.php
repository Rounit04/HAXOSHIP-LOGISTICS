<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use App\Models\Network;

class ShippingChargesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $shippingCharges = session('shipping_charges', []);
        if (!is_array($shippingCharges)) {
            $shippingCharges = [];
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

        // Get all countries to validate against
        $countries = session('countries', []);
        if (!is_array($countries)) {
            $countries = [];
        }
        $countryNames = collect($countries)->pluck('name')->toArray();

        $maxId = count($shippingCharges) > 0 ? max(array_column($shippingCharges, 'id')) : 0;

        foreach ($collection as $row) {
            $origin = $this->getValue($row, ['origin', 'origin_country']);
            if (empty($origin)) {
                continue; // Skip rows without origin
            }

            $destination = $this->getValue($row, ['destination', 'destination_country']);
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

            // Skip if origin doesn't exist (country)
            if (empty($origin) || !in_array($origin, $countryNames)) {
                continue;
            }

            // Skip if destination doesn't exist (country)
            if (empty($destination) || !in_array($destination, $countryNames)) {
                continue;
            }

            $originZone = $this->getValue($row, ['origin_zone', 'origin zone']) ?? '';
            $destinationZone = $this->getValue($row, ['destination_zone', 'destination zone']) ?? '';
            $shipmentType = $this->getValue($row, ['shipment_type', 'shipment type']) ?? 'Dox';
            $minWeight = $this->cleanNumeric($this->getValue($row, ['min_weight', 'min weight']));
            $maxWeight = $this->cleanNumeric($this->getValue($row, ['max_weight', 'max weight']));
            $rate = $this->cleanNumeric($this->getValue($row, ['rate', 'charge', 'price']));
            $remark = $this->getValue($row, ['remark', 'remarks']) ?? '';

            // Check if shipping charge with same origin, destination, zones, network, service already exists - update it
            $existingChargeIndex = null;
            foreach ($shippingCharges as $index => $charge) {
                if (strcasecmp($charge['origin'] ?? '', $origin) === 0 &&
                    strcasecmp($charge['destination'] ?? '', $destination) === 0 &&
                    strcasecmp($charge['origin_zone'] ?? '', $originZone) === 0 &&
                    strcasecmp($charge['destination_zone'] ?? '', $destinationZone) === 0 &&
                    strcasecmp($charge['network'] ?? '', $network) === 0 &&
                    strcasecmp($charge['service'] ?? '', $service) === 0) {
                    $existingChargeIndex = $index;
                    break;
                }
            }

            if ($existingChargeIndex !== null) {
                // Update existing charge with new rate and other data
                $existingCharge = $shippingCharges[$existingChargeIndex];
                $shippingCharges[$existingChargeIndex] = [
                    'id' => $existingCharge['id'],
                    'origin' => $origin,
                    'origin_zone' => $originZone,
                    'destination' => $destination,
                    'destination_zone' => $destinationZone,
                    'shipment_type' => $shipmentType,
                    'min_weight' => $minWeight,
                    'max_weight' => $maxWeight,
                    'network' => $network,
                    'service' => $service,
                    'rate' => $rate,
                    'remark' => $remark,
                    'created_at' => $existingCharge['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            } else {
                // Create new charge
            $maxId++;
            $shippingCharges[] = [
                'id' => $maxId,
                'origin' => $origin,
                    'origin_zone' => $originZone,
                    'destination' => $destination,
                    'destination_zone' => $destinationZone,
                    'shipment_type' => $shipmentType,
                    'min_weight' => $minWeight,
                    'max_weight' => $maxWeight,
                    'network' => $network,
                    'service' => $service,
                    'rate' => $rate,
                    'remark' => $remark,
                    'created_at' => now()->toDateTimeString(),
            ];
            }
        }

        session(['shipping_charges' => $shippingCharges]);
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

    private function cleanNumeric($value)
    {
        if (empty($value)) return 0;
        if (is_numeric($value)) return (float)$value;
        $cleaned = preg_replace('/[^0-9.]/', '', (string)$value);
        return $cleaned !== '' ? (float)$cleaned : 0;
    }
}
