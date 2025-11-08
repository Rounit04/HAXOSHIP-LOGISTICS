<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

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

        $maxId = count($zones) > 0 ? max(array_column($zones, 'id')) : 0;

        foreach ($collection as $row) {
            $pincode = $this->getValue($row, ['pincode', 'pin code', 'pin']);
            if (empty($pincode)) {
                continue;
            }

            // Check if zone already exists (by pincode, country, network, service combination)
            $country = $this->getValue($row, ['country', 'country_name']);
            $network = $this->getValue($row, ['network', 'network_name']);
            $service = $this->getValue($row, ['service', 'service_name']);

            $exists = collect($zones)->first(function($zone) use ($pincode, $country, $network, $service) {
                return $zone['pincode'] == $pincode 
                    && $zone['country'] == $country 
                    && $zone['network'] == $network 
                    && $zone['service'] == $service;
            });

            if ($exists) {
                continue;
            }

            $maxId++;
            $zone = $this->getValue($row, ['zone', 'zone_name']);
            $status = $this->getStatus($this->getValue($row, ['status']));
            $remark = $this->getValue($row, ['remark', 'remarks']);

            $zones[] = [
                'id' => $maxId,
                'pincode' => $pincode,
                'country' => $country ?? '',
                'zone' => $zone ?? '',
                'network' => $network ?? '',
                'service' => $service ?? '',
                'status' => $status,
                'remark' => $remark ?? '',
            ];
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

