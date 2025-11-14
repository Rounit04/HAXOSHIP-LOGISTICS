<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use App\Models\Network;

class FormulasImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $formulas = session('formulas', []);
        if (!is_array($formulas)) {
            $formulas = [];
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

        $maxId = count($formulas) > 0 ? max(array_column($formulas, 'id')) : 0;

        foreach ($collection as $row) {
            $formulaName = $this->getValue($row, ['formula_name', 'formula name', 'name']);
            if (empty($formulaName)) {
                continue; // Skip rows without formula name
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

            // Check if service has this network
            $serviceMatch = collect($services)->first(function($svc) use ($service, $network) {
                return strcasecmp($svc['name'] ?? '', $service) === 0 &&
                       strcasecmp($svc['network'] ?? '', $network) === 0;
            });

            if (!$serviceMatch) {
                continue; // Skip if service doesn't have this network
            }

            // Check if formula already exists
            $exists = collect($formulas)->first(function($formula) use ($row, $formulaName, $network, $service) {
                return strcasecmp($formula['formula_name'] ?? '', $formulaName) === 0 &&
                       strcasecmp($formula['network'] ?? '', $network) === 0 &&
                       strcasecmp($formula['service'] ?? '', $service) === 0;
            });

            if ($exists) {
                continue; // Skip duplicate
            }

            $maxId++;
            $formulas[] = [
                'id' => $maxId,
                'formula_name' => $formulaName,
                'network' => $network,
                'service' => $service,
                'type' => $this->getValue($row, ['type']) ?? 'Fixed',
                'scope' => $this->getValue($row, ['scope']) ?? 'per kg',
                'priority' => $this->getValue($row, ['priority']) ?? '1st',
                'value' => $this->cleanNumeric($this->getValue($row, ['value'])),
                'status' => $this->getStatus($this->getValue($row, ['status', 'active'])),
                'remark' => $this->getValue($row, ['remark', 'remarks']) ?? '',
            ];
        }

        session(['formulas' => $formulas]);
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

    private function getStatus($value)
    {
        if (empty($value)) return 'Active';
        $status = strtolower(trim((string)$value));
        if (in_array($status, ['active', '1', 'true', 'yes'])) {
            return 'Active';
        }
        return 'Inactive';
    }
}




