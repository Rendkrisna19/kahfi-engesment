<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriCreator extends Model
{
    use HasFactory;

    protected $table = 'kategori_creator';
    protected $fillable = ['nama'];

    public function links()
    {
        return $this->hasMany(Link::class, 'kategori_creator_id');
    }
}
