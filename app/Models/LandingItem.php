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

        return Storage::disk('public')->exists($this->image)
            ? Storage::disk('public')->url($this->image)
            : asset($this->image);
    }
}
