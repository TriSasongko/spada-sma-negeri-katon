<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WaliKelasResource\Pages;
use App\Models\WaliKelas;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WaliKelasResource extends Resource
{
    protected static ?string $model = WaliKelas::class;
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationLabel = 'Wali Kelas';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('guru_id')
                    ->label('Pilih Guru (Wali Kelas)')
                    // FIX UTAMA: Gunakan getOptionLabelFromRecordUsing untuk memaksa
                    // Filament menggunakan Accessor Eloquent 'full_name' dan bukan
                    // mencoba membuat query langsung ke kolom 'nama' di DB.
                    ->relationship(name: 'guru', titleAttribute: 'id') // Gunakan ID sebagai titleAttribute default
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name) // Ambil nama dari Accessor
                    ->required()
                    ->searchable()
                    ->preload(),

                // Field untuk memilih Kelas
                Select::make('kelas_id')
                    ->label('Pilih Kelas')
                    ->relationship(name: 'kelas', titleAttribute: 'nama')
                    ->required()
                    // Constraint unik
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn(Builder $query, $livewire) => $query->where('id', '!=', $livewire->record?->id),
                    )
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom untuk menampilkan nama Kelas
                TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),

                // Kolom untuk menampilkan nama Guru menggunakan accessor
                TextColumn::make('guru.full_name') // Menggunakan 'full_name' dari accessor
                    ->label('Wali Kelas')
                    ->sortable()
                    ->searchable(),

                // Kolom untuk waktu pembuatan (opsional)
                TextColumn::make('created_at')
                    ->label('Ditugaskan Sejak')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWaliKelas::route('/'),
            'create' => Pages\CreateWaliKelas::route('/create'),
            'edit' => Pages\EditWaliKelas::route('/{record}/edit'),
        ];
    }
}
