<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $fillable = [
        'name',
        'type',
        'opening_balance',
        'status',
        'bank_details',
        'upi_scanner',
        'remark',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
    ];

    /**
     * Get all transactions for this network
     */
    public function transactions()
    {
        return $this->hasMany(NetworkTransaction::class);
    }

    /**
     * Get current balance (opening balance + all credits - all debits)
     */
    public function getCurrentBalanceAttribute()
    {
        $credits = $this->transactions()->where('type', 'credit')->sum('amount');
        $debits = $this->transactions()->where('type', 'debit')->sum('amount');
        return $this->opening_balance + $credits - $debits;
    }

    /**
     * Get the last transaction balance
     */
    public function getLastBalance()
    {
        $lastTransaction = $this->transactions()->latest()->first();
        if ($lastTransaction) {
            return $lastTransaction->balance_after;
        }
        return $this->opening_balance;
    }

    /**
     * Credit amount to network
     */
    public function credit($amount, $bookingId = null, $awbNo = null, $transactionType = 'booking', $description = null, $notes = null)
    {
        $balanceBefore = $this->getLastBalance();
        $balanceAfter = $balanceBefore + $amount;

        return NetworkTransaction::create([
            'network_id' => $this->id,
            'booking_id' => $bookingId,
            'awb_no' => $awbNo,
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'transaction_type' => $transactionType,
            'description' => $description ?? "Credit of ₹{$amount}",
            'notes' => $notes,
        ]);
    }

    /**
     * Debit amount from network
     */
    public function debit($amount, $bookingId = null, $awbNo = null, $transactionType = 'booking', $description = null, $notes = null)
    {
        $balanceBefore = $this->getLastBalance();
        $balanceAfter = $balanceBefore - $amount;

        return NetworkTransaction::create([
            'network_id' => $this->id,
            'booking_id' => $bookingId,
            'awb_no' => $awbNo,
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'transaction_type' => $transactionType,
            'description' => $description ?? "Debit of ₹{$amount}",
            'notes' => $notes,
        ]);
    }
}
