<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $kuis->judul }}
        </h2>
        <div class="text-sm text-gray-500 mt-1">
            Durasi: {{ $kuis->durasi_menit }} Menit | Jumlah Soal: {{ $kuis->soals->count() }}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Instruksi -->
            @if($kuis->instruksi)
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                {!! $kuis->instruksi !!}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('siswa.kuis.submit', $kuis->id) }}" method="POST" id="form-kuis">
                @csrf

                <div class="space-y-6">
                    @foreach($kuis->soals as $index => $soal)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 bg-gray-200 h-8 w-8 flex items-center justify-center rounded-full text-gray-600 font-bold mr-4">
                                    {{ $index + 1 }}
                                </span>
                                <div class="w-full">
                                    <!-- Pertanyaan -->
                                    <div class="prose max-w-none text-gray-800 font-medium mb-4">
                                        {!! $soal->pertanyaan !!}
                                    </div>

                                    <!-- Opsi Jawaban -->
                                    @if($soal->tipe == 'pilihan_ganda')
                                        <div class="space-y-3 mt-4">
                                            @foreach($soal->opsi_jawaban as $key => $value)
                                                <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                                    <input type="radio"
                                                           name="jawaban[{{ $soal->id }}]"
                                                           value="{{ $key }}"
                                                           class="form-radio h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                    <span class="text-gray-900 flex-1">
                                                        <span class="font-bold mr-2 text-gray-500">{{ $key }}.</span> {{ $value }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <!-- Isian Essay -->
                                        <textarea name="jawaban[{{ $soal->id }}]"
                                                  rows="4"
                                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                  placeholder="Tulis jawaban Anda di sini..."></textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tombol Submit -->
                <div class="mt-8 flex justify-end">
                    <button type="submit"
                            onclick="return confirm('Apakah Anda yakin ingin mengumpulkan jawaban? Pastikan semua soal telah terisi.')"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Kirim Jawaban
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
