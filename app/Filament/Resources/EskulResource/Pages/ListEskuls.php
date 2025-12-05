<?php

namespace App\Filament\Resources\EskulResource\Pages;

use App\Filament\Resources\EskulResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEskuls extends ListRecords
{
    protected static string $resource = EskulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
