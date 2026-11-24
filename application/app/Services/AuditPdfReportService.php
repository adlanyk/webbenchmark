<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Location;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuditPdfReportService
{
    public static function stream(Audit|Collection $audits): Response
    {
        $pdf = self::buildPdf($audits);$filename = 'Laporan-Audit-5R-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->stream($filename);
    }

    public static function buildPdf(Audit|Collection $audits): \Barryvdh\DomPDF\PDF
    {
        $auditList =$audits instanceof Audit ? collect([$audits]) :$audits;

        $auditList->loadMissing([
            'location.pic',
            'location.department',
            'details' => fn ($q) =>$q->with(['checklistItem.department']),
        ]);

        // Kelompokkan semua detail audit per checklist_item_id
        $detailsGroupedByItem = collect();
        foreach ($auditList->sortBy('audit_date') as$audit) {
            foreach ($audit->details as$detail) {
                $itemId =$detail->checklist_item_id;
                $bucket =$detailsGroupedByItem->get($itemId, collect());$bucket->push([
                    'detail' => $detail,
                    'audit' => $audit,
                ]);
                $detailsGroupedByItem->put($itemId,$bucket);
            }
        }

        // Ambil SELURUH Lokasi aktif, urutkan seperti kolom Excel: A, B, ..., Z, AA, AB, ...
        $allLocations = Location::with([
            'pic',
            'department',
            'checklistItems' => fn ($q) =>$q->where('is_active', true)->orderBy('sort_order'),
        ])
            ->where('is_active', true)
            ->get()
            ->sortBy(fn (Location $loc) => self::excelColumnToNumber($loc->code ?? ''))
            ->values();

        $checklistSections = collect();
        $findingRows = collect();$findingsByCategory = [];

        foreach ($allLocations as$location) {
            if ($location->checklistItems->isEmpty()) {
                continue;
            }

            $items = $location->checklistItems->map(function ($checklistItem, $idx) use ($location, $detailsGroupedByItem, &$findingRows, &$findingsByCategory) {$entries = $detailsGroupedByItem->get($checklistItem->id, collect());
                $findingEntries =$entries->filter(fn ($entry) => (int)$entry['detail']->score === 0);
                $score =$findingEntries->isNotEmpty() ? 0 : 1;

                // Gabungkan 2 atau 3+ keterangan temuan unik menggunakan koma (", ")
                $combinedNotes =$findingEntries
                    ->map(fn ($entry) => trim((string) ($entry['detail']->finding_note ?? '')))
                    ->filter()
                    ->unique()
                    ->implode(', ');

                // Gabungkan tanggal due date unik dengan koma (", ")
                $combinedDueDates =$findingEntries
                    ->filter(fn ($entry) => filled($entry['detail']->due_date))
                    ->sortBy(fn ($entry) =>$entry['detail']->due_date)
                    ->map(fn ($entry) => Carbon::parse($entry['detail']->due_date)->translatedFormat('d M Y'))
                    ->unique()
                    ->implode(', ');

                foreach ($findingEntries as$entry) {
                    $detail =$entry['detail'];
                    $dueDateSingle =$detail->due_date
                        ? Carbon::parse($detail->due_date)->translatedFormat('d M Y')
                        : '';

                    $categoryLabel = mb_strtoupper(trim((string) ($checklistItem->question ?? 'LAINNYA')));
                    $findingsByCategory[$categoryLabel] = ($findingsByCategory[$categoryLabel] ?? 0) + 1;

                    $findingRows->push([
                        'location' => $location->name ?? '-',
                        'finding_photo_base64' => self::toBase64Image($detail->finding_photo),
                        'finding_note' => $detail->finding_note ?? '-',
                        'pic' => $location->pic?->name
                            ?? $checklistItem->department?->name
                            ?? '',
                        'action_plan' => $detail->action_plan ?? '',
                        'due_date' => $dueDateSingle,
                        'fix_photo_base64' => self::toBase64Image($detail->fix_photo),
                        'status_raw' => $detail->status ?? 'open',
                        'status' => self::formatStatus($detail->status),
                    ]);
                }

                return [
                    'no' => $checklistItem->sort_order ?? ($idx + 1), 'question' =>$checklistItem->question ?? '-',
                    'score' => $score,
                    'note' => $score === 0 ?$combinedNotes : '',
                    'due_date' => $score === 0 ?$combinedDueDates : '',
                ];
            })->values();

            $totalCount =$items->count();
            $passedCount =$items->where('score', 1)->count();
            $findingsCount =$items->where('score', 0)->count();
            $sectionScore =$totalCount > 0 ? round($passedCount / $totalCount, 2) : 1.00;

            $checklistSections->push([
                'location_code' => $location->code ?? '-',
                'location_name' => mb_strtoupper($location->name ?? '-'),
                'items' => $items,
                'score' => $sectionScore,
                'findings_count' => $findingsCount,
            ]);
        }

        $averageScore =$checklistSections->isNotEmpty()
            ? round($checklistSections->avg('score'), 2)
            : 1.00;

        // =================================================================
        // 1. DIAGRAM BULAT (DONUT PATH SVG MURNI): TOP KATEGORI TEMUAN
        // =================================================================
        arsort($findingsByCategory);$palette = ['#EF4444', '#F59E0B', '#3B82F6', '#8B5CF6', '#64748B'];
        $donutRaw = [];$idxColor = 0;
        $otherCount = 0;

        foreach ($findingsByCategory as $catName =>$cnt) {
            if ($idxColor < 4) {$donutRaw[] = [
                    'label' => Str::limit($catName, 22), 'count' =>$cnt,
                    'color' => $palette[$idxColor],
                ];
                $idxColor++;
            } else {
                $otherCount +=$cnt;
            }
        }

        if ($otherCount > 0) {$donutRaw[] = [
                'label' => 'KATEGORI LAINNYA',
                'count' => $otherCount,
                'color' => $palette[4],
            ];
        }

        $donutSlices = self::buildDonutSvgPaths($donutRaw, 50, 50, 42, 24);

        // =================================================================
        // 2. GRAFIK GARIS (SVG PATH MURNI): SKOR PER KODE AREA (A .. AKHIR)
        // =================================================================
        $areaPoints = $checklistSections->map(fn ($sec) => [
            'code' => $sec['location_code'],
            'name' => $sec['location_name'],
            'score' => $sec['score'],
        ])->values();

        // =================================================================
        // 3. TOP 5 AREA SKOR TERENDAH (GRAFIK BATANG HORIZONTAL)
        // =================================================================
        $lowestAreas =$checklistSections
            ->filter(fn ($s) =>$s['findings_count'] > 0)
            ->sortBy('score')
            ->take(5)
            ->values();

        $totalFindings =$findingRows->count();
        $closedCount =$findingRows->where('status_raw', 'closed')->count();

        // Format periode tanggal header
        $dates =$auditList->pluck('audit_date')->filter()->sort();
        if ($dates->isEmpty()) {$periodLabel = mb_strtoupper(now()->translatedFormat('d F Y'));
        } elseif ($dates->first()->isSameDay($dates->last())) {
            $periodLabel = mb_strtoupper($dates->first()->translatedFormat('d F Y'));
        } else {
            $startDay = $dates->first()->format('d');$endFormatted = mb_strtoupper($dates->last()->translatedFormat('d F Y'));$periodLabel = "{$startDay}-{$endFormatted}";
        }

        $auditors =$auditList->pluck('reporter_name')
            ->filter()
            ->unique()
            ->map(fn ($name) => mb_strtoupper($name))
            ->implode(' & ');

        return Pdf::loadView('pdf.audit-findings-report', [
            'periodLabel' => $periodLabel,
            'auditors' => $auditors ?: '-',
            'checklistSections' => $checklistSections,
            'averageScore' => $averageScore,
            'findingRows' => $findingRows,
            'kpi' => [
                'avg_score' => number_format($averageScore, 2),
                'avg_percent' => round($averageScore * 100, 1), 'total_areas' =>$checklistSections->count(),
                'total_findings' => $totalFindings,
                'closed_count' => $closedCount,
                'closed_percent' => $totalFindings > 0 ? round(($closedCount / $totalFindings) * 100) : 100, ], 'donutSlices' =>$donutSlices,
            'areaPoints' => $areaPoints,
            'lowestAreas' => $lowestAreas,
        ])->setPaper('a4', 'landscape');
    }

    /**
     * Menghitung koordinat SVG <path> irisan Donut Chart menggunakan trigonometri murni.
     * 100% kompatibel dengan DomPDF tanpa atribut transform/dasharray.
     */
    protected static function buildDonutSvgPaths(array $items, float $cx, float$cy, float $outerR, float$innerR): array
    {
        $total = array_sum(array_column($items, 'count'));
        if ($total <= 0) {
            return [];
        }

        $slices = [];$currentAngle = -90.0; // Mulai dari arah jam 12

        foreach ($items as$item) {
            $count = (int)$item['count'];
            if ($count <= 0) {
                continue;
            }

            $pct = $count / $total;
            // Jika 100% penuh (1 kategori saja), buat hampir 360 derajat (359.9) agar SVG arc tidak kolaps
            $sweep =$pct >= 0.9999 ? 359.9 : ($pct * 360.0);$endAngle = $currentAngle +$sweep;

            $startRad = deg2rad($currentAngle);
            $endRad = deg2rad($endAngle);

            // Titik lingkaran luar
            $x1 = round($cx + $outerR * cos($startRad), 2);
            $y1 = round($cy + $outerR * sin($startRad), 2);
            $x2 = round($cx + $outerR * cos($endRad), 2);
            $y2 = round($cy + $outerR * sin($endRad), 2);

            // Titik lingkaran dalam
            $x3 = round($cx + $innerR * cos($endRad), 2);
            $y3 = round($cy + $innerR * sin($endRad), 2);
            $x4 = round($cx + $innerR * cos($startRad), 2);
            $y4 = round($cy + $innerR * sin($startRad), 2);

            $largeArcFlag =$sweep > 180 ? 1 : 0;

            $d = "M {$x1} {$y1} "
               . "A {$outerR} {$outerR} 0 {$largeArcFlag} 1 {$x2} {$y2} "
               . "L {$x3} {$y3} "
               . "A {$innerR} {$innerR} 0 {$largeArcFlag} 0 {$x4} {$y4} Z";

            $slices[] = [
                'label' => $item['label'],
                'count' => $count,
                'percent' => round($pct * 100), 'color' =>$item['color'],
                'd' => $d,
            ];

            $currentAngle =$endAngle;
        }

        return $slices;
    }

    protected static function excelColumnToNumber(string $code): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper(trim($code)));
        if ($letters === '') {
            return PHP_INT_MAX;
        }

        $number = 0;
        $length = strlen($letters);
        for ($i = 0; $i < $length; $i++) {
            $number = ($number * 26) + (ord($letters[$i]) - ord('A') + 1);
        }

        return $number;
    }

    protected static function toBase64Image(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        $fullPath = Storage::disk('public')->path($path);
        $mime = mime_content_type($fullPath) ?: 'image/jpeg';
        $data = base64_encode(file_get_contents($fullPath));

        return "data:{$mime};base64,{$data}";
    }

    protected static function formatStatus(?string $status): string
    {
        return match ($status) {
            'open' => 'OPEN',
            'in_progress' => 'IN PROGRESS',
            'waiting_verification' => 'VERIFIKASI',
            'closed' => 'CLOSED',
            default => '',
        };
    }
}