<?php

namespace App\Filament\Resources\RekapNilaiTugasResource\Pages;

use App\Filament\Resources\RekapNilaiTugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRekapNilaiTugas extends ManageRecords
{
    protected static string $resource = RekapNilaiTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
