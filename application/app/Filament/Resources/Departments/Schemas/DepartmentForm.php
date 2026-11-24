<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Departemen')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Departemen')
                            ->placeholder('Contoh: HRD, LOG, PRD')
                            ->maxLength(10)
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('name')
                            ->label('Nama Departemen')
                            ->placeholder('Contoh: Human Resource, Logistik')
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}