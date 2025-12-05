<?php

namespace App\Filament\Resources\NilaiEskulResource\Pages;

use App\Filament\Resources\NilaiEskulResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNilaiEskul extends CreateRecord
{
    protected static string $resource = NilaiEskulResource::class;

    // Redirect ke halaman list setelah simpan
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
