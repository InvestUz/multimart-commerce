<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'name', 'is_active',
        'token', 'verified_at', 'unsubscribed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (empty($subscriber->token)) {
                $subscriber->token = Str::random(40);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    public function verify(): void
    {
        $this->update(['verified_at' => now(), 'is_active' => true]);
    }

    public function unsubscribe(): void
    {
        $this->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    public function resubscribe(): void
    {
        $this->update([
            'is_active' => true,
            'unsubscribed_at' => null,
        ]);
    }
}
