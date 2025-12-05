<?php

namespace App\Filament\Resources\NilaiEskulResource\Pages;

use App\Filament\Resources\NilaiEskulResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNilaiEskuls extends ListRecords
{
    protected static string $resource = NilaiEskulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
