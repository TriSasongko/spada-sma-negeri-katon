<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelolaJadwalResource\Pages;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Guru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KelolaJadwalResource extends Resource
{
    // Kita bind ke Model Kelas, karena flow-nya "Pilih Kelas -> Edit Jadwal Kelas Itu"
    protected static ?string $model = Kelas::class;

    // Konfigurasi Tampilan Menu Sidebar
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Kelola Jadwal';
    protected static ?string $modelLabel = 'Jadwal Kelas';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'kelola-jadwal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bagian Atas: Info Kelas (Read Only)
                Forms\Components\Section::make('Identitas Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Kelas')
                            ->disabled()
                            ->dehydrated(false), // Data ini tidak ikut di-save ulang
                    ]),

                // Bagian Utama: Input Jadwal (Repeater)
                Forms\Components\Section::make('Jadwal Pelajaran')
                    ->description('Tambahkan sesi pelajaran untuk kelas ini.')
                    ->schema([
                        // 'jadwals' adalah nama fungsi relasi hasMany di Model Kelas
                        Forms\Components\Repeater::make('jadwals')
                            ->relationship()
                            ->label('')
                            ->addActionLabel('Tambah Sesi Pelajaran')
                            ->schema([
                                // Kolom 1: Hari & Waktu
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('hari')
                                            ->options([
                                                'Senin' => 'Senin',
                                                'Selasa' => 'Selasa',
                                                'Rabu' => 'Rabu',
                                                'Kamis' => 'Kamis',
                                                'Jumat' => 'Jumat',
                                                'Sabtu' => 'Sabtu',
                                            ])
                                            ->required(),

                                        Forms\Components\TimePicker::make('jam_mulai')
                                            ->label('Mulai')
                                            ->seconds(false)
                                            ->required(),

                                        Forms\Components\TimePicker::make('jam_selesai')
                                            ->label('Selesai')
                                            ->seconds(false)
                                            ->required(),
                                    ]),

                                // Kolom 2: Mapel & Guru
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        // Pilih Mapel
                                        Forms\Components\Select::make('mapel_id')
                                            ->label('Mata Pelajaran')
                                            ->options(Mapel::all()->pluck('nama', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live() // PENTING: Agar dropdown guru bereaksi
                                            ->afterStateUpdated(fn (Set $set) => $set('guru_id', null)),

                                        // Pilih Guru (Difilter otomatis)
                                        Forms\Components\Select::make('guru_id')
                                            ->label('Guru Pengampu')
                                            ->options(function (Get $get) {
                                                $mapelId = $get('mapel_id');

                                                if (!$mapelId) {
                                                    return []; // Kosongkan jika mapel belum dipilih
                                                }

                                                // Logika Filter:
                                                // Ambil Guru yang punya relasi ke Mapel yang dipilih
                                                return Guru::whereHas('mapels', function ($query) use ($mapelId) {
                                                    $query->where('mapels.id', $mapelId);
                                                })->with('user')->get()->pluck('user.name', 'id');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder(fn (Get $get) => $get('mapel_id') ? 'Pilih Guru' : 'Pilih Mapel Terlebih Dahulu'),
                                    ]),
                            ])
                            ->columns(1) // Tumpuk ke bawah agar rapi di HP
                            ->defaultItems(0)
                            ->cloneable() // Fitur copy biar cepat input jadwal sama
                            ->reorderableWithButtons()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),

                // Menampilkan jumlah sesi pelajaran yang sudah diatur
                Tables\Columns\TextColumn::make('jadwals_count')
                    ->counts('jadwals')
                    ->label('Total Sesi')
                    ->badge()
                    ->color('info')
                    ->sortable(),
            ])
            ->actions([
                // Tombol aksi utama: "Atur Jadwal"
                Tables\Actions\EditAction::make()
                    ->label('Atur Jadwal')
                    ->icon('heroicon-m-calendar-days'),
            ])
            ->bulkActions([]); // Matikan bulk action agar aman
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelolaJadwals::route('/'),
            'edit' => Pages\EditKelolaJadwal::route('/{record}/edit'),
        ];
    }

    // Matikan tombol "Create" karena Kelas dibuat di menu Master Kelas, bukan disini
    public static function canCreate(): bool
    {
        return false;
    }
}
