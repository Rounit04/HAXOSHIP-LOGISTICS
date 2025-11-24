<?php

namespace App\Imports;

use App\Models\ShippingCharge;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ShippingChargesUpdateImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public $updatedCount = 0;
    public $rowNumber = 1;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected $validRows = [];

    /**
     * @var \Illuminate\Support\Collection<string, \App\Models\ShippingCharge>|null
     */
    protected $existingCharges = null;

    /**
     * Process the uploaded rows and queue updates.
     *
     * @param Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        $this->loadExistingCharges();

        foreach ($rows as $row) {
            $this->rowNumber++;

            // Get values without validation errors
            $origin = $this->normalizeStringValue($this->getValue($row, ['origin', 'origin_country']));
            $destination = $this->normalizeStringValue($this->getValue($row, ['destination', 'destination_country']));
            $originZone = $this->normalizeZoneValue($this->getValue($row, ['origin_zone', 'origin zone']));
            $destinationZone = $this->normalizeZoneValue($this->getValue($row, ['destination_zone', 'destination zone']));
            $network = $this->normalizeStringValue($this->getValue($row, ['network', 'network_name']));
            $service = $this->normalizeStringValue($this->getValue($row, ['service', 'service_name']));

            // Skip if essential fields are empty (can't identify record)
            if (empty($origin) && empty($destination) && empty($network) && empty($service)) {
                continue; // Skip completely empty rows
            }

            // Build updates - no validation, just process what's provided
            $updates = [];
            $rateValue = $this->getValue($row, ['rate', 'charge', 'price']);
            if ($rateValue !== null && $rateValue !== '') {
                $rate = $this->cleanNumeric($rateValue);
                if ($rate > 0) {
                    $updates['rate'] = $rate;
                }
            }

            $remarkValue = $this->getValue($row, ['remark', 'remarks']);
            if ($remarkValue !== null && $remarkValue !== '') {
                $updates['remark'] = trim((string)$remarkValue);
            }

            // Skip if no updates to make
            if (empty($updates)) {
                continue;
            }

            // Try to find matching record
            $key = $this->createKey($origin, $destination, $originZone, $destinationZone, $network, $service);

            if ($this->existingCharges->has($key)) {
                $this->validRows[] = [
                    'key' => $key,
                    'updates' => $updates,
                ];
            }
            // If record not found, just skip it (no error)
        }

        // Process all valid updates
        if (!empty($this->validRows)) {
            \DB::beginTransaction();
            try {
                foreach ($this->validRows as $row) {
                    $record = $this->existingCharges->get($row['key']);
                    if ($record) {
                        $record->update(array_merge(
                            $row['updates'],
                            ['updated_at' => now()]
                        ));
                        $this->updatedCount++;
                    }
                }
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                // Log but don't throw - allow partial updates
                \Log::info('Update import error (non-blocking): ' . $e->getMessage());
            }
        }
    }

    protected function loadExistingCharges(): void
    {
        if ($this->existingCharges !== null) {
            return;
        }

        $this->existingCharges = ShippingCharge::all()->keyBy(function ($item) {
            return $this->createKey(
                $item->origin,
                $item->destination,
                $item->origin_zone,
                $item->destination_zone,
                $item->network,
                $item->service
            );
        });
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
        if ($value === null || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }

        $cleaned = preg_replace('/[^0-9.]/', '', (string) $value);
        return $cleaned !== '' ? (float) $cleaned : 0;
    }

    private function normalizeStringValue($value)
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

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

    private function createKey($origin, $destination, $originZone, $destinationZone, $network, $service)
    {
        $parts = [
            $this->keyPart($origin),
            $this->keyPart($destination),
            $this->keyPart($originZone, true),
            $this->keyPart($destinationZone, true),
            $this->keyPart($network),
            $this->keyPart($service),
        ];

        return implode('|', $parts);
    }

    private function keyPart($value, $isZone = false)
    {
        $normalized = $isZone ? $this->normalizeZoneValue($value) : $this->normalizeStringValue($value);
        return $normalized === '' ? '' : mb_strtolower($normalized);
    }

}

