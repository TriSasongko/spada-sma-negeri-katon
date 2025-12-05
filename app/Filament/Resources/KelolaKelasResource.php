<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelolaKelasResource\Pages;
use App\Filament\Resources\KelasResource\RelationManagers\SiswasRelationManager;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KelolaKelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationLabel = 'Kelola Kelas';
    protected static ?string $slug = 'kelola-kelas';
    protected static ?string $modelLabel = 'Kelola Kelas';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Kelas')
                            ->disabled()
                            ->dehydrated(false),

                        // PERBAIKAN DI SINI:
                        // 1. Ganti nama field jadi 'wali_kelas_display' (agar tidak bentrok dengan logic binding model)
                        // 2. Gunakan formatStateUsing untuk mengambil data secara manual
                        Forms\Components\TextInput::make('wali_kelas_display')
                            ->label('Wali Kelas')
                            ->disabled()
                            ->dehydrated(false) // Data ini tidak perlu disimpan ke DB
                            ->formatStateUsing(function ($record) {
                                // Jika record belum ada (create mode), return null
                                if (!$record) return null;

                                // Ambil data secara manual: Kelas -> WaliKelas -> Guru -> User -> Name
                                // Tanda '?' (null safe operator) mencegah error jika data kosong di tengah jalan
                                return $record->waliKelas?->guru?->user?->name ?? 'Belum ditentukan';
                            }),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Optimasi Query (Eager Loading) agar loading halaman list cepat
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['waliKelas.guru.user']))
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),

                // Di Tabel, dot notation biasanya aman karena Filament menanganinya berbeda
                Tables\Columns\TextColumn::make('waliKelas.guru.user.name')
                    ->label('Wali Kelas')
                    ->placeholder('Belum ada')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('siswas_count')
                    ->counts('siswas')
                    ->label('Jumlah Siswa')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Kelola Siswa')
                    ->icon('heroicon-m-user-group'),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListKelolaKelas::route('/'),
            'edit' => Pages\EditKelolaKelas::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
