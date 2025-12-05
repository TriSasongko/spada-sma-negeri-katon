<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Akun Siswa')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Siswa')
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->rule(function ($record) {
                                $userId = $record?->user_id;
                                return Rule::unique('users', 'email')->ignore($userId);
                            }),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),

                Forms\Components\Section::make('Data Akademik')
                    ->schema([
                        Forms\Components\TextInput::make('nis')
                            ->label('NIS')
                            ->unique(ignoreRecord: true),

                        // --- BAGIAN KELAS (OPSIONAL) ---
                        Forms\Components\Select::make('kelas_id')
                            ->relationship('kelas', 'nama')
                            ->searchable()
                            ->preload()
                            ->label('Kelas')
                            ->placeholder('Pilih Kelas (Opsional)')
                            ->nullable(), // Mengizinkan nilai NULL
                            // ->required() // Baris ini SUDAH DIHAPUS agar tidak wajib
                        // -------------------------------
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
                    ->placeholder('Belum Masuk Kelas') // Teks jika kelas kosong
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
