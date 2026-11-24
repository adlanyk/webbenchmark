<?php

namespace App\Filament\Resources\Audits\Tables;

use App\Models\Department;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        $canViewAllDepartments = function (): bool {
            $user = auth()->user();

            return $user && ($user->hasAnyRole(['verifikator', 'super_admin']) || blank($user->department_id));
        };

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('audit_number')
                    ->label('No. Audit')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('location.department.name')
                    ->label('Departemen')
                    ->badge()
                    ->color('gray')
                    ->default('-')
                    ->sortable(),

                TextColumn::make('location.pic.name')
                    ->label('PIC Lokasi')
                    ->default('-'),

                TextColumn::make('audit_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('findings_count')
                    ->label('Jml Temuan (0)')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

                TextColumn::make('total_score')
                    ->label('Nilai Akhir')
                    ->numeric(decimalPlaces: 2)
                    ->badge()
                    ->color(fn ($state) => $state >= 0.80 ? 'success' : 'warning')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->placeholder('Semua Departemen')
                    ->visible($canViewAllDepartments)
                    ->options(fn () => Department::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('location', function (Builder $locationQuery) use ($data) {
                            $locationQuery->where('department_id', $data['value']);
                        });
                    }),

                SelectFilter::make('finding_status')
                    ->label('Status Temuan')
                    ->placeholder('Semua Status')
                    ->options([
                        'waiting_verification' => 'Menunggu Verifikasi',
                        'open' => 'Open (Belum Dikerjakan)',
                        'in_progress' => 'In Progress',
                        'closed' => 'Closed (Selesai)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('details', function (Builder $detailQuery) use ($data) {
                            $detailQuery
                                ->where('score', 0)
                                ->where('status', $data['value']);
                        });
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
            ->recordActions([
                EditAction::make(),
                // DeleteAction::make(),
            ]);
    }
}