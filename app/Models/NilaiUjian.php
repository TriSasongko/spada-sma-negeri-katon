<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiUjian extends Model
{
    use HasFactory;

    // Agar semua kolom bisa diisi (mass assignment)
    protected $guarded = [];

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke Mata Pelajaran
    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    // Relasi ke Tahun Ajaran (Opsional, tapi ada di script Resource)
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
