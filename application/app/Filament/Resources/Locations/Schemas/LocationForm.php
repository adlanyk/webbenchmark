<?php

namespace App\Filament\Resources\Locations\Schemas;

use App\Models\Department;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Lokasi & Penanggung Jawab (PIC)')
                    ->columns(2)
                    ->columnspanFull()
                    ->schema([
                        // TAMBAHAN: Pilihan Departemen
                        Select::make('department_id')
                            ->label('Departemen')
                            ->relationship('department', 'name') // Otomatis ambil dari tabel departments
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih Departemen...')
                            ->required()
                            ->disabled(fn () => !Auth::user()->hasRole(['super_admin', 'admin'])) // Hanya admin yang bisa ubah
                            ->dehydrated() // Tetap kirim nilainya ke database meskipun disabled
                            ->columnSpanFull(), // Biarkan lebar penuh di baris pertama

                        TextInput::make('code')
                            ->label('Kode Blok')
                            ->placeholder('Contoh: A, B, C')
                            ->maxLength(10)
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('name')
                            ->label('Nama Lokasi')
                            ->placeholder('Contoh: POS SECURITY, RESEPSIONIST, OFFICE')
                            ->required(),

                        Select::make('pic_user_id')
                            ->label('PIC Penanggung Jawab')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText('Temuan pada lokasi ini otomatis dikirim ke akun PIC yang dipilih.'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}