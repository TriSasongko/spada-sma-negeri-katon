<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelolaEskulResource\Pages;
use App\Filament\Resources\KelolaEskulResource\RelationManagers\SiswasRelationManager;
use App\Models\Eskul;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KelolaEskulResource extends Resource
{
    protected static ?string $model = Eskul::class;

    protected static ?string $slug = 'kelola-eskul';
    protected static ?string $navigationLabel = 'Kelola Ekstrakurikuler';
    protected static ?string $modelLabel = 'Kelola Eskul';
    protected static ?string $navigationGroup = 'Kesiswaan';
    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Eskul')
                    ->schema([
                        // 1. Nama Eskul
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Eskul')
                            ->disabled()
                            ->dehydrated(false),

                        // 2. Nama Pembina (FIX: Ambil dari Relasi)
                        Forms\Components\TextInput::make('pembina_display') // Gunakan nama unik
                            ->label('Pembina')
                            ->disabled()
                            ->dehydrated(false) // Data tidak disimpan ke DB
                            ->formatStateUsing(function ($record) {
                                if (!$record) return '-';

                                // OPSI A: Cek Relasi 'pembinas' (Many-to-Many)
                                // Asumsi: Model Guru terhubung ke User (guru->user->name)
                                if ($record->pembinas()->exists()) {
                                    return $record->pembinas->map(function ($guru) {
                                        return $guru->user->name ?? $guru->name ?? '-';
                                    })->join(', ');
                                }

                                // OPSI B: Jika relasi kosong, coba ambil dari kolom 'pembina' biasa
                                return $record->pembina ?? '-';
                            }),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Eskul')
                    ->searchable()
                    ->sortable(),

                // Menampilkan Pembina di Tabel Depan juga
                Tables\Columns\TextColumn::make('pembinas.user.name')
                    ->label('Pembina')
                    ->listWithLineBreaks() // Jika pembina lebih dari 1, buat baris baru
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('siswas_count')
                    ->counts('siswas')
                    ->label('Jumlah Anggota'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Kelola Anggota')
                    ->icon('heroicon-m-user-group'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SiswasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelolaEskuls::route('/'),
            'edit' => Pages\EditKelolaEskul::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
