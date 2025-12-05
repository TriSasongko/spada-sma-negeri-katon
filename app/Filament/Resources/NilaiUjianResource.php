<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NilaiUjianResource\Pages;
use App\Models\NilaiUjian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Models\Siswa;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NilaiUjianResource extends Resource
{
    protected static ?string $model = NilaiUjian::class;

    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $navigationLabel = 'Input Nilai (UTS/UAS)';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        // --- PERBAIKAN DI SINI ---
                        Forms\Components\Select::make('siswa_id')
                            ->label('Nama Siswa')
                            ->options(function () {
                                // Ambil ID dari tabel 'siswas' (key), tapi tampilkan nama dari tabel 'users' (value)
                                return Siswa::join('users', 'siswas.user_id', '=', 'users.id')
                                    ->pluck('users.name', 'siswas.id');
                            })
                            ->searchable() // Agar bisa diketik cari namanya
                            ->preload()
                            ->required(),
                        // -------------------------

                        Forms\Components\Select::make('mapel_id')
                            ->relationship('mapel', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Mata Pelajaran'),

                        Forms\Components\Select::make('jenis_ujian')
                            ->options([
                                'UTS' => 'UTS (Ujian Tengah Semester)',
                                'UAS' => 'UAS (Ujian Akhir Semester)',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('nilai')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\Select::make('tahun_ajaran_id')
                            ->relationship('tahunAjaran', 'tahun')
                            ->required()
                            ->label('Tahun Ajaran'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.user.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mapel.nama')
                    ->label('Mapel')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_ujian')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'UTS' => 'info',
                        'UAS' => 'warning',
                    }),

                Tables\Columns\TextColumn::make('nilai')
                    ->sortable()
                    ->color(fn(string $state): string => $state < 75 ? 'danger' : 'success')
                    ->weight('bold'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_ujian')
                    ->options([
                        'UTS' => 'UTS',
                        'UAS' => 'UAS',
                    ]),
                Tables\Filters\SelectFilter::make('mapel_id')
                    ->relationship('mapel', 'nama')
                    ->label('Mata Pelajaran'),
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
            'index' => Pages\ListNilaiUjians::route('/'),
            'create' => Pages\CreateNilaiUjian::route('/create'),
            'edit' => Pages\EditNilaiUjian::route('/{record}/edit'),
        ];
    }
}
