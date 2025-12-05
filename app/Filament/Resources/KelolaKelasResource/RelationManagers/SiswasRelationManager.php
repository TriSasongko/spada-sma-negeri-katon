<?php

namespace App\Filament\Resources\KelasResource\RelationManagers;

use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class SiswasRelationManager extends RelationManager
{
    protected static string $relationship = 'siswas';
    protected static ?string $title = 'Daftar Siswa';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kelas_id')
                    ->relationship('kelas', 'nama')
                    ->label('Pindahkan ke Kelas Lain')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nis')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('tambah_siswa')
                    ->label('Masukkan Siswa')
                    ->icon('heroicon-m-user-plus')
                    ->form([
                        Forms\Components\Select::make('siswa_id')
                            ->label('Pilih Siswa')
                            ->options(function () {
                                return Siswa::query()
                                    ->whereNull('kelas_id')
                                    ->with('user')
                                    ->get()
                                    ->pluck('user.name', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        $siswa = Siswa::find($data['siswa_id']);
                        $siswa->update(['kelas_id' => $livewire->getOwnerRecord()->id]);
                        Notification::make()->title('Siswa berhasil dimasukkan')->success()->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Pindah'),
                Tables\Actions\Action::make('keluarkan')
                    ->label('Keluarkan')
                    ->icon('heroicon-m-arrow-right-start-on-rectangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Siswa $record) {
                        $record->update(['kelas_id' => null]);
                        Notification::make()->title('Siswa dikeluarkan')->success()->send();
                    }),
            ]);
    }
}
