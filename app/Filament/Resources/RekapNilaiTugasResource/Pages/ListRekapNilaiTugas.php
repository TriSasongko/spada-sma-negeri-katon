<?php

namespace App\Filament\Resources\RekapNilaiTugasResource\Pages;

use App\Filament\Resources\RekapNilaiTugasResource;
use Filament\Resources\Pages\ListRecords;

class ListRekapNilaiTugas extends ListRecords
{
    protected static string $resource = RekapNilaiTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kosong karena kita menonaktifkan fitur Create
        ];
    }
}
