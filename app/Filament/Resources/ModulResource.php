<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModulResource\Pages;
use App\Filament\Resources\ModulResource\RelationManagers;
use App\Models\Modul;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ModulResource extends Resource
{
    protected static ?string $model = Modul::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Modul')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        // --- INPUT GURU (KHUSUS ADMIN) ---
                        Forms\Components\Select::make('guru_id')
                            ->label('Guru Pengampu')
                            ->options(\App\Models\Guru::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable()
                            ->preload()
                            // Hanya muncul untuk Admin
                            ->required(fn () => auth()->user()->hasRole('admin'))
                            ->visible(fn () => auth()->user()->hasRole('admin'))
                            // PENTING: live() membuat form bereaksi saat nilai berubah
                            ->live()
                            // Reset pilihan kelas jika guru diganti
                            ->afterStateUpdated(fn (Set $set) => $set('kelas_id', null)),

                        Forms\Components\RichEditor::make('deskripsi')
                            ->columnSpanFull(),

                        // --- INPUT KELAS (DINAMIS) ---
                        Forms\Components\Select::make('kelas_id')
                            ->relationship(
                                name: 'kelas',
                                titleAttribute: 'nama',
                                modifyQueryUsing: function (Builder $query, Get $get) {
                                    // Skenario 1: Yang login adalah Guru
                                    if (auth()->user()->hasRole('guru')) {
                                        $guruId = auth()->user()->guru?->id;
                                        // Tampilkan kelas yang diajar guru ini saja
                                        return $query->whereHas('gurus', function ($q) use ($guruId) {
                                            $q->where('gurus.id', $guruId);
                                        });
                                    }

                                    // Skenario 2: Yang login Admin
                                    // Ambil nilai guru_id yang dipilih Admin di form
                                    $selectedGuruId = $get('guru_id');

                                    if ($selectedGuruId) {
                                        // Filter kelas berdasarkan guru yang dipilih Admin
                                        return $query->whereHas('gurus', function ($q) use ($selectedGuruId) {
                                            $q->where('gurus.id', $selectedGuruId);
                                        });
                                    }

                                    // Jika Admin belum pilih guru, jangan tampilkan apa-apa (opsional)
                                    // atau return $query untuk tampilkan semua.
                                    // Kita pilih return semua jika belum ada guru dipilih (default)
                                    return $query;
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Kelas'),

                        Forms\Components\Select::make('mapel_id')
                            ->relationship('mapel', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),

                        Forms\Components\DateTimePicker::make('publish_at')
                            ->label('Jadwal Publish'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kelas.nama')->sortable()->badge(),
                Tables\Columns\TextColumn::make('mapel.nama')->sortable(),
                Tables\Columns\TextColumn::make('guru.user.name')->label('Guru')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MaterisRelationManager::class,
            RelationManagers\TugasRelationManager::class,
            RelationManagers\KuisRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModuls::route('/'),
            'create' => Pages\CreateModul::route('/create'),
            'edit' => Pages\EditModul::route('/{record}/edit'),
        ];
    }

    // Filter agar Guru hanya melihat modul miliknya sendiri di tabel
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->hasRole('guru')) {
            $guruId = Auth::user()->guru?->id;
            if ($guruId) {
                $query->where('guru_id', $guruId);
            }
        }

        return $query;
    }
}
