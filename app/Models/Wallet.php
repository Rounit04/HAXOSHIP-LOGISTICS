<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get the user that owns the wallet
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transactions for this wallet
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get or create wallet for user
     */
    public static function getOrCreateWallet($userId)
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'currency' => 'USD']
        );
    }

    /**
     * Add amount to wallet
     */
    public function deposit($amount, $description = null, $referenceId = null)
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->save();

        WalletTransaction::create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'status' => 'completed',
            'reference_id' => $referenceId,
            'description' => $description,
        ]);

        return $this;
    }

    /**
     * Withdraw amount from wallet
     */
    public function withdraw($amount, $description = null, $referenceId = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient wallet balance');
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->save();

        WalletTransaction::create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'status' => 'completed',
            'reference_id' => $referenceId,
            'description' => $description,
        ]);

        return $this;
    }

    /**
     * Create pending withdrawal request
     */
    public function requestWithdrawal($amount, $description = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient wallet balance');
        }

        $balanceBefore = $this->balance;
        $balanceAfter = $this->balance - $amount;

        $transaction = WalletTransaction::create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'status' => 'pending',
            'reference_id' => 'WDR-' . time() . '-' . $this->user_id,
            'description' => $description,
        ]);

        return $transaction;
    }
}
