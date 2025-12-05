<?php

namespace App\Filament\Resources\KelolaEskulResource\RelationManagers;

use App\Models\Siswa; // Pastikan import Model Siswa
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SiswasRelationManager extends RelationManager
{
    protected static string $relationship = 'siswas';

    protected static ?string $title = 'Anggota Eskul';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            // Title record di tabel tetap gunakan NIS/Nama User yang aman
            ->recordTitleAttribute('nis')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),

                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas Asal'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Daftarkan Siswa')
                    ->modalHeading('Pilih Siswa Masuk Ekskul Ini')
                    // Kita kustomisasi form-nya agar tampilannya Nama tapi datanya aman
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        Forms\Components\Select::make('recordId')
                            ->label('Siswa')
                            ->searchable()
                            ->preload() // Load data di awal agar dropdown cepat
                            ->required()
                            ->options(function (RelationManager $livewire) {
                                // Ambil ID Eskul yang sedang dibuka
                                $eskulId = $livewire->getOwnerRecord()->id;

                                return Siswa::query()
                                    // Filter: Hanya tampilkan siswa yang BELUM masuk ekskul ini
                                    ->whereDoesntHave('eskuls', function ($query) use ($eskulId) {
                                        $query->where('eskuls.id', $eskulId);
                                    })
                                    ->with('user') // Eager load user agar cepat
                                    ->get()
                                    // Format tampilan: "Nama Siswa" (Key-nya tetap ID Siswa)
                                    ->mapWithKeys(function ($siswa) {
                                        return [$siswa->id => $siswa->user->name];
                                    });
                            }),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Keluarkan')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Keluarkan Terpilih'),
                ]),
            ]);
    }
}
