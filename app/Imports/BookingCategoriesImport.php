<?php

namespace App\Imports;

use App\Models\BookingCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class BookingCategoriesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $categoryName = $this->getValue($row, ['category_name', 'name', 'category']);
        if (empty($categoryName)) {
            return null; // Skip rows without category name
        }

        // Check if category already exists
        if (BookingCategory::where('name', $categoryName)->exists()) {
            return null; // Skip duplicate
        }

        $type = $this->getValue($row, ['type']);
        $requiresAwb = $this->getBoolean($this->getValue($row, ['requires_awb', 'requires awb', 'requires_awb_number']));
        $status = $this->getStatus($this->getValue($row, ['status']));

        return new BookingCategory([
            'name' => $categoryName,
            'type' => in_array($type, ['wallet', 'ledger', 'support']) ? $type : 'wallet',
            'requires_awb' => $requiresAwb,
            'status' => $status,
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
     * Get status value
     */
    private function getStatus($value)
    {
        if (empty($value)) return 'Active';
        $status = strtolower(trim($value));
        if (in_array($status, ['active', '1', 'yes', 'true'])) {
            return 'Active';
        }
        return 'In-active';
    }

    /**
     * Get boolean value
     */
    private function getBoolean($value)
    {
        if (empty($value)) return false;
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'yes', 'true', 'on']);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'category_name' => 'required',
        ];
    }
}
