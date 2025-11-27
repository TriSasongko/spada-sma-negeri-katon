<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\ModulResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateModul extends CreateRecord
{
    protected static string $resource = ModulResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Logika:
        // Jika yang login GURU, paksa guru_id pakai ID dia sendiri.
        // Jika ADMIN, biarkan data['guru_id'] dari form lewat apa adanya.

        if (Auth::user()->hasRole('guru')) {
            $data['guru_id'] = Auth::user()->guru->id;
        }

        return $data;
    }
}
