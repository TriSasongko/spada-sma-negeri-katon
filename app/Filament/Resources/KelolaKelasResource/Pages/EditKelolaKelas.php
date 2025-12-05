<?php

namespace App\Filament\Resources\KelolaKelasResource\Pages;

use App\Filament\Resources\KelolaKelasResource;
use Filament\Resources\Pages\EditRecord;

class EditKelolaKelas extends EditRecord
{
    protected static string $resource = KelolaKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Hapus tombol Delete agar kelas tidak terhapus dari sini
        ];
    }

    // Hilangkan tombol Save & Cancel karena kita hanya mengelola Relasi (Siswa)
    protected function getFormActions(): array
    {
        return [];
    }
}
