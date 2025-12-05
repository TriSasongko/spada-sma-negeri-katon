<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wali_kelas', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel gurus
            $table->foreignId('guru_id')
                ->constrained('gurus')
                ->onDelete('cascade');

            // Relasi ke tabel kelas
            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->onDelete('cascade');

            // Satu kelas hanya boleh punya satu wali kelas
            $table->unique(['kelas_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_kelas');
    }
};
