<?php

namespace App\Filament\Resources\ModulResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KuisRelationManager extends RelationManager
{
    protected static string $relationship = 'kuis';
    protected static ?string $title = 'Kuis & Ujian';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('instruksi')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('durasi_menit')
                    ->numeric()
                    ->default(60)
                    ->label('Durasi (Menit)'),

                // --- BAGIAN SOAL (REPEATER) ---
                Forms\Components\Section::make('Daftar Soal')
                    ->schema([
                        Forms\Components\Repeater::make('soals') // Relasi 'soals' harus ada di Model Kuis
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('tipe')
                                    ->options([
                                        'pilihan_ganda' => 'Pilihan Ganda',
                                        'essay' => 'Essay',
                                    ])
                                    ->required()
                                    ->live(),

                                Forms\Components\RichEditor::make('pertanyaan')
                                    ->required()
                                    ->columnSpanFull(),

                                // Opsi Jawaban (Hanya untuk PG) - Disimpan sebagai JSON
                                Forms\Components\KeyValue::make('opsi_jawaban')
                                    ->keyLabel('Huruf (A,B,C,D)')
                                    ->valueLabel('Teks Jawaban')
                                    ->visible(fn (Forms\Get $get) => $get('tipe') === 'pilihan_ganda'),

                                Forms\Components\Select::make('kunci_jawaban')
                                    ->options([
                                        'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('tipe') === 'pilihan_ganda'),
                            ])
                            ->itemLabel(fn (array $state): ?string => strip_tags($state['pertanyaan'] ?? null)),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul'),
                Tables\Columns\TextColumn::make('durasi_menit')->label('Durasi')->suffix(' Menit'),
                Tables\Columns\TextColumn::make('soals_count')->counts('soals')->label('Jml Soal'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Kuis Baru')
                    ->modalWidth('4xl'), // Lebar modal besar agar enak input soal
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('4xl'),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
