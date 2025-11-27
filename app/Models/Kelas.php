<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit karena 'kelas' tidak mengikuti aturan plural bahasa Inggris
    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'jurusan',
    ];

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function moduls()
    {
        return $this->hasMany(Modul::class);
    }

    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_kelas', 'kelas_id', 'guru_id');
    }
}
