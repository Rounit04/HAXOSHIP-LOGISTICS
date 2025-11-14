<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class PaymentsIntoBankImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $payments = session('payments_into_bank', []);
        if (!is_array($payments)) {
            $payments = [];
        }

        // Get all banks for validation
        $banks = session('banks', []);
        if (!is_array($banks)) {
            $banks = [];
        }
        
        // Create bank lookup map (format: "Bank Name - Account Number")
        $bankMap = [];
        foreach ($banks as $bank) {
            $bankKey = ($bank['bank_name'] ?? '') . ' - ' . ($bank['account_number'] ?? '');
            $bankMap[$bankKey] = true;
        }

        $maxId = count($payments) > 0 ? max(array_column($payments, 'id')) : 0;

        foreach ($collection as $row) {
            $bankAccount = $this->getValue($row, ['bank_account', 'bank account', 'bank_account']);
            if (empty($bankAccount)) {
                continue; // Skip rows without bank account
            }

            // Validate bank account exists
            if (!isset($bankMap[$bankAccount])) {
                continue; // Skip if bank account doesn't exist
            }

            $modeOfPayment = $this->getValue($row, ['mode_of_payment', 'mode of payment', 'mode']);
            if (!in_array($modeOfPayment, ['UPI', 'Cash', 'Netf'])) {
                continue; // Skip invalid payment modes
            }

            $type = $this->getValue($row, ['type']);
            if (!in_array($type, ['Credit', 'Debit'])) {
                continue; // Skip invalid types
            }

            $categoryBank = $this->getValue($row, ['category_bank', 'category bank', 'category']);
            if (empty($categoryBank) || !in_array($categoryBank, ['Salary', 'Expense', 'Revenue'])) {
                continue; // Skip rows without valid category
            }

            $transactionNo = $this->getValue($row, ['transaction_no', 'transaction no', 'transaction_no', 'transaction']);
            if (empty($transactionNo)) {
                continue; // Skip rows without transaction number
            }

            $amount = $this->cleanNumeric($this->getValue($row, ['amount']));
            if ($amount === null || $amount < 0) {
                continue; // Skip invalid amounts
            }

            $remark = $this->getValue($row, ['remark', 'remarks', 'note', 'notes']) ?? '';

            // Check if payment already exists (based on transaction number)
            $exists = collect($payments)->first(function($payment) use ($transactionNo) {
                return strcasecmp($payment['transaction_no'] ?? '', $transactionNo) === 0;
            });

            if ($exists) {
                continue; // Skip duplicate transaction numbers
            }

            $maxId++;
            $payments[] = [
                'id' => $maxId,
                'bank_account' => $bankAccount,
                'mode_of_payment' => $modeOfPayment,
                'type' => $type,
                'category_bank' => $categoryBank,
                'transaction_no' => $transactionNo,
                'amount' => $amount,
                'remark' => $remark,
                'created_at' => now()->toDateTimeString(),
            ];
        }

        session(['payments_into_bank' => $payments]);
        session()->save();
    }

    private function getValue($row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key])) {
                return trim($row[$key] ?? '');
            }
            foreach ($row as $rowKey => $value) {
                if (strcasecmp(trim($rowKey), trim($key)) === 0) {
                    return trim($value ?? '');
                }
            }
        }
        return null;
    }

    private function cleanNumeric($value)
    {
        if (empty($value)) {
            return null;
        }
        if (is_numeric($value)) {
            return (float)$value;
        }
        // Remove non-numeric characters except decimal point and minus sign
        $cleaned = preg_replace('/[^0-9.-]/', '', (string)$value);
        return $cleaned !== '' && $cleaned !== '-' ? (float)$cleaned : null;
    }
}

