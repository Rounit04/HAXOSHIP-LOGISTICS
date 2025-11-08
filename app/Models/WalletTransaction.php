<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'reference_id',
        'description',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * Get the wallet that owns the transaction
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the user that owns the transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Approve pending withdrawal
     */
    public function approve()
    {
        if ($this->type !== 'withdrawal' || $this->status !== 'pending') {
            throw new \Exception('Only pending withdrawals can be approved');
        }

        $this->wallet->balance = $this->balance_after;
        $this->wallet->save();

        $this->status = 'completed';
        $this->save();

        return $this;
    }

    /**
     * Reject pending withdrawal
     */
    public function reject($notes = null)
    {
        if ($this->type !== 'withdrawal' || $this->status !== 'pending') {
            throw new \Exception('Only pending withdrawals can be rejected');
        }

        $this->status = 'cancelled';
        if ($notes) {
            $this->notes = $notes;
        }
        $this->save();

        return $this;
    }
}
