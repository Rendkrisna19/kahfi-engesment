<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'nama_campaign',
        'platform',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function userAccess()
    {
        return $this->hasMany(UserCampaignAccess::class);
    }
}
