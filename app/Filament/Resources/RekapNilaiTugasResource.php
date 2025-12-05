<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RekapNilaiTugasResource\Pages;
use App\Models\Siswa; // Menggunakan model Siswa sebagai basis data
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RekapNilaiTugasResource extends Resource
{
    // Kita gunakan Model Siswa karena kita ingin melist Siswa dan nilai rata-ratanya
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $navigationLabel = 'Rekap Nilai Tugas';
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $slug = 'rekap-nilai-tugas';

    // Urutan menu
    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            // Modifikasi query agar bisa mengambil relasi user dan kelas
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'kelas', 'pengumpulanTugas']))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),

                // Kolom Hitungan Rata-rata
                TextColumn::make('rata_rata_nilai')
                    ->label('Rata-rata Tugas')
                    ->state(function (Model $record) {
                        // Menghitung rata-rata dari kolom 'nilai' di tabel pengumpulan_tugas
                        // whereNotNull memastikan hanya tugas yang sudah dinilai yang dihitung
                        $avg = $record->pengumpulanTugas()
                                      ->whereNotNull('nilai')
                                      ->avg('nilai');

                        return $avg ? number_format($avg, 1) : 'Belum ada nilai';
                    })
                    ->color(fn ($state) => $state === 'Belum ada nilai' ? 'gray' : ($state < 75 ? 'danger' : 'success'))
                    ->badge(),

                // Kolom Jumlah Tugas yang sudah dikumpulkan
                TextColumn::make('tugas_count')
                    ->label('Tugas Dikumpulkan')
                    ->counts('pengumpulanTugas')
                    ->alignCenter(),
            ])
            ->filters([
                // Filter berdasarkan Kelas
                SelectFilter::make('kelas_id')
                    ->label('Filter Kelas')
                    ->relationship('kelas', 'nama'),
            ])
            ->actions([
                // Hanya tombol View, tidak ada Edit/Delete
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Kosongkan jika tidak ingin ada bulk delete
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekapNilaiTugas::route('/'),
        ];
    }

    // --- MENONAKTIFKAN FITUR TAMBAH/EDIT/HAPUS ---
    // Karena ini halaman laporan/rekap, kita disable fitur manipulasi data

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
