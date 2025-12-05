<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ekstrakurikuler extends Model
{
    protected $guarded = [];

    // Relasi untuk Guru Pembina (BelongsTo)
    public function pembina(): BelongsTo
    {
        // Asumsi Guru Pembina adalah Model User
        return $this->belongsTo(User::class, 'guru_pembina_id');
    }

    // Relasi untuk Anggota Siswa (Many-to-Many)
    public function anggota(): BelongsToMany
    {
        // Asumsi nama tabel pivot: anggota_ekstrakurikuler
        return $this->belongsToMany(Siswa::class, 'anggota_ekstrakurikuler', 'ekstrakurikuler_id', 'siswa_id');
    }
}
