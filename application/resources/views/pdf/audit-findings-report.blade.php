<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Audit 5R Plant Makassar</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 6mm 7mm 6mm 7mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.5pt;
            line-height: 1.15;
            color: #000000;
            margin: 0;
            padding: 0;
        }

        /* =====================================================
           HEADER INFO (LOCATION, TANGGAL, INSPECTOR)
           ===================================================== */
        .info-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
            font-weight: bold;
        }
        .info-header-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-label {
            width: 15%;
            font-weight: 900;
            text-transform: uppercase;
        }
        .info-colon {
            width: 1%;
            padding-right: 5px;
        }
        .info-value {
            width: 84%;
            border-bottom: 1.5px dotted #000000;
            font-weight: bold;
        }
        .info-indent {
            padding-left: 16%; /* Untuk menjorokkan nama inspector ke-2 dan ke-3 */
        }

        /* =====================================================
           EXECUTIVE VISUAL DASHBOARD (KPI + DONUT + LINE CHART)
           ===================================================== */
        .sheet1-header {
            text-align: center;
            margin-bottom: 4px;
        }

        .sheet1-title {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 1px 0;
        }

        .sheet1-subtitle {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        table.dashboard-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            margin: 0 -5px 5px -5px;
            table-layout: fixed;
            page-break-inside: avoid;
        }

        td.dash-card {
            border: 1pt solid #CBD5E1;
            border-radius: 4px;
            background-color: #F8FAFC;
            padding: 5px 7px;
            vertical-align: top;
        }

        .dash-card-title {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1E293B;
            border-bottom: 1pt solid #E2E8F0;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }

        table.kpi-mini-grid {
            width: 100%;
            border-collapse: collapse;
        }

        table.kpi-mini-grid td {
            padding: 3px 3px;
            border: 0.8pt solid #E2E8F0;
            background-color: #FFFFFF;
            text-align: center;
        }

        .kpi-val {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0F172A;
            display: block;
        }

        .kpi-lbl {
            font-size: 6.2pt;
            color: #475569;
            text-transform: uppercase;
        }

        .area-bar-wrap {
            width: 100%;
            height: 5px;
            background-color: #E2E8F0;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 1.5px;
        }

        .area-bar-fill {
            height: 5px;
            background-color: #EF4444;
        }

        /* =====================================================
           LEMBAR 1: REKAP CHECKLIST STANDART KEBERSIHAN (DEMPET & PADAT)
           ===================================================== */
        table.checklist-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
        }

        table.checklist-table th,
        table.checklist-table td {
            border: 1pt solid #000000;
            padding: 1.8px 4px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.checklist-table tr {
            page-break-inside: avoid;
        }

        tr.area-header-row {
            page-break-after: avoid;
        }

        tr.keep-with-subtotal {
            page-break-after: avoid;
        }

        .th-cyan {
            background-color: #00B0F0;
            color: #000000;
            font-weight: bold;
            text-align: center;
            font-size: 7.5pt;
            padding: 2.5px 4px;
            text-transform: uppercase;
        }

        .row-subtotal td {
            font-weight: bold;
            background-color: #FFFFFF;
            padding: 2px 4px;
        }

        .row-average td.avg-box {
            border: 1.2pt solid #000000;
            font-weight: bold;
            font-size: 8pt;
            padding: 2.5px 4px;
            background-color: #FFFFFF;
        }

        .row-average td.no-border {
            border: none !important;
            padding: 0;
        }

        /* =====================================================
           FOOTER & SIGNATURE (SESUAI GAMBAR)
           ===================================================== */
        .sheet1-footer-block {
            page-break-inside: avoid;
            margin-top: 8px;
        }

        .legend-table-new {
            width: 38%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 7.5pt;
        }
        .legend-table-new th, 
        .legend-table-new td {
            border: 1pt solid #000000;
            padding: 2px 4px;
        }
        .legend-table-new th {
            text-align: center;
            font-weight: bold;
        }

        .instructions-box-new {
            font-size: 7.5pt;
            line-height: 1.3;
            margin-bottom: 20px;
        }

        .signature-table-new {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            text-align: center;
        }
        .signature-table-new td {
            width: 33.33%;
            vertical-align: bottom;
            padding: 0 10px;
        }
        .sig-space {
            height: 50px; /* Ruang kosong untuk tanda tangan */
        }
        .sig-name-new {
            font-size: 8pt;
            font-weight: bold;
            border-bottom: 1pt solid #000000;
            display: inline-block;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }
        .sig-title-new {
            font-size: 8pt;
        }
        .sig-role-text {
            font-size: 8pt;
            margin-bottom: 2px;
            height: 14px;
        }

        /* =====================================================
           LEMBAR 2: DOKUMENTASI FOTO TEMUAN & PERBAIKAN PIC
           ===================================================== */
        .page-break {
            page-break-before: always;
        }

        .report-header {
            text-align: center;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 2px 0;
        }

        .report-period {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .auditor-bar {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
            text-align: left;
        }

        table.audit-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.audit-table th,
        table.audit-table td {
            border: 1.2pt solid #000000;
            padding: 4px 5px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.audit-table thead {
            display: table-header-group;
        }

        table.audit-table tr {
            page-break-inside: avoid;
        }

        .th-yellow {
            background-color: #FFFF00;
            color: #000000;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
            padding: 5px 4px;
        }

        .th-green {
            background-color: #92D050;
            color: #000000;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
            padding: 5px 4px;
        }

        .th-white {
            background-color: #FFFFFF;
            color: #000000;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
            padding: 5px 4px;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .photo-cell {
            padding: 2px !important;
            text-align: center;
            vertical-align: middle;
        }

        .photo-cell img {
            max-width: 115px;
            max-height: 120px;
            width: auto;
            height: auto;
            display: block;
            margin: 0 auto; /* Agar gambar center */
            object-fit: cover;
        }

        .status-badge {
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
        }
    </style>
</head>
<body>


    <div class="sheet1-header" style="margin-top: 15px;">
        <p class="sheet1-title">CHECKLIST AUDIT 5R / STANDART KEBERSIHAN PLANT MAKASSAR</p>
    </div>
     {{-- ===================================================== --}}
    {{-- HEADER INFO BARU (LOCATION, TANGGAL, INSPECTOR)       --}}
    {{-- ===================================================== --}}
    <table class="info-header-table">
        <tr>
            <td class="info-label">LOCATION</td>
            <td class="info-colon">:</td>
            <td class="info-value">PLANT MAKASSAR</td>
        </tr>
        <tr>
            <td class="info-label">HARI / TANGGAL</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $periodLabel }}</td>
        </tr>
        <tr>
            <td class="info-label" style="font-style: italic;">INSPECTOR MOT</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $auditors }}</td> 
        </tr>
    </table>

    {{-- =================================================================
         EXECUTIVE VISUAL DASHBOARD (100% DOMPDF SVG PATH RENDER)
         ================================================================= --}}
    <table class="dashboard-grid">
        <tr>
            {{-- KOLOM 1: KPI RINGKASAN & GRAFIK BATANG FOKUS AREA --}}
            <td class="dash-card" style="width: 28%;">
                <div class="dash-card-title">Ringkasan Performa 5R</div>
                <table class="kpi-mini-grid">
                    <tr>
                        <td style="width: 34%;">
                            <span class="kpi-val" style="color: #0284C7;">{{ $kpi['avg_score'] }}</span>
                            <span class="kpi-lbl">Nilai Rata"</span>
                        </td>
                        <td style="width: 33%;">
                            <span class="kpi-val" style="color: #DC2626;">{{ $kpi['total_findings'] }}</span>
                            <span class="kpi-lbl">Temuan (0)</span>
                        </td>
                        <td style="width: 33%;">
                            <span class="kpi-val" style="color: #16A34A;">{{ $kpi['closed_percent'] }}%</span>
                            <span class="kpi-lbl">Closed</span>
                        </td>
                    </tr>
                </table>

                <div style="margin-top: 4px; font-size: 6.5pt; font-weight: bold; color: #334155;">
                    Fokus Perbaikan Area (Skor Terendah):
                </div>
                @forelse ($lowestAreas as $area)
                    @php $pct = round($area['score'] * 100); @endphp
                    <div style="margin-top: 2px;">
                        <div style="font-size: 6.2pt;">
                            <strong>[{{ $area['location_code'] }}] {{ \Illuminate\Support\Str::limit($area['location_name'], 20) }}</strong>
                            <span style="float: right; font-weight: bold; color: #DC2626;">{{ number_format($area['score'], 2) }} ({{ $area['findings_count'] }})</span>
                        </div>
                        <div class="area-bar-wrap">
                            <div class="area-bar-fill" style="width: {{ max(8, 100 - $pct) }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div style="margin-top: 4px; font-size: 6.6pt; color: #16A34A; font-weight: bold;">
                        ✓ Seluruh {{ $kpi['total_areas'] }} area mencapai skor sempurna (1.00).
                    </div>
                @endforelse
            </td>

            {{-- KOLOM 2: DIAGRAM BULAT (SVG ARC PATH) TOP KATEGORI TEMUAN --}}
            <td class="dash-card" style="width: 32%;">
                <div class="dash-card-title">Diagram Kategori Temuan Dominan</div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 90px; text-align: center; vertical-align: middle;">
                            <svg width="88" height="88" viewBox="0 0 100 100">
                                @if ($kpi['total_findings'] > 0 && count($donutSlices) > 0)
                                    @foreach ($donutSlices as $slice)
                                        <path d="{{ $slice['d'] }}" fill="{{ $slice['color'] }}" stroke="#FFFFFF" stroke-width="1" />
                                    @endforeach
                                @else
                                    <circle cx="50" cy="50" r="42" fill="#22C55E" />
                                    <circle cx="50" cy="50" r="24" fill="#F8FAFC" />
                                @endif
                                <text x="50" y="48" text-anchor="middle" font-size="13" font-weight="bold" fill="#0F172A">
                                    {{ $kpi['total_findings'] }}
                                </text>
                                <text x="50" y="60" text-anchor="middle" font-size="6.8" fill="#475569">
                                    TEMUAN
                                </text>
                            </svg>
                        </td>
                        <td style="vertical-align: middle; padding-left: 3px;">
                            @forelse ($donutSlices as $slice)
                                <div style="margin-bottom: 2.5px; font-size: 6.3pt; line-height: 1.15;">
                                    <span style="display: inline-block; width: 7px; height: 7px; background-color: {{ $slice['color'] }}; border-radius: 2px; margin-right: 3px;"></span>
                                    <span>{{ $slice['label'] }}</span>
                                    <strong style="float: right;">{{ $slice['count'] }} ({{ $slice['percent'] }}%)</strong>
                                </div>
                            @empty
                                <div style="font-size: 6.8pt; color: #16A34A; font-weight: bold;">
                                    0 Temuan Ketidaksesuaian
                                </div>
                            @endforelse
                        </td>
                    </tr>
                </table>
            </td>

            {{-- KOLOM 3: GRAFIK GARIS & AREA (SVG PATH) SKOR PER KODE AREA (A .. AKHIR) --}}
            <td class="dash-card" style="width: 40%;">
                <div class="dash-card-title">Grafik Nilai Kepatuhan per Area (Target 0.85)</div>
                @php
                    $w = 310;
                    $h = 90;
                    $padL = 22;
                    $padR = 10;
                    $padT = 12;
                    $padB = 18;
                    $plotW = $w - $padL - $padR;
                    $plotH = $h - $padT - $padB;
                    $countPts = $areaPoints->count();
                    $coords = [];

                    foreach ($areaPoints as $i => $pt) {
                        $x = $countPts > 1
                            ? $padL + ($i * ($plotW / ($countPts - 1)))
                            : $padL + ($plotW / 2);
                        $clampedVal = max(0, min(1, (float) $pt['score']));
                        $y = $padT + $plotH - ($clampedVal * $plotH);
                        $coords[] = [
                            'x' => round($x, 1),
                            'y' => round($y, 1),
                            'code' => $pt['code'],
                            'score' => $pt['score'],
                        ];
                    }

                    // Bangun SVG <path d="M x0 y0 L x1 y1 ..."> untuk garis & area
                    $linePath = '';
                    $areaPath = '';
                    if ($countPts > 1) {
                        $segments = [];
                        foreach ($coords as $idx => $c) {
                            $segments[] = ($idx === 0 ? 'M ' : 'L ') . "{$c['x']} {$c['y']}";
                        }
                        $linePath = implode(' ', $segments);
                        $firstX = $coords[0]['x'];
                        $lastX = $coords[$countPts - 1]['x'];
                        $baseY = round($padT + $plotH, 1);
                        $areaPath = "{$linePath} L {$lastX} {$baseY} L {$firstX} {$baseY} Z";
                    }

                    $targetY = round($padT + $plotH - (0.85 * $plotH), 1);
                    $labelStep = $countPts > 20 ? 2 : 1;
                @endphp

                <svg width="310" height="90" viewBox="0 0 {{ $w }} {{ $h }}">
                    {{-- Grid Horizontal 0.00, 0.50, 1.00 --}}
                    <line x1="{{ $padL }}" y1="{{ $padT }}" x2="{{ $w - $padR }}" y2="{{ $padT }}" stroke="#E2E8F0" stroke-width="0.8" />
                    <line x1="{{ $padL }}" y1="{{ $padT + ($plotH / 2) }}" x2="{{ $w - $padR }}" y2="{{ $padT + ($plotH / 2) }}" stroke="#E2E8F0" stroke-width="0.8" />
                    <line x1="{{ $padL }}" y1="{{ $padT + $plotH }}" x2="{{ $w - $padR }}" y2="{{ $padT + $plotH }}" stroke="#94A3B8" stroke-width="1" />

                    {{-- Garis Putus-putus Target 0.85 --}}
                    <line x1="{{ $padL }}" y1="{{ $targetY }}" x2="{{ $w - $padR }}" y2="{{ $targetY }}" stroke="#16A34A" stroke-width="0.9" stroke-dasharray="3,2" />

                    {{-- Label Sumbu Y --}}
                    <text x="{{ $padL - 3 }}" y="{{ $padT + 3 }}" text-anchor="end" font-size="6.2" fill="#64748B">1.0</text>
                    <text x="{{ $padL - 3 }}" y="{{ $padT + ($plotH / 2) + 2 }}" text-anchor="end" font-size="6.2" fill="#64748B">0.5</text>
                    <text x="{{ $padL - 3 }}" y="{{ $padT + $plotH }}" text-anchor="end" font-size="6.2" fill="#64748B">0.0</text>

                    {{-- Arsiran Area & Garis Utama (<path> kompatibel penuh di DomPDF) --}}
                    @if ($countPts > 1)
                        <path d="{{ $areaPath }}" fill="#BAE6FD" fill-opacity="0.45" />
                        <path d="{{ $linePath }}" fill="none" stroke="#0284C7" stroke-width="1.6" />
                    @endif

                    {{-- Titik Nilai per Area --}}
                    @foreach ($coords as $idx => $c)
                        @php $isBelowTarget = $c['score'] < 0.85; @endphp
                        <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}"
                                r="{{ $isBelowTarget ? '3.2' : '2.2' }}"
                                fill="{{ $isBelowTarget ? '#EF4444' : '#0284C7' }}"
                                stroke="#FFFFFF" stroke-width="0.8" />

                        @if ($isBelowTarget)
                            <text x="{{ $c['x'] }}" y="{{ max(8, $c['y'] - 4.5) }}" text-anchor="middle" font-size="6" font-weight="bold" fill="#DC2626">
                                {{ number_format($c['score'], 2) }}
                            </text>
                        @endif

                        @if ($idx % $labelStep === 0 || $idx === $countPts - 1)
                            <text x="{{ $c['x'] }}" y="{{ $h - 4 }}" text-anchor="middle" font-size="5.8"
                                  font-weight="{{ $isBelowTarget ? 'bold' : 'normal' }}"
                                  fill="{{ $isBelowTarget ? '#DC2626' : '#475569' }}">
                                {{ $c['code'] }}
                            </text>
                        @endif
                    @endforeach
                </svg>
            </td>
        </tr>
    </table>

    {{-- =================================================================
         TABEL REKAP CHECKLIST SELURUH AREA (DEMPET & PADAT)
         ================================================================= --}}
    <table class="checklist-table">
        <tbody>
            @foreach ($checklistSections as $section)
                <tr class="area-header-row">
                    <th class="th-cyan" style="width: 4.5%;">{{ $section['location_code'] }}</th>
                    <th class="th-cyan" style="width: 45.5%;">{{ $section['location_name'] }}</th>
                    <th class="th-cyan" style="width: 6.5%;">NILAI</th>
                    <th class="th-cyan" style="width: 31.5%;">KETERANGAN</th>
                    <th class="th-cyan" style="width: 12%;">DATE ACTION PLANT</th>
                </tr>

                @foreach ($section['items'] as $item)
                    <tr class="{{ $loop->last ? 'keep-with-subtotal' : '' }}">
                        <td class="text-center">{{ $item['no'] }}</td>
                        <td class="text-left">{{ $item['question'] }}</td>
                        <td class="text-center" style="{{ $item['score'] === 0 ? 'font-weight:bold; color:#c00000;' : '' }}">
                            {{ $item['score'] }}
                        </td>
                        <td class="text-center">{{ $item['note'] }}</td>
                        <td class="text-center">{{ $item['due_date'] }}</td>
                    </tr>
                @endforeach

                <tr class="row-subtotal">
                    <td></td>
                    <td class="text-center">NILAI</td>
                    <td class="text-center">{{ number_format($section['score'], 2) }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach

            <tr class="row-average">
                <td class="no-border"></td>
                <td class="avg-box text-center">NILAI RATA"</td>
                <td class="avg-box text-center">{{ number_format($averageScore, 2) }}</td>
                <td class="no-border"></td>
                <td class="no-border"></td>
            </tr>
        </tbody>
    </table>

    {{-- ===================================================== --}}
    {{-- FOOTER BARU (LEGEND, PETUNJUK, TANDA TANGAN)          --}}
    {{-- ===================================================== --}}
    <div class="sheet1-footer-block">
        <table class="legend-table-new">
            <thead>
                <tr>
                    <th colspan="2">NILAI STANDART KEBERSIHAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 80%;">TERDAPAT TEMUAN/KETIDAKSESUAIAN</td>
                    <td style="width: 20%; text-align: center;">: 0</td>
                </tr>
                <tr>
                    <td>TIDAK ADA TEMUAN/KETIDAKSESUAIAN</td>
                    <td style="text-align: center;">: 1</td>
                </tr>
            </tbody>
        </table>

        <div class="instructions-box-new">
            <div>PETUNJUK / CARA PENGISIAN STANDART KEBERSIHAN PLANT</div>
            <div>1. Lokasi , Date Dan Inspection harus di isi sesuai dengan pelaksaan</div>
            <div>2. Score harus diisi dengan nilai Angka 0 dan 1</div>
            <div style="padding-left: 14px;">Score Angka 0 = Terdapat temuan/ketidaksesuaian</div>
            <div style="padding-left: 14px;">Score Angka 1 = Tidak ada temuan/ketidaksesuaian</div>
            <div>3. Keterangan diisi oleh auditor apabila ada keterangan tambahan</div>
        </div>

        <table class="signature-table-new">
            <tr>
                {{-- Tanda Tangan Kiri --}}
                <!-- <td>
                    <div class="sig-role-text">&nbsp;</div> 
                    <div class="sig-space"> -->
                        {{-- Jika ada gambar TTD, taruh img di sini --}}
                        {{-- <img src="..." height="40"> --}}
                    <!-- </div>
                    <div class="sig-name-new">H. Irsyad Naufal</div>
                    <div class="sig-title-new">Document Control</div>
                </td> -->

                {{-- Tanda Tangan Tengah --}}
                <td>
                    <div class="sig-role-text">Dilakukan Oleh</div>
                    <div class="sig-space">
                        {{-- <img src="..." height="40"> --}}
                    </div>
                    <div class="sig-name-new">{{ ucwords(mb_strtolower($auditors)) }}</div>
                    <div class="sig-title-new">SOP Operational</div>
                </td>

                {{-- Tanda Tangan Kanan --}}
                <td>
                    <div class="sig-role-text">Mengetahui</div>
                    <div class="sig-space">
                        {{-- <img src="..." height="40"> --}}
                    </div>
                    <div class="sig-name-new">Muh Nurhamsi</div>
                    <div class="sig-title-new">Plant Manager</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- =================================================================
         HALAMAN BERIKUTNYA: DOKUMENTASI TEMUAN & TINDAK LANJUT PERBAIKAN (DUE DATE)
         ================================================================= --}}
    <div class="page-break"></div>

    <div class="report-header">
        <p class="report-title">DOKUMENTASI TEMUAN KETIDAKSESUAIAN AUDIT 5R PLANT MAKASSAR</p>
        <p class="report-period">{{ $periodLabel }}</p>
    </div>

    <div class="auditor-bar">
        AUDITOR : {{ $auditors }}
    </div>

    <table class="audit-table">
        <thead>
            <tr>
                <th class="th-yellow" style="width: 3.5%;">No</th>
                <th class="th-yellow" style="width: 9.5%;">LOKASI</th>
                <th class="th-yellow" style="width: 19%;">FOTO TEMUAN</th>
                <th class="th-yellow" style="width: 13%;">URAIAN TEMUAN</th>
                <th class="th-green" style="width: 7%;">PIC</th>
                <th class="th-green" style="width: 11%;">ACTION PLAN</th>
                <th class="th-green" style="width: 8%;">DUE DATE</th>
                <th class="th-green" style="width: 19%;">FOTO PERBAIKAN</th>
                <th class="th-white" style="width: 10%;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($findingRows as $index => $row)
                <tr style="height: 125px;">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row['location'] }}</td>
                    <td class="photo-cell text-center">
                        @if ($row['finding_photo_base64'])
                            <img src="{{ $row['finding_photo_base64'] }}" alt="Foto Temuan">
                        @endif
                    </td>
                    <td class="text-left">{{ $row['finding_note'] }}</td>
                    <td class="text-center">{{ $row['pic'] }}</td>
                    <td class="text-left">{{ $row['action_plan'] }}</td>
                    <td class="text-center">{{ $row['due_date'] }}</td>
                    <td class="photo-cell">
                        @if ($row['fix_photo_base64'])
                            <img src="{{ $row['fix_photo_base64'] }}" alt="Foto Perbaikan">
                        @endif
                    </td>
                    <td class="text-center status-badge">{{ $row['status'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 24px; font-style: italic;">
                        Tidak ada temuan ketidaksesuaian (nilai 0) pada periode audit ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>