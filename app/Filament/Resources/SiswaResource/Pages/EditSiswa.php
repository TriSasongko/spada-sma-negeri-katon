<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ambil data nama & email dari tabel users untuk ditampilkan di form
        $data['name'] = $this->record->user->name;
        $data['email'] = $this->record->user->email;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = $this->record->user;

        // Siapkan data untuk update User
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        // Update password hanya jika diisi (tidak kosong)
        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        // Eksekusi update ke tabel users
        $user->update($userData);

        // Pastikan kelas_id bernilai NULL jika user menghapus pilihan kelas
        if (empty($data['kelas_id'])) {
            $data['kelas_id'] = null;
        }

        // Hapus field milik User dari array sebelum save ke tabel siswas
        unset($data['name']);
        unset($data['email']);
        unset($data['password']);

        if (isset($data['password_confirmation'])) {
            unset($data['password_confirmation']);
        }

        return $data;
    }
}
