<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'author',
        'views',
        'published_at',
        'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }
        return null;
    }

    public function getFormattedDateAttribute()
    {
        return $this->published_at ? $this->published_at->format('d M Y') : $this->created_at->format('d M Y');
    }
}
