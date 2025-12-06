<x-filament-panels::page>
    <h2 class="text-xl font-bold mb-4 text-gray-700 dark:text-white">
        📅 Jadwal Kelas {{ $record->nama }}
    </h2>

    @php
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    @endphp

    <table class="w-full border rounded-lg overflow-hidden shadow bg-white dark:bg-gray-900 dark:border-gray-700">
        <thead class="bg-gray-200 dark:bg-gray-800 dark:text-gray-100 font-semibold">
            <tr>
                <th class="border dark:border-gray-700 p-3 text-left">Hari</th>
                <th class="border dark:border-gray-700 p-3 text-left">Jam</th>
                <th class="border dark:border-gray-700 p-3 text-left">Mata Pelajaran</th>
                <th class="border dark:border-gray-700 p-3 text-left">Guru</th>
            </tr>
        </thead>

        <tbody class="text-gray-700 dark:text-gray-200">
            @foreach ($hariList as $hari)
                @php
                    $data = $record->jadwals->where('hari', $hari)->sortBy('jam_mulai');
                @endphp

                @if ($data->count())
                    @foreach ($data as $j)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800 transition">

                            @if ($loop->first)
                                <td class="border dark:border-gray-700 p-3 font-semibold bg-gray-50 dark:bg-gray-800"
                                    rowspan="{{ $data->count() }}">
                                    {{ $hari }}
                                </td>
                            @endif

                            <td class="border dark:border-gray-700 p-3">
                                {{ $j->jam_mulai }} - {{ $j->jam_selesai }}
                            </td>

                            <td class="border dark:border-gray-700 p-3">
                                {{ $j->mapel->nama }}
                            </td>

                            <td class="border dark:border-gray-700 p-3">
                                {{ $j->guru->user->name ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="border dark:border-gray-700 p-3 font-semibold bg-gray-50 dark:bg-gray-800">
                            {{ $hari }}
                        </td>
                        <td colspan="3"
                            class="border dark:border-gray-700 p-3 text-gray-500 dark:text-gray-400 text-center italic">
                            Tidak ada jadwal
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</x-filament-panels::page>
