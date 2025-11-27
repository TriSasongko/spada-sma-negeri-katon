<?php

namespace App\Http\Controllers;

use App\Models\JawabanKuis;
use App\Models\Kuis;
use App\Models\Modul;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    // ... method index, show, uploadTugas TETAP ADA (JANGAN DIHAPUS) ...
    public function index()
    {
        $user = Auth::user();

        if (!$user->siswa) {
            abort(403, 'Profil siswa tidak ditemukan.');
        }

        $kelasId = $user->siswa->kelas_id;

        $moduls = Modul::with(['mapel', 'guru.user'])
            ->where('kelas_id', $kelasId)
            ->where('status', 'published')
            ->latest('publish_at')
            ->get();

        return view('siswa.dashboard', compact('moduls', 'user'));
    }

    public function show(Modul $modul)
    {
        $siswa = Auth::user()->siswa;
        if ($modul->kelas_id !== $siswa->kelas_id) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        $modul->load([
            'materis',
            'tugas.pengumpulan' => function($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            },
            // Load kuis beserta jawaban siswa ini (untuk cek sudah dikerjakan/belum)
            'kuis.jawabanSiswa' => function($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            },
            'guru.user'
        ]);

        return view('siswa.modul.show', compact('modul'));
    }

    public function uploadTugas(Request $request, Tugas $tugas)
    {
        // ... (kode upload tugas tetap sama seperti sebelumnya)
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,jpg,png|max:10240',
            'catatan' => 'nullable|string',
        ]);

        $siswa = Auth::user()->siswa;

        if ($tugas->deadline && now()->greaterThan($tugas->deadline)) {
            return back()->with('error', 'Maaf, waktu pengumpulan tugas sudah habis.');
        }

        $path = $request->file('file')->store('tugas-siswa', 'public');

        PengumpulanTugas::updateOrCreate(
            [
                'tugas_id' => $tugas->id,
                'siswa_id' => $siswa->id,
            ],
            [
                'file_path' => $path,
                'catatan_siswa' => $request->catatan,
                'tanggal_dikumpulkan' => now(),
            ]
        );

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }

    // --- FITUR BARU: MENGERJAKAN KUIS ---

    public function kerjakanKuis(Kuis $kuis)
    {
        $siswa = Auth::user()->siswa;

        // 1. Cek apakah kuis milik kelas siswa
        if ($kuis->modul->kelas_id !== $siswa->kelas_id) {
            abort(403, 'Akses ditolak.');
        }

        // 2. Cek apakah siswa SUDAH pernah mengerjakan
        $sudahMengerjakan = JawabanKuis::where('kuis_id', $kuis->id)
            ->where('siswa_id', $siswa->id)
            ->exists();

        if ($sudahMengerjakan) {
            return redirect()->route('siswa.modul.show', $kuis->modul_id)
                ->with('error', 'Anda sudah mengerjakan kuis ini sebelumnya.');
        }

        return view('siswa.kuis.kerjakan', compact('kuis', 'siswa'));
    }

    public function submitKuis(Request $request, Kuis $kuis)
    {
        $siswa = Auth::user()->siswa;

        // Validasi input jawaban (array)
        $request->validate([
            'jawaban' => 'array',
        ]);

        $jawabanInput = $request->input('jawaban', []);
        $totalSkor = 0;
        $totalSoalPG = 0;

        foreach ($kuis->soals as $soal) {
            $jawabanSiswa = $jawabanInput[$soal->id] ?? null;
            $skor = 0;

            // Logika Penilaian Otomatis (Hanya PG)
            if ($soal->tipe === 'pilihan_ganda') {
                $totalSoalPG++;
                // Bandingkan jawaban siswa dengan kunci jawaban
                if ($jawabanSiswa && $jawabanSiswa == $soal->kunci_jawaban) {
                    $skor = 1; // 1 poin benar (sementara, nanti dihitung persentase)
                }
            }

            // Simpan Jawaban ke Database
            JawabanKuis::create([
                'kuis_id' => $kuis->id,
                'siswa_id' => $siswa->id,
                'soal_id' => $soal->id,
                'jawaban_siswa' => $jawabanSiswa ?? '-', // Simpan teks jawaban
                'skor' => $skor, // 1 jika benar, 0 jika salah
            ]);

            $totalSkor += $skor;
        }

        // Hitung Nilai Akhir (Skala 100) untuk PG
        // Rumus: (Benar / Total Soal PG) * 100
        // Essay belum dihitung di sini, harus manual guru.
        $nilaiAkhir = ($totalSoalPG > 0) ? round(($totalSkor / $totalSoalPG) * 100) : 0;

        return redirect()->route('siswa.modul.show', $kuis->modul_id)
            ->with('success', 'Kuis berhasil dikumpulkan! Nilai Pilihan Ganda Anda: ' . $nilaiAkhir);
    }
}
