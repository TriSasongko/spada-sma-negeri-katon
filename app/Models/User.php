<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- PERBAIKAN DI SINI ---
    public function canAccessPanel(Panel $panel): bool
    {
        // Izinkan Admin DAN Guru masuk ke Dashboard Filament
        return $this->hasRole('admin') || $this->hasRole('guru');
    }

    // Relasi ke Profile Guru
    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    // Relasi ke Profile Siswa
    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }
}
