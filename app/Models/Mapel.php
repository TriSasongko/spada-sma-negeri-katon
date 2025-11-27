<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_mapel');
    }

    public function moduls()
    {
        return $this->hasMany(Modul::class);
    }
}
