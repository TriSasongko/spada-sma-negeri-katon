<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanKuis extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit (opsional, tapi aman)
    protected $table = 'jawaban_kuis';

    // MEMPERBAIKI ERROR MASS ASSIGNMENT
    // guarded = [] artinya "tidak ada kolom yang dilarang diisi",
    // sehingga kuis_id, siswa_id, dll bisa disimpan via create()
    protected $guarded = [];

    // Relasi ke Kuis
    public function kuis()
    {
        return $this->belongsTo(Kuis::class);
    }

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke Soal
    public function soal()
    {
        return $this->belongsTo(SoalKuis::class);
    }
}
