<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Eskul extends Model
{
    use HasFactory;

    protected $table = 'eskuls';
    protected $guarded = [];

    /**
     * Relasi Many-to-Many ke Siswa (Anggota Eskul).
     * Tabel Pivot: eskul_siswa
     */
    public function siswas(): BelongsToMany
    {
        // Parameter: Model Tujuan, Nama Tabel Pivot, FK Model Ini, FK Model Tujuan
        return $this->belongsToMany(Siswa::class, 'eskul_siswa', 'eskul_id', 'siswa_id')
            ->withTimestamps();
    }

    /**
     * Relasi Many-to-Many ke Guru (Pembina).
     * Tabel Pivot: pembina_eskul
     */
    public function pembinas(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'pembina_eskul', 'eskul_id', 'guru_id')
            ->using(PembinaEskul::class)
            ->withTimestamps();
    }
}
