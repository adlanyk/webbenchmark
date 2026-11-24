<?php

namespace App\Filament\Resources\Audits\Pages;

use App\Filament\Resources\Audits\AuditResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAudit extends CreateRecord
{
    protected static string $resource = AuditResource::class;

    protected function getRedirectUrl(): string
    {
        // Setelah simpan, kembali ke halaman daftar Audit
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $audit = $this->record;

        // 1. Hitung skor akhir (Jumlah nilai 1 / Total item)
        $audit->recalculateScore();

        // 2. Jika ada temuan (nilai 0), kirim notifikasi ke PIC Lokasi
        if ($audit->findings_count > 0 && $audit->location?->pic) {
            $pic = $audit->location->pic;

            Notification::make()
                ->title("Temuan Baru di {$audit->location->name}")
                ->body("Terdapat {$audit->findings_count} temuan pada tanggal {$audit->audit_date->format('d/m/Y')}. Silakan isi Action Plan, Due Date, dan Foto Perbaikan.")
                ->danger()
                ->sendToDatabase($pic);
        }
    }
}