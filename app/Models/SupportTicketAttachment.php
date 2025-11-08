<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketAttachment extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    /**
     * Get the support ticket that owns the attachment.
     */
    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }
}
