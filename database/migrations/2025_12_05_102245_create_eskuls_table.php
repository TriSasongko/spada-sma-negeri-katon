<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel 16. Daftar Ekstrakurikuler
        Schema::create('eskuls', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // Contoh: Futsal, Pramuka, KIR
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['Olahraga', 'Seni', 'Akademik', 'Lainnya'])->default('Lainnya');
            $table->timestamps();
        });

        // Tabel 17. Pivot Pembina Eskul (Guru - Eskul)
        Schema::create('pembina_eskul', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eskul_id')->constrained('eskuls')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->date('tanggal_mulai')->nullable();

            // Constraint unik: Satu guru hanya bisa menjadi pembina satu kali untuk eskul tertentu
            $table->unique(['eskul_id', 'guru_id']);
            $table->timestamps();
        });

        // Opsi: Jika Siswa dapat mendaftar ke Eskul (opsional)
        // Schema::create('siswa_eskul', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('eskul_id')->constrained('eskuls')->onDelete('cascade');
        //     $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
        //     $table->timestamps();
        // });
    }

    public function down(): void
    {
        // Schema::dropIfExists('siswa_eskul');
        Schema::dropIfExists('pembina_eskul');
        Schema::dropIfExists('eskuls');
    }
};
