<?php

namespace App\Filament\Resources\KelolaKelasResource\Pages;

use App\Filament\Resources\KelolaKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKelolaKelas extends ListRecords
{
    protected static string $resource = KelolaKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
