<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pengumpulanTugas()
    {
        // Pastikan nama tabel benar
        return $this->hasMany(PengumpulanTugas::class, 'siswa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Helper untuk mengambil mapel berdasarkan kelas siswa
    // Nanti berguna di dashboard siswa
}
