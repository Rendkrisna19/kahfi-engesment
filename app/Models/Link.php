<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'kategori_konten_id',
        'kategori_creator_id',
        'url',
        'username',
        'platform',
        'caption',
        'tanggal_upload',
        'views',
        'likes',
        'comments',
        'saves',
        'shares',
        'reposts',
        'engagement_rate',
        'saw_score',
        'prev_views',
        'prev_likes',
        'prev_comments',
        'prev_shares',
        'prev_saves',
        'prev_engagement_rate',
        'prev_saw_score',
        'last_rescraped_at',
        'status_scraping',
        'updated_at',
    ];

    public const CREATED_AT = null;
    public const UPDATED_AT = 'updated_at';

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function kategoriKonten()
    {
        return $this->belongsTo(KategoriKonten::class, 'kategori_konten_id');
    }

    public function kategoriCreator()
    {
        return $this->belongsTo(KategoriCreator::class, 'kategori_creator_id');
    }
}
