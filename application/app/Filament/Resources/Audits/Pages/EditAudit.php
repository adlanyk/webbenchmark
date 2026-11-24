<?php

namespace App\Filament\Resources\Audits\Pages;

use App\Filament\Resources\Audits\AuditResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAudit extends EditRecord
{
    protected static string $resource = AuditResource::class;

         protected function getSavedNotificationTitle(): ?string
    {
        return ' Inspeksi 5R berhasil diperbarui!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function canExportOrVerify(): bool
    {
        $user = auth()->user();

        return (bool) ($user && $user->hasAnyRole(['verifikator', 'super_admin']));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->visible(fn (): bool => $this->canExportOrVerify())
                ->url(fn () => route('audits.pdf.preview', $this->record), shouldOpenInNewTab: true),

            // DeleteAction::make()
            //     ->visible(fn (): bool => $this->canExportOrVerify()),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->canExportOrVerify()) {
            $this->record->details()
                ->where('score', 0)
                ->where('status', 'closed')
                ->whereNull('verified_at')
                ->update([
                    'fixed_at' => now(),
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
        }
    }
}