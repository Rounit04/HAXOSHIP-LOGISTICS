<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class AdminTodo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'note',
        'remind_at',
        'reminder_sent_at',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    protected $appends = [
        'remind_at_display',
        'completed_at_display',
    ];

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeDueForReminder($query)
    {
        return $query->whereNotNull('remind_at')
            ->where('remind_at', '<=', now())
            ->whereNull('reminder_sent_at');
    }

    public function markCompleted(): void
    {
        $this->is_completed = true;
        $this->completed_at = now();
        $this->save();
    }

    public function markPending(): void
    {
        $this->is_completed = false;
        $this->completed_at = null;
        $this->save();
    }

    public function markReminderSent(): void
    {
        $this->reminder_sent_at = now();
        $this->save();
    }

    public function getRemindAtDisplayAttribute(): ?string
    {
        return $this->remind_at ? $this->remind_at->toDayDateTimeString() : null;
    }

    public function getCompletedAtDisplayAttribute(): ?string
    {
        return $this->completed_at ? $this->completed_at->diffForHumans() : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
