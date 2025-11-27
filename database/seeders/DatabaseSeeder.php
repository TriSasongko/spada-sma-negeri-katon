<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // Buat 1 Akun Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@sekolah.id',
            'password' => Hash::make('password'), // password default
        ]);

        $admin->assignRole('admin');
    }
}
