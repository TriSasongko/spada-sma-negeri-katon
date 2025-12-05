<?php

namespace App\Filament\Resources\EskulResource\Pages;

use App\Filament\Resources\EskulResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEskul extends EditRecord
{
    protected static string $resource = EskulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
