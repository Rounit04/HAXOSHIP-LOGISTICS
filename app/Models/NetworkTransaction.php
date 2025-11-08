<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkTransaction extends Model
{
    protected $fillable = [
        'network_id',
        'booking_id',
        'awb_no',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'transaction_type',
        'description',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * Get the network that owns this transaction
     */
    public function network()
    {
        return $this->belongsTo(Network::class);
    }
}
