<?php

namespace App\Filament\Resources\NilaiUjianResource\Pages;

use App\Filament\Resources\NilaiUjianResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNilaiUjian extends CreateRecord
{
    protected static string $resource = NilaiUjianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
