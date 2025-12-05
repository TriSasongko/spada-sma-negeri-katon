<?php

namespace App\Filament\Resources\NilaiUjianResource\Pages;

use App\Filament\Resources\NilaiUjianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNilaiUjian extends EditRecord
{
    protected static string $resource = NilaiUjianResource::class;

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
