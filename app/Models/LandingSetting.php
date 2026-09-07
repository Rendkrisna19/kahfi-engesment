<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting value by key with optional fallback.
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting && $setting->value !== null ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value, string $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    /**
     * Get all settings as key => value associative array.
     */
    public static function allKeyValues(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Get the brand logo URL or null if not set.
     */
    public static function brandLogoUrl(): ?string
    {
        $logo = static::get('brand_logo');
        if (!$logo) {
            return null;
        }

        if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            return $logo;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($logo)
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($logo)
            : asset($logo);
    }
}
