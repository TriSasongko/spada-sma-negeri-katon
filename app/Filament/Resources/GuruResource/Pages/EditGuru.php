<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditGuru extends EditRecord
{
    protected static string $resource = GuruResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Isi form dengan data dari tabel users
        $data['name'] = $this->record->user->name;
        $data['email'] = $this->record->user->email;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = $this->record->user;

        // Update data User
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        // Update password hanya jika diisi
        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user->update($userData);

        // Bersihkan data sebelum disimpan ke tabel gurus
        unset($data['name']);
        unset($data['email']);
        unset($data['password']);

        return $data;
    }
}
