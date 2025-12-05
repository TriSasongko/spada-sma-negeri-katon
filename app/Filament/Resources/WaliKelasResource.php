<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WaliKelasResource\Pages;
use App\Models\WaliKelas;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
            // Select Guru
            // ==========================
            Select::make('guru_id')
                ->label('Pilih Guru')
                ->relationship('guru', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn($record) =>
                    $record->user?->name ?? 'Tanpa Nama'
                )
                ->required()
                ->searchable()
                ->preload(),

            // ==========================
            // Select Kelas (UNIQUE)
            // ==========================
            Select::make('kelas_id')
                ->label('Pilih Kelas')
                ->relationship('kelas', 'nama')
                ->required()
                ->unique(
                    table: 'wali_kelas',
                    column: 'kelas_id',
                    ignoreRecord: true,
                )
                ->preload(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([

            TextColumn::make('kelas.nama')
                ->label('Kelas')
                ->searchable()
                ->sortable(),

            TextColumn::make('guru.user.name')
                ->label('Wali Kelas')
                ->searchable()
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Ditugaskan Sejak')
                ->dateTime()
                ->sortable(),
        ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
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
    public static function canViewAny(): bool
    {
        // Hanya admin yang bisa melihat menu Wali Kelas
        return auth()->user()?->hasRole('admin');
    }

}
