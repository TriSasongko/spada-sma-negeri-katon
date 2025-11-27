<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengumpulanTugasResource\Pages;
use App\Models\PengumpulanTugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengumpulanTugasResource extends Resource
{
    protected static ?string $model = PengumpulanTugas::class;

    // Ganti Label Menu agar lebih komunikatif
    protected static ?string $navigationLabel = 'Koreksi Tugas';
    protected static ?string $modelLabel = 'Pengumpulan Tugas';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pengumpulan')
                    ->schema([
                        // Tampilkan info siswa & tugas sebagai teks saja (Placeholder)
                        Forms\Components\Placeholder::make('siswa_nama')
                            ->label('Nama Siswa')
                            ->content(fn ($record) => $record->siswa->user->name . ' (' . $record->siswa->kelas->nama . ')'),

                        Forms\Components\Placeholder::make('tugas_judul')
                            ->label('Judul Tugas')
                            ->content(fn ($record) => $record->tugas->judul),

                        Forms\Components\Placeholder::make('tanggal_dikumpulkan')
                            ->label('Waktu Pengumpulan')
                            ->content(fn ($record) => $record->tanggal_dikumpulkan),

                        // File Upload (Hanya bisa didownload/lihat, disable edit untuk guru)
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Jawaban Siswa')
                            ->directory('tugas-siswa')
                            ->downloadable()
                            ->openable()
                            ->disabled()
                            ->dehydrated(false) // Agar tidak ikut tersimpan ulang saat save
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Penilaian Guru')
                    ->description('Berikan nilai dan feedback untuk siswa')
                    ->schema([
                        Forms\Components\TextInput::make('nilai')
                            ->numeric()
                            ->label('Nilai (0-100)')
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\Textarea::make('komentar_guru')
                            ->label('Catatan / Feedback')
                            ->placeholder('Contoh: Kerja bagus, tapi perbaiki bagian kesimpulan.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.user.name')
                    ->label('Siswa')
                    ->description(fn ($record) => $record->siswa->kelas->nama) // Tampilkan kelas di bawah nama
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tugas.judul')
                    ->label('Tugas')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('tanggal_dikumpulkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                // Tombol Download File Cepat
                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn () => 'Buka File')
                    ->url(fn ($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary'),

                // Input Nilai Langsung di Tabel (Agar Guru kerja cepat)
                Tables\Columns\TextInputColumn::make('nilai')
                    ->label('Nilai')
                    ->type('number')
                    ->rules(['numeric', 'max:100'])
                    ->sortable(),
            ])
            ->filters([
                // Filter tugas yang belum dinilai
                Tables\Filters\Filter::make('belum_dinilai')
                    ->query(fn (Builder $query) => $query->whereNull('nilai'))
                    ->label('Hanya yang Belum Dinilai'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Detail & Komentar'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengumpulanTugas::route('/'),
            'edit' => Pages\EditPengumpulanTugas::route('/{record}/edit'),
        ];
    }

    // Filter PENTING:
    // Guru hanya boleh melihat tugas yang berasal dari MODUL MEREKA SENDIRI.
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->hasRole('guru')) {
            // Ambil ID Modul yang dibuat oleh guru ini
            $guruId = Auth::user()->guru?->id;

            if ($guruId) {
                // Query: Ambil pengumpulan_tugas dimana tugasnya milik modul yang dibuat guru ini
                $query->whereHas('tugas', function($q) use ($guruId) {
                    $q->whereHas('modul', function($m) use ($guruId) {
                        $m->where('guru_id', $guruId);
                    });
                });
            }
        }

        return $query;
    }

    // Matikan tombol "Create"
    // karena data ini hanya bisa dibuat oleh Siswa (lewat upload), bukan Guru/Admin
    public static function canCreate(): bool
    {
        return false;
    }
}
