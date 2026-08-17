<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKonten extends Model
{
    use HasFactory;

    protected $table = 'kategori_konten';
    protected $fillable = ['nama'];

    public function links()
    {
        return $this->hasMany(Link::class, 'kategori_konten_id');
    }
}
