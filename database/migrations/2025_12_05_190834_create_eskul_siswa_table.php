<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan nama tabel di sini adalah 'eskul_siswa'
        Schema::create('eskul_siswa', function (Blueprint $table) {
            $table->id();

            // Foreign Key ke tabel 'eskuls'
            // Pastikan nama tabel di database Anda benar 'eskuls'
            $table->foreignId('eskul_id')
                  ->constrained('eskuls')
                  ->cascadeOnDelete();

            // Foreign Key ke tabel 'siswas'
            $table->foreignId('siswa_id')
                  ->constrained('siswas')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eskul_siswa');
    }
};
