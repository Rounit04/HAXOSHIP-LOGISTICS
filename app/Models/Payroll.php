<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'salary_amount',
        'period_start',
        'period_end',
        'generation_type',
        'status',
        'remarks',
        'generated_at',
        'paid_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'salary_amount' => 'decimal:2',
        'generated_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the user that owns the payroll
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
