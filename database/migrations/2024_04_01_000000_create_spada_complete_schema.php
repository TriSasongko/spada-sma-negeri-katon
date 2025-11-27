<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tabel Tahun Ajaran
        Schema::create('tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->string('tahun'); // Contoh: 2024/2025
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 2. Tabel Kelas
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Contoh: X IPA 1
            $table->string('jurusan')->nullable(); // IPA/IPS/Bahasa
            $table->timestamps();
        });

        // 3. Tabel Mata Pelajaran
        Schema::create('mapels', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Contoh: Matematika Wajib
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 4. Tabel Guru (Profile)
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nip')->unique()->nullable();
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->timestamps();
        });

        // 5. Tabel Siswa (Profile)
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('nis')->unique()->nullable();
            $table->timestamps();
        });

        // 6. Pivot Guru - Mapel (Many to Many)
        // Satu guru bisa mengajar banyak mapel, satu mapel bisa diajar banyak guru
        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
            $table->timestamps();
        });

        // --- INTI SISTEM SPADA (MODUL BASED) ---

        // 7. Tabel Modul
        Schema::create('moduls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('publish_at')->nullable();
            $table->timestamps();
        });

        // 8. Tabel Materi (Isi Modul)
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('moduls')->onDelete('cascade');
            $table->string('judul');
            $table->enum('tipe', ['pdf', 'file', 'video', 'link']);
            $table->string('file_path')->nullable(); // Untuk PDF/File
            $table->text('url')->nullable(); // Untuk Video/Link
            $table->timestamps();
        });

        // 9. Tabel Tugas
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('moduls')->onDelete('cascade');
            $table->string('judul');
            $table->text('instruksi')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->timestamps();
        });

        // 10. Tabel Pengumpulan Tugas (Siswa)
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->text('catatan_siswa')->nullable();
            $table->integer('nilai')->nullable(); // 0-100
            $table->text('komentar_guru')->nullable();
            $table->timestamp('tanggal_dikumpulkan')->useCurrent();
            $table->timestamps();
        });

        // 11. Tabel Kuis
        Schema::create('kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('moduls')->onDelete('cascade');
            $table->string('judul');
            $table->text('instruksi')->nullable();
            $table->integer('durasi_menit')->default(60);
            $table->timestamps();
        });

        // 12. Tabel Soal Kuis
        Schema::create('soal_kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuis_id')->constrained('kuis')->onDelete('cascade');
            $table->enum('tipe', ['pilihan_ganda', 'essay']);
            $table->text('pertanyaan');
            // Menyimpan opsi jawaban (A,B,C,D,E) dalam format JSON jika PG
            $table->json('opsi_jawaban')->nullable();
            $table->string('kunci_jawaban')->nullable(); // A/B/C/D/E untuk PG
            $table->timestamps();
        });

        // 13. Jawaban Siswa (Kuis)
        Schema::create('jawaban_kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuis_id')->constrained('kuis')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('soal_id')->constrained('soal_kuis')->onDelete('cascade');
            $table->text('jawaban_siswa'); // Huruf (PG) atau Teks (Essay)
            $table->integer('skor')->default(0); // Diisi otomatis jika PG
            $table->timestamps();
        });

        // 14. Diskusi Modul
        Schema::create('diskusi_moduls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('moduls')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Bisa Guru atau Siswa
            $table->text('pesan');
            $table->foreignId('parent_id')->nullable()->constrained('diskusi_moduls')->onDelete('cascade'); // Untuk reply
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskusi_moduls');
        Schema::dropIfExists('jawaban_kuis');
        Schema::dropIfExists('soal_kuis');
        Schema::dropIfExists('kuis');
        Schema::dropIfExists('pengumpulan_tugas');
        Schema::dropIfExists('tugas');
        Schema::dropIfExists('materis');
        Schema::dropIfExists('moduls');
        Schema::dropIfExists('guru_mapel');
        Schema::dropIfExists('siswas');
        Schema::dropIfExists('gurus');
        Schema::dropIfExists('mapels');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('tahun_ajarans');
    }
};
