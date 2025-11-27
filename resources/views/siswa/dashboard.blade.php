<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa') }} - Kelas {{ Auth::user()->siswa->kelas->nama ?? '-' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Banner -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    Selamat datang, <strong>{{ Auth::user()->name }}</strong>!
                    <br>
                    Semangat belajar hari ini. Berikut adalah modul pembelajaran terbaru untukmu.
                </div>
            </div>

            <!-- Grid Modul -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($moduls as $modul)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition duration-300 border border-gray-100">
                        <div class="p-6">
                            <!-- Badge Mapel -->
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-2">
                                {{ $modul->mapel->nama }}
                            </span>

                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                <a href="{{ route('siswa.modul.show', $modul->id) }}" class="hover:text-blue-600">
                                    {{ $modul->judul }}
                                </a>
                            </h3>

                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                                {!! strip_tags($modul->deskripsi) !!}
                            </p>

                            <div class="flex items-center justify-between mt-4 border-t pt-4">
                                <div class="text-xs text-gray-500">
                                    <div class="font-semibold">{{ $modul->guru->user->name ?? 'Guru' }}</div>
                                    <!-- PERBAIKAN: Menggunakan pengecekan null pada publish_at -->
                                    <div>
                                        {{ $modul->publish_at ? $modul->publish_at->format('d M Y') : $modul->created_at->format('d M Y') }}
                                    </div>
                                </div>
                                <a href="{{ route('siswa.modul.show', $modul->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Buka Modul
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-10 text-gray-500">
                        Belum ada modul yang dipublish untuk kelasmu.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
