<?php

namespace App\Filament\Resources\NilaiUjianResource\Pages;

use App\Filament\Resources\NilaiUjianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNilaiUjians extends ListRecords
{
    protected static string $resource = NilaiUjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
