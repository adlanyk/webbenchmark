<?php

namespace App\Filament\Resources\Locations\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('code', 'asc')
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable(),

              TextColumn::make('department.name')
    ->label('Departemen')
    ->badge() // Memberikan efek label berwarna
    ->color('success') // Warna hijau (opsional)
    ->default('Tanpa Departemen') // Jika data kosong
    ->searchable()
    ->sortable(),

                TextColumn::make('checklist_items_count')
                    ->label('Jumlah Item Kategori')
                    ->counts('checklistItems')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make()->label('Kelola Kategori'),
                DeleteAction::make(),
            ]);
    }
}