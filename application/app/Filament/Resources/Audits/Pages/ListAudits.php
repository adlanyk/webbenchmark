<?php

namespace App\Filament\Resources\Audits\Pages;

use App\Filament\Resources\Audits\AuditResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    protected function canAccessPdfReport(): bool
    {
        $user = auth()->user();

        return (bool) ($user && $user->hasAnyRole(['verifikator', 'super_admin']));
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => $this->canAccessPdfReport()),

            Action::make('preview_laporan_pdf')
                ->label('Preview Laporan PDF')
                ->icon('heroicon-o-eye')
                ->color('danger')
                ->visible(fn (): bool => $this->canAccessPdfReport())
                ->modalHeading('Preview Laporan Audit 5R')
                ->modalDescription('Pilih rentang tanggal pelaksanaan audit untuk menampilkan laporan PDF beserta grafik evaluasi.')
                ->modalSubmitActionLabel('Buka Preview PDF')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->default(now()->startOfWeek())
                        ->required(),

                    DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->afterOrEqual('start_date')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $url = route('audits.pdf.period-preview', [
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                    ]);

                    $this->js("window.open('{$url}', '_blank');");
                }),
        ];
    }
}