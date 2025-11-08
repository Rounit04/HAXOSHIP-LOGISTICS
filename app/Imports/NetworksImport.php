<?php

namespace App\Imports;

use App\Models\Network;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class NetworksImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $networkName = $this->getValue($row, ['network_name', 'name', 'network']);
        if (empty($networkName)) {
            return null; // Skip rows without network name
        }

        // Check if network already exists
        if (Network::where('name', $networkName)->exists()) {
            return null; // Skip duplicate
        }

        $networkType = $this->getValue($row, ['network_type', 'type']);
        $openingBalance = $this->cleanNumeric($this->getValue($row, ['opening_balance', 'opening balance']));
        $status = $this->getStatus($this->getValue($row, ['status']));
        $bankDetails = $this->getValue($row, ['bank_details', 'bank details']);
        $remark = $this->getValue($row, ['remark', 'remarks']);

        return new Network([
            'name' => $networkName,
            'type' => in_array($networkType, ['Domestic', 'International']) ? $networkType : 'Domestic',
            'opening_balance' => $openingBalance ?? 0,
            'status' => $status,
            'bank_details' => $bankDetails ?? '',
            'remark' => $remark ?? '',
        ]);
    }

    /**
     * Get value from row with multiple possible key variations
     */
    private function getValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key])) {
                return $row[$key];
            }
            // Try case-insensitive match
            foreach ($row as $rowKey => $value) {
                if (strcasecmp(trim($rowKey), trim($key)) === 0) {
                    return $value;
                }
            }
        }
        return null;
    }

    /**
     * Clean numeric values
     */
    private function cleanNumeric($value)
    {
        if (empty($value)) return 0;
        if (is_numeric($value)) return (float)$value;
        $cleaned = preg_replace('/[^0-9.]/', '', (string)$value);
        return $cleaned !== '' ? (float)$cleaned : 0;
    }

    /**
     * Get status value
     */
    private function getStatus($value)
    {
        if (empty($value)) return 'Active';
        $status = strtolower(trim($value));
        if (in_array($status, ['active', '1', 'yes', 'true'])) {
            return 'Active';
        }
        return 'Inactive';
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'network_name' => 'required',
        ];
    }
}
