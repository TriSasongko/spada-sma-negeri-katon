<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Buat User di tabel users
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // 2. Assign Role Siswa
        $user->assignRole('siswa');

        // 3. Masukkan user_id ke array data siswa
        $data['user_id'] = $user->id;

        // 4. Pastikan kelas_id bernilai NULL jika kosong
        // (Mencegah error jika form mengirim string kosong "")
        if (empty($data['kelas_id'])) {
            $data['kelas_id'] = null;
        }

        // 5. Hapus field milik User dari array agar tidak error saat insert ke tabel siswas
        unset($data['name']);
        unset($data['email']);
        unset($data['password']);

        // Hapus password_confirmation jika ada
        if (isset($data['password_confirmation'])) {
            unset($data['password_confirmation']);
        }

        return $data;
    }
}
