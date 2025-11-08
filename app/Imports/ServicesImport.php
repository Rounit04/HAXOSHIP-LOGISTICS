<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class ServicesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $services = session('services', []);
        if (!is_array($services)) {
            $services = [];
        }

        $maxId = count($services) > 0 ? max(array_column($services, 'id')) : 0;

        foreach ($collection as $row) {
            $serviceName = $this->getValue($row, ['service_name', 'name', 'service']);
            if (empty($serviceName)) {
                continue; // Skip rows without service name
            }

            // Check if service already exists
            $exists = collect($services)->first(function($service) use ($serviceName) {
                return strcasecmp($service['name'], $serviceName) === 0;
            });

            if ($exists) {
                continue; // Skip duplicate
            }

            $maxId++;
            $network = $this->getValue($row, ['network', 'network_name']);
            $transitTime = $this->getValue($row, ['transit_time', 'transit time']);
            $itemsAllowed = $this->getValue($row, ['items_allowed', 'items allowed']);
            $status = $this->getStatus($this->getValue($row, ['status']));
            $remark = $this->getValue($row, ['remark', 'remarks']);
            $displayTitle = $this->getValue($row, ['display_title', 'display title']) ?? $serviceName;
            $description = $this->getValue($row, ['description']);
            $iconType = $this->getValue($row, ['icon_type', 'icon type']) ?? 'truck';
            $isHighlighted = $this->getBoolean($this->getValue($row, ['is_highlighted', 'is highlighted', 'highlighted']));

            $services[] = [
                'id' => $maxId,
                'name' => $serviceName,
                'network' => $network ?? '',
                'transit_time' => $transitTime ?? '',
                'items_allowed' => $itemsAllowed ?? '',
                'status' => $status,
                'remark' => $remark ?? '',
                'display_title' => $displayTitle,
                'description' => $description ?? '',
                'icon_type' => $iconType,
                'is_highlighted' => $isHighlighted,
            ];
        }

        session(['services' => $services]);
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

    private function getBoolean($value)
    {
        if (empty($value)) return false;
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'yes', 'true', 'on']);
    }
}

