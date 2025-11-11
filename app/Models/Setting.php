<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group'];

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public static function get($key, $default = null)
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->getCastedValue() : $default;
        });
    }

    public static function set($key, $value, $type = 'text', $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );

        Cache::forget("setting_{$key}");
    }

    public function getCastedValue()
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($this->value) ? (float) $this->value : $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    public static function clearCache(): void
    {
        Cache::flush();
    }
}
