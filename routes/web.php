<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

// Redirect dashboard berdasarkan Role
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->hasRole('admin') || $user->hasRole('guru')) {
        return redirect('/admin');
    }

    return redirect()->route('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- TAMBAHAN: RUTE PROFIL (WAJIB ADA KARENA DIPANGGIL DI LAYOUT) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Group Route Khusus Siswa
Route::middleware(['auth', 'role:siswa'])->group(function () {

    // Dashboard Utama Siswa
    Route::get('/siswa/dashboard', [SiswaController::class, 'index'])->name('siswa.dashboard');

    // Halaman Detail Modul (Belajar)
    Route::get('/siswa/modul/{modul}', [SiswaController::class, 'show'])->name('siswa.modul.show');

    // Upload Tugas
    Route::post('/siswa/tugas/{tugas}/upload', [SiswaController::class, 'uploadTugas'])->name('siswa.tugas.upload');

    // --- FITUR KUIS ---

    // 1. Halaman Mengerjakan Kuis (GET)
    Route::get('/siswa/kuis/{kuis}/kerjakan', [SiswaController::class, 'kerjakanKuis'])
        ->name('siswa.kuis.kerjakan');

    // 2. Submit Jawaban Kuis (POST)
    Route::post('/siswa/kuis/{kuis}/submit', [SiswaController::class, 'submitKuis'])
        ->name('siswa.kuis.submit');
});

require __DIR__.'/auth.php';
