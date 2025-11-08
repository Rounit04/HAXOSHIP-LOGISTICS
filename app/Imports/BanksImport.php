<?php

namespace App\Imports;

use App\Models\Bank;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class BanksImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $bankName = $this->getValue($row, ['bank_name', 'name', 'bank']);
            if (empty($bankName)) {
                continue; // Skip rows without bank name
            }

            $accountNumber = $this->getValue($row, ['account_number', 'account number', 'account_no']);
            
            // Check if bank already exists (based on bank name and account number)
            $exists = Bank::where('bank_name', $bankName)
                ->where('account_number', $accountNumber ?? '')
                ->exists();

            if ($exists) {
                continue; // Skip duplicate
            }

            Bank::create([
                'bank_name' => $bankName,
                'account_holder_name' => $this->getValue($row, ['account_holder_name', 'account holder name', 'holder_name']) ?? '',
                'account_number' => $accountNumber ?? '',
                'ifsc_code' => $this->getValue($row, ['ifsc_code', 'ifsc code', 'ifsc']) ?? '',
                'opening_balance' => $this->cleanNumeric($this->getValue($row, ['opening_balance', 'opening balance'])),
            ]);
        }
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
