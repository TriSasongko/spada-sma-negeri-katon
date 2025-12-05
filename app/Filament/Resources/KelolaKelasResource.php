<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelolaKelasResource\Pages;
use App\Filament\Resources\KelasResource\RelationManagers\SiswasRelationManager; // Pastikan baris ini ada
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KelolaKelasResource extends Resource
{
    // Menghubungkan ke Model Kelas
    protected static ?string $model = Kelas::class;

    // KONFIGURASI MENU NAVIGASI
    protected static ?string $navigationLabel = 'Kelola Kelas'; // Nama di Sidebar
    protected static ?string $slug = 'kelola-kelas'; // URL di browser
    protected static ?string $modelLabel = 'Kelola Kelas'; // Label tunggal
    protected static ?string $navigationGroup = 'Master Data'; // Grup menu
    protected static ?string $navigationIcon = 'heroicon-o-users'; // Ikon menu (User Group)

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Tampilan Form (Read Only)
                // Hanya untuk info kelas mana yang sedang dikelola
                Forms\Components\Section::make('Informasi Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Kelas')
                            ->disabled() // Tidak bisa diedit di sini
                            ->dehydrated(false), // Data tidak dikirim ke DB saat save
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Nama Kelas
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),

                // Kolom Jumlah Siswa (Otomatis menghitung dari relasi)
                Tables\Columns\TextColumn::make('siswas_count')
                    ->counts('siswas')
                    ->label('Jumlah Siswa')
                    ->sortable(),
            ])
            ->filters([
                // Tidak perlu filter
            ])
            ->actions([
                // Tombol Aksi Utama
                Tables\Actions\EditAction::make()
                    ->label('Kelola Siswa') // Ubah teks tombol Edit
                    ->icon('heroicon-m-user-group'),
            ])
            ->bulkActions([
                // Kosongkan agar tidak ada opsi hapus massal
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Memanggil Tab Manajer Siswa
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

    // MEMATIKAN FITUR "BUAT KELAS BARU"
    // Karena pembuatan kelas dilakukan di menu "Kelas" yang asli
    public static function canCreate(): bool
    {
        return false;
    }
}
