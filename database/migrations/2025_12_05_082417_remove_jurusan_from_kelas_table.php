<?php

// ...
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Menghapus kolom 'jurusan'
            $table->dropColumn('jurusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Menambahkan kembali kolom 'jurusan' (misalnya, sebagai string)
            // Sesuaikan tipe data ini dengan tipe data sebelumnya!
            $table->string('jurusan')->nullable()->after('nama');
        });
    }
};
