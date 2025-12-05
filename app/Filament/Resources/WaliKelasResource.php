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
        return $form->schema([

            // ==========================
            // Select Guru (Nama dari users.name)
            // ==========================
            Select::make('guru_id')
                ->label('Pilih Guru (Wali Kelas)')
                ->relationship('guru', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn($record) =>
                    $record->user?->name ?? 'Tanpa Nama'
                )
                ->required()
                ->searchable()
                ->preload(),

            // ==========================
            // Select Kelas (Unique)
            // ==========================
            Select::make('kelas_id')
                ->label('Pilih Kelas')
                ->relationship('kelas', 'nama')
                ->required()
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn(Builder $query, $livewire) =>
                    $query->where('id', '!=', $livewire->record?->id),
                )
                ->searchable()
                ->preload(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([

            // Nama Kelas
            TextColumn::make('kelas.nama')
                ->label('Kelas')
                ->sortable()
                ->searchable(),

            // Nama Guru dari users.name
            TextColumn::make('guru.user.name')
                ->label('Wali Kelas')
                ->sortable()
                ->searchable(),

            // Waktu dibuat
            TextColumn::make('created_at')
                ->label('Ditugaskan Sejak')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([])
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
