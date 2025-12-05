<?php

namespace App\Filament\Resources\KelolaEskulResource\Pages;

use App\Filament\Resources\KelolaEskulResource;
use Filament\Resources\Pages\EditRecord;

class EditKelolaEskul extends EditRecord
{
    protected static string $resource = KelolaEskulResource::class;

    // Hapus tombol Delete Record (Tong sampah di pojok kanan atas)
    protected function getHeaderActions(): array
    {
        return [];
    }

    // Hapus tombol Save & Cancel di bawah form
    protected function getFormActions(): array
    {
        return [];
    }
}
