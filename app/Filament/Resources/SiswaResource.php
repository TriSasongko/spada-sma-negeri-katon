<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule; // <--- PENTING: Import ini wajib ada

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        // Pastikan hanya admin yang bisa lihat
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Akun Siswa')
                    ->schema([
                        // Input Nama (Disimpan ke tabel Users via Controller/Page logic)
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Siswa')
                            ->required(),

                        // Input Email dengan Validasi Khusus
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            // --- FIX VALIDASI UNIQUE ---
                            ->rule(function ($record) {
                                // Saat Edit: Ambil user_id dari siswa tersebut agar di-ignore
                                $userId = $record?->user_id;

                                // Cek unique ke tabel 'users', kolom 'email', kecuali ID user ini
                                return Rule::unique('users', 'email')->ignore($userId);
                            }),
                            // ---------------------------

                        Forms\Components\TextInput::make('password')
                            ->password()
                            // Hanya simpan jika diisi (agar saat edit tidak wajib isi ulang)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),

                Forms\Components\Section::make('Data Akademik')
                    ->schema([
                        Forms\Components\TextInput::make('nis')
                            ->label('NIS')
                            // Unique di tabel siswas, ignore record siswa yang sedang diedit
                            ->unique(ignoreRecord: true),

                        // Relasi ke Kelas
                        Forms\Components\Select::make('kelas_id')
                            ->relationship('kelas', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Kelas'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),

                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Hapus User juga saat Siswa dihapus
                Tables\Actions\DeleteAction::make()
                     ->before(fn (Siswa $record) => $record->user?->delete()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
