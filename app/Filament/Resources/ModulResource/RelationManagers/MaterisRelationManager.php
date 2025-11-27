<?php

namespace App\Filament\Resources\ModulResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MaterisRelationManager extends RelationManager
{
    protected static string $relationship = 'materis';
    protected static ?string $title = 'Materi Pembelajaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('tipe')
                    ->options([
                        'pdf' => 'Dokumen PDF',
                        'file' => 'File Lain (PPT/Doc)',
                        'video' => 'Video (MP4)',
                        'link' => 'Tautan Luar (Youtube/Drive)',
                    ])
                    ->required()
                    ->live(), // Agar form reaktif saat tipe berubah

                // Field Upload File (Muncul jika tipe bukan Link)
                Forms\Components\FileUpload::make('file_path')
                    ->label('Upload File')
                    ->directory('materi-files')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'video/mp4'])
                    ->visible(fn (Forms\Get $get) => in_array($get('tipe'), ['pdf', 'file', 'video'])),

                // Field Link (Muncul jika tipe Link)
                Forms\Components\TextInput::make('url')
                    ->label('Link URL')
                    ->url()
                    ->visible(fn (Forms\Get $get) => $get('tipe') === 'link'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul'),
                Tables\Columns\TextColumn::make('tipe')
                    ->badge()
                    ->colors([
                        'primary' => 'link',
                        'success' => 'pdf',
                        'warning' => 'video',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
