<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Eskul extends Model
{
    use HasFactory;

    protected $table = 'eskuls';
    protected $guarded = [];

    /**
     * Relasi Many-to-Many ke Guru melalui tabel pivot pembina_eskul.
     * Menggunakan model PembinaEskul sebagai model pivot.
     */
    public function pembinas(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'pembina_eskul', 'eskul_id', 'guru_id')
            ->using(PembinaEskul::class)
            ->withTimestamps();
    }
}
