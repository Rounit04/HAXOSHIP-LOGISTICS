<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'title',
        'data',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public static function getUnreadCount()
    {
        return static::where('read', false)->count();
    }

    /**
     * Get latest notifications
     */
    public static function getLatest($limit = 10)
    {
        return static::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notifications count for a specific user
     */
    public static function getUnreadCountForUser($userId)
    {
        return static::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Get latest notifications for a specific user
     */
    public static function getLatestForUser($userId, $limit = 10)
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
