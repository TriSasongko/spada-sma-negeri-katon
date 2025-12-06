<?php

namespace App\Filament\Resources\KelolaJadwalResource\Pages;

use App\Filament\Resources\KelolaJadwalResource;
use Filament\Resources\Pages\ViewRecord;

class ViewKelolaJadwal extends ViewRecord
{
    protected static string $resource = KelolaJadwalResource::class;

    // Jika ingin pakai blade custom, tetapkan view custom (opsional)
    protected static string $view = 'filament.resources.kelola-jadwal.view';

    // Kalau mau, override mount untuk eager load relasi:
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }
}
