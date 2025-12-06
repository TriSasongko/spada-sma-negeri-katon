<?php

namespace App\Filament\Resources\KelolaJadwalResource\Pages;

use App\Filament\Resources\KelolaJadwalResource;
use Filament\Resources\Pages\ListRecords;

class ListKelolaJadwals extends ListRecords
{
    protected static string $resource = KelolaJadwalResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Kosongkan array ini
    }
}
