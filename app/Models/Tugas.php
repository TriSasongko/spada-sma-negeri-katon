<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas'; // Memaksa nama tabel (laravel biasanya mencari 'tugases')

    protected $guarded = [];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function modul()
    {
        return $this->belongsTo(Modul::class);
    }

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }
}
