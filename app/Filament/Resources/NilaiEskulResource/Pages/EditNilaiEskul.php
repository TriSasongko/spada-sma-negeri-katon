<?php

namespace App\Filament\Resources\NilaiEskulResource\Pages;

use App\Filament\Resources\NilaiEskulResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNilaiEskul extends EditRecord
{
    protected static string $resource = NilaiEskulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
