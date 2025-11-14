<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use App\Models\AwbUpload;
use App\Models\Network;

class WalletTransactionsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $walletTransactions = session('wallet_transactions', []);
        if (!is_array($walletTransactions)) {
            $walletTransactions = [];
        }

        // Get all AWBs for validation (from database first, then session)
        $dbAwbUploads = AwbUpload::pluck('awb_no')->toArray();
        $sessionAwbUploads = collect(session('awb_uploads', []))->pluck('awb_no')->toArray();
        $validAwbNumbers = array_merge($dbAwbUploads, $sessionAwbUploads);
        $validAwbNumbers = array_unique($validAwbNumbers);

        // Get all networks for validation (from database first, then session)
        $dbNetworks = Network::pluck('name')->toArray();
        $sessionNetworks = collect($this->getSessionNetworks())->pluck('name')->toArray();
        $validNetworks = array_merge($dbNetworks, $sessionNetworks);
        $validNetworks = array_unique($validNetworks);

        $maxId = count($walletTransactions) > 0 ? max(array_column($walletTransactions, 'id')) : 0;

        foreach ($collection as $row) {
            $awbNumber = $this->getValue($row, ['awb_number', 'awb number', 'awb_no', 'awb']);
            if (empty($awbNumber)) {
                continue; // Skip rows without AWB number
            }

            // Validate AWB exists
            if (!in_array($awbNumber, $validAwbNumbers)) {
                continue; // Skip if AWB doesn't exist
            }

            $network = $this->getValue($row, ['network', 'network_name']);
            if (empty($network) || !in_array($network, $validNetworks)) {
                continue; // Skip invalid networks
            }

            $transactionType = $this->getValue($row, ['transaction_type', 'transaction type', 'type']);
            if (empty($transactionType)) {
                continue; // Skip rows without transaction type
            }

            $mode = $this->getValue($row, ['mode', 'mode_of_payment', 'mode of payment']);
            if (!in_array($mode, ['UPI', 'Cash', 'Netf'])) {
                continue; // Skip invalid payment modes
            }

            $type = $this->getValue($row, ['type', 'transaction_type_type']);
            if (!in_array($type, ['Credit', 'Debit'])) {
                continue; // Skip invalid types
            }

            $amount = $this->cleanNumeric($this->getValue($row, ['amount']));
            if ($amount === null || $amount < 0) {
                continue; // Skip invalid amounts
            }

            $remark = $this->getValue($row, ['remark', 'remarks', 'note', 'notes']) ?? '';

            $maxId++;
            $walletTransactions[] = [
                'id' => $maxId,
                'awb_number' => $awbNumber,
                'network' => $network,
                'transaction_type' => $transactionType,
                'mode' => $mode,
                'type' => $type,
                'amount' => $amount,
                'remark' => $remark,
                'created_at' => now()->toDateTimeString(),
            ];
        }

        session(['wallet_transactions' => $walletTransactions]);
        session()->save();
    }

    private function getSessionNetworks()
    {
        if (session()->has('networks')) {
            return session('networks');
        }
        return [];
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

