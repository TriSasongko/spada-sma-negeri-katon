<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateGuru extends CreateRecord
{
    protected static string $resource = GuruResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Buat User baru
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // 2. Assign Role Guru
        $user->assignRole('guru');

        // 3. Set user_id untuk tabel gurus
        $data['user_id'] = $user->id;

        // 4. Hapus field yang tidak ada di tabel gurus agar tidak error
        unset($data['name']);
        unset($data['email']);
        unset($data['password']);

        return $data;
    }
}
