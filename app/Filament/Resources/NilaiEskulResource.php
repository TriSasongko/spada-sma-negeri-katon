<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NilaiEskulResource\Pages;
use App\Models\NilaiEskul;
use App\Models\Eskul;
use App\Models\Siswa;
// Tambahkan Model PembinaEskul jika sudah dibuat, atau gunakan DB Query
use Illuminate\Support\Facades\DB;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class NilaiEskulResource extends Resource
{
    protected static ?string $model = NilaiEskul::class;

    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $navigationLabel = 'Input Nilai Eskul';
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?int $navigationSort = 5;

    // === 1. PERBAIKAN HAK AKSES (MENU VISIBILITY) ===
    public static function canViewAny(): bool
    {
        $user = Auth::user();

        // Cek 1: Apakah user adalah Admin?
        // Kita cek berdasarkan Role atau Email sesuai database Anda
        if ($user->hasRole('admin') || $user->email === 'admin@sekolah.id') {
            return true;
        }

        // Cek 2: Apakah user adalah Guru Pembina?
        // Cek di tabel 'pembina_eskul' apakah guru ini terdaftar
        if ($user->guru) {
            return DB::table('pembina_eskul')
                ->where('guru_id', $user->guru->id)
                ->exists();
        }

        return false;
    }

    // === 2. PERBAIKAN FILTER DATA (QUERY) ===
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Jika Admin, tampilkan SEMUA data
        if ($user->hasRole('admin') || $user->email === 'admin@sekolah.id') {
            return $query;
        }

        // Jika Guru, hanya tampilkan nilai dari eskul yang dia bina
        if ($user->guru) {
            // Ambil ID eskul dari tabel relasi pembina_eskul
            $eskulIds = DB::table('pembina_eskul')
                ->where('guru_id', $user->guru->id)
                ->pluck('eskul_id');

            return $query->whereIn('eskul_id', $eskulIds);
        }

        // Jika bukan siapa-siapa, kosongkan data
        return $query->whereRaw('1 = 0');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        // 1. Pilih Eskul (Filter Pilihan)
                        Forms\Components\Select::make('eskul_id')
                            ->label('Ekstrakurikuler')
                            ->options(function () {
                                $user = Auth::user();

                                // Jika Admin, tampilkan semua Eskul
                                if ($user->hasRole('admin') || $user->email === 'admin@sekolah.id') {
                                    return Eskul::pluck('nama', 'id');
                                }

                                // Jika Guru, hanya tampilkan eskul yang dibina
                                if ($user->guru) {
                                    return Eskul::whereIn('id', function($q) use ($user) {
                                        $q->select('eskul_id')
                                          ->from('pembina_eskul')
                                          ->where('guru_id', $user->guru->id);
                                    })->pluck('nama', 'id');
                                }

                                return [];
                            })
                            ->required()
                            ->searchable(),

                        // 2. Pilih Siswa (Join User agar nama muncul)
                        Forms\Components\Select::make('siswa_id')
                            ->label('Nama Siswa')
                            ->options(function () {
                                return Siswa::join('users', 'siswas.user_id', '=', 'users.id')
                                    ->pluck('users.name', 'siswas.id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        // 3. Input Predikat
                        Forms\Components\Select::make('predikat')
                            ->options([
                                'A' => 'Sangat Baik',
                                'B' => 'Baik',
                                'C' => 'Cukup',
                                'D' => 'Kurang',
                            ])
                            ->required(),

                        // 4. Tahun Ajaran
                        Forms\Components\Select::make('tahun_ajaran_id')
                            ->label('Tahun Ajaran')
                            ->relationship('tahunAjaran', 'tahun')
                            ->required(),

                        // 5. Catatan
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan / Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('eskul.nama')
                    ->label('Ekstrakurikuler')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('predikat')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tahunAjaran.tahun')
                    ->label('Thn Ajaran'),

                Tables\Columns\TextColumn::make('catatan')
                    ->limit(20),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('eskul_id')
                    ->label('Filter Eskul')
                    ->relationship('eskul', 'nama'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNilaiEskuls::route('/'),
            'create' => Pages\CreateNilaiEskul::route('/create'),
            'edit' => Pages\EditNilaiEskul::route('/{record}/edit'),
        ];
    }
}
