<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'country', 'state', 'city',
        'rate', 'type', 'is_active', 'priority',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation($query, $country, $state = null, $city = null)
    {
        $query->where('country', $country);

        if ($state) {
            $query->where(function($q) use ($state) {
                $q->whereNull('state')->orWhere('state', $state);
            });
        }

        if ($city) {
            $query->where(function($q) use ($city) {
                $q->whereNull('city')->orWhere('city', $city);
            });
        }

        return $query;
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    public function calculateTax($amount): float
    {
        if ($this->type === 'percentage') {
            return ($amount * $this->rate) / 100;
        }
        return $this->rate;
    }

    public static function calculateTaxForLocation($amount, $country, $state = null, $city = null): float
    {
        $taxes = static::active()
            ->byLocation($country, $state, $city)
            ->ordered()
            ->get();

        $totalTax = 0;
        foreach ($taxes as $tax) {
            $totalTax += $tax->calculateTax($amount);
        }

        return $totalTax;
    }
}
