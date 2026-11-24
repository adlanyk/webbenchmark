<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;


class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pribadi')
                    ->description('Detail akun pengguna')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            Select::make('department_id')
    ->label('Departemen User')
    ->relationship('department', 'name')
    ->searchable()
    ->preload()
    ->placeholder('Pilih Departemen...'),
                    ])->columns(3),

                Section::make('Keamanan Akun')
                    ->description('Biarkan kosong jika tidak ingin mengubah password')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ]),

                Section::make('Hak Akses & Role')
                    ->description('Tentukan role yang dimiliki pengguna ini')
                    ->schema([
                        Select::make('roles')
                            ->label('Pilih Role')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Role menentukan menu dan fitur apa saja yang bisa diakses oleh pengguna.'),
                    ]),
            ]);
    }
}