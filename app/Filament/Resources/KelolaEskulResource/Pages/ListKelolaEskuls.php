<?php

namespace App\Filament\Resources\KelolaEskulResource\Pages;

use App\Filament\Resources\KelolaEskulResource;
use Filament\Resources\Pages\ListRecords;

class ListKelolaEskuls extends ListRecords
{
    protected static string $resource = KelolaEskulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kosong, karena tidak ada tombol "New Eskul" di sini
        ];
    }
}
