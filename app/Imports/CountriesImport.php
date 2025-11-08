<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class CountriesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $countries = session('countries', []);
        if (!is_array($countries)) {
            $countries = [];
        }

        $maxId = count($countries) > 0 ? max(array_column($countries, 'id')) : 0;

        foreach ($collection as $row) {
            $countryName = $this->getValue($row, ['country_name', 'name', 'country']);
            if (empty($countryName)) {
                continue;
            }

            // Check if country already exists
            $exists = collect($countries)->first(function($country) use ($countryName) {
                return strcasecmp($country['name'], $countryName) === 0;
            });

            if ($exists) {
                continue;
            }

            $maxId++;
            $code = $this->getValue($row, ['country_code', 'code']);
            $isdNo = $this->getValue($row, ['country_isd_no', 'isd_no', 'isd no']);
            $dialingCode = $this->getValue($row, ['country_dialing_code', 'dialing_code', 'dialing code']);
            $status = $this->getStatus($this->getValue($row, ['status']));
            $remark = $this->getValue($row, ['remark', 'remarks']);

            $countries[] = [
                'id' => $maxId,
                'name' => $countryName,
                'code' => $code ?? '',
                'isd_no' => $isdNo ?? '',
                'dialing_code' => $dialingCode ?? '',
                'status' => $status,
                'remark' => $remark ?? '',
            ];
        }

        session(['countries' => $countries]);
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

