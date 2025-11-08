<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

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

        $maxId = count($shippingCharges) > 0 ? max(array_column($shippingCharges, 'id')) : 0;

        foreach ($collection as $row) {
            $origin = $this->getValue($row, ['origin', 'origin_country']);
            if (empty($origin)) {
                continue; // Skip rows without origin
            }

            // Check if shipping charge already exists (based on origin, destination, zone, network, service)
            $exists = collect($shippingCharges)->first(function($charge) use ($row) {
                return strcasecmp($charge['origin'] ?? '', $this->getValue($row, ['origin', 'origin_country']) ?? '') === 0 &&
                       strcasecmp($charge['destination'] ?? '', $this->getValue($row, ['destination', 'destination_country']) ?? '') === 0 &&
                       strcasecmp($charge['origin_zone'] ?? '', $this->getValue($row, ['origin_zone', 'origin zone']) ?? '') === 0 &&
                       strcasecmp($charge['destination_zone'] ?? '', $this->getValue($row, ['destination_zone', 'destination zone']) ?? '') === 0 &&
                       strcasecmp($charge['network'] ?? '', $this->getValue($row, ['network', 'network_name']) ?? '') === 0 &&
                       strcasecmp($charge['service'] ?? '', $this->getValue($row, ['service', 'service_name']) ?? '') === 0;
            });

            if ($exists) {
                continue; // Skip duplicate
            }

            $maxId++;
            $shippingCharges[] = [
                'id' => $maxId,
                'origin' => $origin,
                'origin_zone' => $this->getValue($row, ['origin_zone', 'origin zone']) ?? '',
                'destination' => $this->getValue($row, ['destination', 'destination_country']) ?? '',
                'destination_zone' => $this->getValue($row, ['destination_zone', 'destination zone']) ?? '',
                'shipment_type' => $this->getValue($row, ['shipment_type', 'shipment type']) ?? 'Dox',
                'min_weight' => $this->cleanNumeric($this->getValue($row, ['min_weight', 'min weight'])),
                'max_weight' => $this->cleanNumeric($this->getValue($row, ['max_weight', 'max weight'])),
                'network' => $this->getValue($row, ['network', 'network_name']) ?? '',
                'service' => $this->getValue($row, ['service', 'service_name']) ?? '',
                'rate' => $this->cleanNumeric($this->getValue($row, ['rate', 'charge', 'price'])),
                'remark' => $this->getValue($row, ['remark', 'remarks']) ?? '',
            ];
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
