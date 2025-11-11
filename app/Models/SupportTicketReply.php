<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'user_id', 'message',
        'attachments', 'is_staff_reply',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_staff_reply' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByTicket($query, $ticketId)
    {
        return $query->where('ticket_id', $ticketId);
    }

    public function scopeStaffReplies($query)
    {
        return $query->where('is_staff_reply', true);
    }

    public function scopeCustomerReplies($query)
    {
        return $query->where('is_staff_reply', false);
    }
}
