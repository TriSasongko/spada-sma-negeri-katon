<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $modul->judul }}
            </h2>
            <span class="text-sm text-gray-500">{{ $modul->mapel->nama }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Sukses/Error -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- 1. Deskripsi Modul -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Deskripsi Pembelajaran</h3>
                <div class="prose max-w-none text-gray-700">
                    {!! $modul->deskripsi !!}
                </div>
            </div>

            <!-- 2. Materi Pembelajaran -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Materi Pembelajaran
                </h3>
                <div class="space-y-4">
                    @forelse($modul->materis as $materi)
                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded border">
                            <div class="flex items-center">
                                @if($materi->tipe == 'pdf')
                                    <span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">PDF</span>
                                @elseif($materi->tipe == 'video')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">VIDEO</span>
                                @elseif($materi->tipe == 'link')
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">LINK</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">FILE</span>
                                @endif
                                <span class="font-medium text-gray-700">{{ $materi->judul }}</span>
                            </div>

                            <div>
                                @if($materi->tipe == 'link')
                                    <a href="{{ $materi->url }}" target="_blank" class="text-indigo-600 hover:underline text-sm font-semibold">Buka Link &rarr;</a>
                                @elseif($materi->tipe == 'video' && $materi->url)
                                    <a href="{{ $materi->url }}" target="_blank" class="text-indigo-600 hover:underline text-sm font-semibold">Tonton Video &rarr;</a>
                                @else
                                    <a href="{{ Storage::url($materi->file_path) }}" target="_blank" class="text-indigo-600 hover:underline text-sm font-semibold">Download / Lihat &rarr;</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic text-sm">Belum ada materi yang diunggah.</p>
                    @endforelse
                </div>
            </div>

            <!-- 3. Tugas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    Tugas & Pengumpulan
                </h3>

                <div class="space-y-8">
                    @forelse($modul->tugas as $tugas)
                        @php
                            $pengumpulan = $tugas->pengumpulan->first();
                            $isSubmitted = $pengumpulan ? true : false;
                        @endphp
                        <div class="border rounded-lg p-4 {{ $isSubmitted ? 'bg-green-50 border-green-200' : 'bg-white' }}">
                            <div class="mb-2">
                                <h4 class="font-bold text-lg">{{ $tugas->judul }}</h4>
                                <div class="text-sm text-gray-500 mb-2">
                                    Deadline: <span class="font-semibold text-red-600">{{ $tugas->deadline ? $tugas->deadline->format('d M Y, H:i') : 'Tidak ada' }}</span>
                                </div>
                                <div class="prose prose-sm text-gray-600 bg-gray-50 p-3 rounded">
                                    {!! $tugas->instruksi !!}
                                </div>
                            </div>

                            <!-- Status Pengumpulan -->
                            <div class="mt-4 border-t pt-4">
                                @if($isSubmitted)
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Sudah Dikumpulkan
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">Dikirim pada: {{ $pengumpulan->tanggal_dikumpulkan }}</p>
                                            @if($pengumpulan->nilai !== null)
                                                <p class="text-sm font-bold text-blue-600 mt-1">Nilai: {{ $pengumpulan->nilai }} / 100</p>
                                                <p class="text-xs text-gray-600 italic">"{{ $pengumpulan->komentar_guru }}"</p>
                                            @else
                                                <p class="text-xs text-gray-400 italic mt-1">Menunggu penilaian guru...</p>
                                            @endif
                                        </div>
                                        <a href="{{ Storage::url($pengumpulan->file_path) }}" target="_blank" class="text-sm text-blue-600 underline">Lihat File Saya</a>
                                    </div>
                                @else
                                    <form action="{{ route('siswa.tugas.upload', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700">Upload Jawaban (PDF/Doc/Gambar)</label>
                                            <input type="file" name="file" required class="mt-1 block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100
                                            "/>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                            <textarea name="catatan" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                        </div>
                                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Kirim Tugas
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic text-sm">Tidak ada tugas pada modul ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- 4. Kuis & Ujian (Bagian Ini yang Mengontrol Alur Kuis) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Kuis & Ujian
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($modul->kuis as $kuis)
                        @php
                            // Cek apakah sudah ada jawaban dari siswa ini
                            // Relasi 'jawabanSiswa' sudah di-load di Controller
                            $sudahDikerjakan = $kuis->jawabanSiswa->isNotEmpty();

                            // Hitung nilai (khusus PG) untuk ditampilkan sekilas
                            $nilai = 0;
                            if($sudahDikerjakan) {
                                $totalSoalPG = $kuis->soals->where('tipe', 'pilihan_ganda')->count();
                                $benar = $kuis->jawabanSiswa->sum('skor');
                                // Hindari pembagian dengan nol
                                $nilai = ($totalSoalPG > 0) ? round(($benar / $totalSoalPG) * 100) : 0;
                            }
                        @endphp

                        <div class="border {{ $sudahDikerjakan ? 'border-green-200 bg-green-50' : 'border-purple-200 bg-purple-50' }} p-4 rounded-lg">
                            <h4 class="font-bold text-gray-800">{{ $kuis->judul }}</h4>
                            <p class="text-sm text-gray-600 mb-2">Durasi: {{ $kuis->durasi_menit }} Menit</p>

                            @if($sudahDikerjakan)
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Selesai Dikerjakan
                                    </span>
                                    <p class="text-sm font-bold text-gray-700 mt-1">Nilai Pilihan Ganda: {{ $nilai }}</p>
                                </div>
                            @else
                                <a href="{{ route('siswa.kuis.kerjakan', $kuis->id) }}"
                                   class="inline-block mt-2 bg-purple-600 text-white text-sm px-4 py-2 rounded hover:bg-purple-700 transition">
                                    Mulai Kuis
                                </a>
                            @endif
                        </div>
                    @empty
                         <p class="text-gray-500 italic text-sm">Tidak ada kuis pada modul ini.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
