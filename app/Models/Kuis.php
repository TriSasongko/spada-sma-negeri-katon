<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuis extends Model
{
    use HasFactory;

    protected $table = 'kuis'; // Memaksa nama tabel

    protected $guarded = [];

    public function modul()
    {
        return $this->belongsTo(Modul::class);
    }

    public function soals()
    {
        return $this->hasMany(SoalKuis::class);
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanKuis::class);
    }
}
