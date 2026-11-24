<?php

use App\Models\Audit;
use App\Services\AuditPdfReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;


Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/login', function () {
    // Mengarahkan ke panel admin Filament
    return redirect()->route('filament.admin.auth.login');
})->name('login');


Route::livewire('/inspeksi', 'public-audit-form')->name('public.audit');
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/audits/{audit}/pdf-preview', function (Audit $audit) {
        abort_unless(auth()->user()?->hasAnyRole(['verifikator', 'super_admin']), 403);

        return AuditPdfReportService::stream($audit);
    })->name('audits.pdf.preview');

    Route::get('/admin/audits-report/pdf-preview', function (Request $request) {
        abort_unless(auth()->user()?->hasAnyRole(['verifikator', 'super_admin']), 403);

        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $audits = Audit::whereBetween('audit_date', [
            $request->input('start_date'),
            $request->input('end_date'),
        ])->orderBy('audit_date')->get();

        return AuditPdfReportService::stream($audits);
    })->name('audits.pdf.period-preview');
});