<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource\Pages;
use App\Models\Guru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Akun Login')
                    ->description('Data untuk login ke sistem')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignorable: fn($record) => $record?->user
                            ),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),
                    ])->columns(2),

                Forms\Components\Section::make('Profil Guru')
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('gelar_depan'),
                        Forms\Components\TextInput::make('gelar_belakang'),

                        // 1. Relasi Mapel (Sudah ada)
                        Forms\Components\Select::make('mapels')
                            ->relationship('mapels', 'nama')
                            ->multiple()
                            ->preload()
                            ->label('Mata Pelajaran Ampuan'),

                        // 2. TAMBAHAN BARU: Relasi Kelas (Penugasan)
                        // Pastikan method 'kelas()' sudah ada di Model Guru
                        Forms\Components\Select::make('kelas')
                            ->relationship('kelas', 'nama')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Kelas Ajar (Penugasan)'),

                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nip')->label('NIP'),

                Tables\Columns\TextColumn::make('mapels.nama')
                    ->label('Mapel')
                    ->badge()
                    ->limitList(2),

                // 3. TAMBAHAN BARU: Menampilkan Kelas di Tabel
                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas Ajar')
                    ->badge()
                    ->color('info')
                    ->limitList(2),

                Tables\Columns\TextColumn::make('user.email')->label('Email'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn(Guru $record) => $record->user->delete()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
        ];
    }
}
