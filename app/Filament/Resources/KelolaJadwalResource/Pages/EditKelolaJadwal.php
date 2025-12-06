<?php

namespace App\Filament\Resources\KelolaJadwalResource\Pages;

use App\Filament\Resources\KelolaJadwalResource;
use Filament\Resources\Pages\EditRecord;

class EditKelolaJadwal extends EditRecord
{
    protected static string $resource = KelolaJadwalResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Hapus tombol delete agar kelas tidak terhapus
    }

    public function getTitle(): string
    {
        return 'Atur Jadwal: ' . $this->record->nama;
    }

    // Agar setelah save tetap di halaman edit (opsional)
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
