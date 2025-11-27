<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalKuis extends Model
{
    use HasFactory;

    protected $table = 'soal_kuis';

    protected $guarded = [];

    protected $casts = [
        'opsi_jawaban' => 'array', // Penting: Mengubah JSON di database menjadi Array PHP
    ];

    public function kuis()
    {
        return $this->belongsTo(Kuis::class);
    }
}
