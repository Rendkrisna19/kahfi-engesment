<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LandingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'subtitle',
        'description',
        'image',
        'link',
        'extra_meta',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'extra_meta' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        $cleanPath = ltrim($this->image, '/\\');

        // 1. Direct file in public path (e.g. uploads/landing/...)
        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        // 2. File in public/storage/...
        if (file_exists(public_path('storage/' . $cleanPath))) {
            return asset('storage/' . $cleanPath);
        }

        // 3. File in storage/app/public/...
        if (file_exists(storage_path('app/public/' . $cleanPath))) {
            return url('storage/' . $cleanPath);
        }

        // 4. Storage disk check
        if (Storage::disk('public')->exists($cleanPath)) {
            return Storage::disk('public')->url($cleanPath);
        }

        return asset($cleanPath);
    }
}
