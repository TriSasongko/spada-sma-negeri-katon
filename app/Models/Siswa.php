<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Jangan lupa import ini

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi Many-to-Many ke Eskul.
     * Siswa bisa mengikuti banyak Eskul.
     */
    public function eskuls(): BelongsToMany
    {
        return $this->belongsToMany(Eskul::class, 'eskul_siswa', 'siswa_id', 'eskul_id')
            ->withTimestamps();
    }

    public function pengumpulanTugas()
    {
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
}