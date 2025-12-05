<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PembinaEskul extends Pivot
{
    protected $table = 'pembina_eskul';
    protected $guarded = [];

    /**
     * Relasi ke model Guru
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    /**
     * Relasi ke model Eskul
     */
    public function eskul(): BelongsTo
    {
        return $this->belongsTo(Eskul::class);
    }
}
