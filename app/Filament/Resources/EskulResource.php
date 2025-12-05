<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EskulResource\Pages;
use App\Models\Eskul;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EskulResource extends Resource
{
    protected static ?string $model = Eskul::class;

    protected static ?string $navigationGroup = 'Kesiswaan';
    protected static ?string $navigationLabel = 'Ekstrakurikuler';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\TextInput::make('nama')
                ->label('Nama Ekskul')
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->columnSpanFull(),

            Forms\Components\Select::make('kategori')
                ->label('Kategori')
                ->options([
                    'Olahraga' => 'Olahraga',
                    'Seni' => 'Seni',
                    'Akademik' => 'Akademik',
                    'Lainnya' => 'Lainnya',
                ])
                ->required(),

            // ================================
            // 📌 Relasi Many-to-Many
            // Tabel Pivot: pembina_eskul
            // ================================
            Forms\Components\Select::make('pembina')
                ->label('Pembina Eskul (Guru)')
                ->multiple()
                ->relationship('pembinas', 'id')
                ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name ?? 'Tanpa Nama')
                ->searchable()
                ->preload()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Ekskul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->sortable(),

                // Menampilkan daftar Guru Pembina
                Tables\Columns\BadgeColumn::make('pembinas.user.name')
                    ->label('Pembina')
                    ->separator(', ')
                    ->limitList(3),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEskuls::route('/'),
            'create' => Pages\CreateEskul::route('/create'),
            'edit' => Pages\EditEskul::route('/{record}/edit'),
        ];
    }
}
