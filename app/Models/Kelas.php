<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kelas extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit
    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'jurusan',
    ];

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    public function moduls(): HasMany
    {
        return $this->hasMany(Modul::class);
    }

    public function gurus(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'guru_kelas', 'kelas_id', 'guru_id');
    }

    // Relasi untuk mengambil data Wali Kelas
    public function waliKelas(): HasOne
    {
        return $this->hasOne(WaliKelas::class);
    }

    /**
     * RELASI BARU (WAJIB ADA)
     * Untuk fitur Kelola Jadwal Manual.
     * Menghubungkan Kelas ke tabel jadwal_pelajarans.
     */
    public function jadwals(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'kelas_id');
    }
}