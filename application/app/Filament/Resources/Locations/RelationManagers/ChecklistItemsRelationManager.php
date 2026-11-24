<?php

namespace App\Filament\Resources\Locations\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChecklistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'checklistItems';

    protected static ?string $title = 'Daftar Kategori / Item Penilaian';

    protected static ?string $modelLabel = 'Item Kategori';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sort_order')
                    ->label('Nomor Urut')
                    ->numeric()
                    ->default(fn () => ($this->getOwnerRecord()->checklistItems()->max('sort_order') ?? 0) + 1)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Textarea::make('question')
                    ->label('Uraian Kategori / Pertanyaan')
                    ->placeholder('Contoh: GERBANG PLANT (TIDAK ADA RUSAK DAN BERSIH)')
                    ->rows(2)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('No.')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('question')
                    ->label('Kategori / Item Penilaian')
                    ->wrap()
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->width('80px'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('+ Tambah Kategori'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}