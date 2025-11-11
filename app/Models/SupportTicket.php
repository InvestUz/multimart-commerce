<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'user_id', 'order_id', 'subject',
        'category', 'priority', 'status', 'description',
        'attachments', 'assigned_to', 'resolved_at', 'closed_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function assignTo(User $user): void
    {
        $this->update(['assigned_to' => $user->id]);
    }

    public function resolve(): void
    {
        $this->update(['status' => 'resolved', 'resolved_at' => now()]);
    }

    public function close(): void
    {
        $this->update(['status' => 'closed', 'closed_at' => now()]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function addReply(User $user, string $message, ?array $attachments = null): void
    {
        SupportTicketReply::create([
            'ticket_id' => $this->id,
            'user_id' => $user->id,
            'message' => $message,
            'attachments' => $attachments,
            'is_staff_reply' => $user->isSuperAdmin() || $user->isVendor(),
        ]);
    }
}
