<?php

namespace App\Filament\Resources\Audits\Schemas;

use App\Models\AuditDetail;
use App\Models\ChecklistItem;
use App\Models\Location;
use App\Services\ImageCompressorService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AuditForm
{
    protected static function canManageAll(): bool
    {
        $user = auth()->user();

        return (bool) ($user && ($user->hasAnyRole(['verifikator', 'super_admin']) || blank($user->department_id)));
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // =========================================================
                // 1A. HEADER INPUT (SAAT CREATE) - FULL WIDTH
                // =========================================================
                Section::make('Informasi Inspeksi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ])
                    ->visibleOn('create')
                    ->schema([
                        Select::make('location_id')
                            ->label('Lokasi Inspeksi')
                            ->options(function () {
                                $user = auth()->user();

                                return Location::with('department')
                                    ->where('is_active', true)
                                    ->when(! self::canManageAll(), function ($query) use ($user) {
                                        $query->where(function ($q) use ($user) {
                                            $q->where('department_id', $user->department_id)
                                                ->orWhereHas('checklistItems', fn ($itemQ) => $itemQ->where('department_id', $user->department_id));
                                        });
                                    })
                                    ->orderBy('code')
                                    ->get()
                                    ->mapWithKeys(fn ($loc) => [
                                        $loc->id => ($loc->department ? "[{$loc->department->code}] " : '') . "{$loc->code}. {$loc->name}",
                                    ]);
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (! $state) {
                                    $set('details', []);

                                    return;
                                }

                                $items = ChecklistItem::where('location_id', $state)
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->get()
                                    ->map(fn ($item) => [
                                        'checklist_item_id' => $item->id,
                                        'question_label' => "{$item->sort_order}. {$item->question}",
                                        'score' => 1,
                                        'finding_note' => null,
                                        'finding_photo' => null,
                                        'status' => 'none',
                                    ])
                                    ->toArray();

                                $set('details', $items);
                            }),

                        DatePicker::make('audit_date')
                            ->label('Tanggal Inspeksi')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->default(now())
                            ->required(),

                        TextInput::make('reporter_name')
                            ->label('Nama Auditor')
                            ->default(fn () => auth()->user()?->name)
                            ->required(),
                    ]),

                // =========================================================
                // 1B. SUMMARY BAR (SAAT EDIT) - FULL WIDTH KE KANAN
                // =========================================================
                Section::make()
                    ->columnSpanFull()
                    ->visibleOn('edit')
                    ->columns([
                        'default' => 2,
                        'md' => 4,
                    ])
                    ->schema([
                        Placeholder::make('summary_location')
                            ->label('Lokasi Area')
                            ->content(fn ($record) => new HtmlString(
                                '<span class="text-base font-bold text-gray-950 dark:text-white">'
                                . e($record?->location ? "{$record->location->code}. {$record->location->name}" : '-')
                                . '</span>'
                            )),

                        Placeholder::make('summary_date')
                            ->label('Tanggal Audit')
                            ->content(fn ($record) => new HtmlString(
                                '<span class="text-sm font-semibold text-gray-800 dark:text-gray-200">'
                                . e($record?->audit_date?->translatedFormat('d F Y') ?? '-')
                                . '</span>'
                            )),

                        Placeholder::make('summary_auditor')
                            ->label('Auditor')
                            ->content(fn ($record) => new HtmlString(
                                '<span class="text-sm font-semibold text-gray-800 dark:text-gray-200">'
                                . e($record?->reporter_name ?? '-')
                                . '</span>'
                            )),

                        Placeholder::make('summary_score')
                            ->label('Skor & Temuan')
                            ->content(function ($record) {
                                if (! $record) {
                                    return '-';
                                }
                                $percent = number_format((float) $record->total_score * 100, 0);
                                $findings = (int) $record->findings_count;
                                $badgeStyle = $findings > 0
                                    ? 'background-color: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);'
                                    : 'background-color: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3);';

                                return new HtmlString(
                                    "<div style='display:flex; align-items:center; gap:8px;'>
                                        <span class='text-base font-bold'>{$percent}%</span>
                                        <span style='padding:2px 10px; font-size:12px; font-weight:600; border-radius:9999px; {$badgeStyle}'>{$findings} Temuan</span>
                                    </div>"
                                );
                            }),
                    ]),

                // =========================================================
                // 2. DAFTAR TEMUAN & PERBAIKAN PIC - FULL WIDTH DI BAWAH
                // =========================================================
                Section::make(fn (string $operation) => $operation === 'edit'
                    ? 'Daftar Temuan & Perbaikan PIC'
                    : 'Checklist Penilaian Lapangan')
                    ->icon(fn (string $operation) => $operation === 'edit'
                        ? 'heroicon-o-wrench-screwdriver'
                        : 'heroicon-o-list-bullet')
                    ->description(fn (string $operation) => $operation === 'edit'
                        ? 'Hanya menampilkan item bermasalah (nilai 0). Lengkapi Action Plan, Due Date, Foto Perbaikan, dan ubah Status.'
                        : 'Pilih Sesuai (1) atau Temuan (0). Form keterangan & foto akan muncul otomatis jika dipilih Temuan.')
                    ->columnSpanFull()
                    ->schema([
                        Placeholder::make('no_findings_info')
                            ->hiddenLabel()
                            ->content(new HtmlString(
                                '<div style="display:flex; align-items:center; gap:12px; padding:16px; border-radius:12px; background-color:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); color:#22c55e;">
                                    <span style="font-size:20px; font-weight:bold;">✓</span>
                                    <div>
                                        <div style="font-weight:600;">Area Aman — Tidak Ada Temuan</div>
                                        <div style="font-size:13px; opacity:0.85;">Seluruh item penilaian pada area ini bernilai 1 (Sesuai).</div>
                                    </div>
                                </div>'
                            ))
                            ->visible(function (?string $operation, $record) {
                                if ($operation !== 'edit' || ! $record) {
                                    return false;
                                }

                                $user = auth()->user();

                                return ! $record->details()
                                    ->where('score', 0)
                                    ->when(! self::canManageAll(), fn ($q) => $q->whereHas('checklistItem', fn ($itemQ) => $itemQ->where('department_id', $user->department_id)))
                                    ->exists();
                            }),

                        Repeater::make('details')
                            ->relationship(
                                modifyQueryUsing: function (Builder $query, string $operation) {
                                    $user = auth()->user();

                                    if ($operation === 'edit') {
                                        $query->where('score', 0);
                                    }

                                    if (! self::canManageAll() && $user?->department_id) {
                                        $query->whereHas('checklistItem', fn (Builder $q) => $q->where('department_id', $user->department_id));
                                    }

                                    return $query;
                                }
                            )
                            ->hiddenLabel()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                if (! empty($state['question_label'])) {
                                    return $state['question_label'];
                                }

                                if (! empty($state['checklist_item_id'])) {
                                    $item = ChecklistItem::with('department')->find($state['checklist_item_id']);
                                    if ($item) {
                                        $dept = $item->department ? " [{$item->department->code}]" : '';

                                        return "{$item->sort_order}. {$item->question}{$dept}";
                                    }
                                }

                                return 'Item Temuan';
                            })
                            ->schema([
                                Hidden::make('checklist_item_id'),
                                Hidden::make('question_label'),

                                // HEADER BARIS ITEM
                                Grid::make(12)
                                    ->schema([
                                        ToggleButtons::make('score')
                                            ->hiddenLabel()
                                            ->options([
                                                1 => 'Sesuai (1)',
                                                0 => 'Temuan (0)',
                                            ])
                                            ->colors([
                                                1 => 'success',
                                                0 => 'danger',
                                            ])
                                            ->icons([
                                                1 => 'heroicon-m-check',
                                                0 => 'heroicon-m-x-mark',
                                            ])
                                            ->grouped()
                                            ->default(1)
                                            ->required()
                                            ->live()
                                            ->disabled(fn (string $operation) => $operation === 'edit' && ! self::canManageAll())
                                            ->afterStateUpdated(fn ($state, Set $set) => $set('status', (int) $state === 0 ? 'open' : 'none'))
                                            ->columnSpan([
                                                'default' => 12,
                                                'md' => 4,
                                            ]),
                                    ]),

                                // SPLIT VIEW: KIRI (TEMUAN LAPANGAN) vs KANAN (TINDAK LANJUT PIC)
                                Grid::make([
                                    'default' => 1,
                                    'lg' => 2,
                                ])
                                    ->visible(fn (Get $get) => (int) $get('score') === 0)
                                    ->schema([
                                        // PANEL KIRI: BUKTI TEMUAN
                                        Section::make('Temuan Lapangan')
                                            ->icon('heroicon-o-exclamation-circle')
                                            ->compact()
                                            ->columnSpan(fn (string $operation) => $operation === 'edit' ? 1 : 2)
                                            ->schema([
                                                Textarea::make('finding_note')
                                                    ->label('Uraian Temuan')
                                                    ->placeholder('Jelaskan ketidaksesuaian yang ditemukan...')
                                                    ->rows(3)
                                                    ->disabled(fn (string $operation) => $operation === 'edit' && ! self::canManageAll())
                                                    ->required(fn (Get $get) => (int) $get('score') === 0),

                                                FileUpload::make('finding_photo')
                                                    ->label('Foto Kondisi Awal (Before)')
                                                    ->image()
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                                    ->maxSize(10240)
                                                    ->imageResizeMode('contain')
                                                    ->imageResizeTargetWidth('1280')
                                                    ->imageResizeTargetHeight('1280')
                                                    ->imageResizeUpscale(false)
                                                    ->imagePreviewHeight('220')
                                                    ->directory('audit-findings')
                                                    ->disk('public')
                                                    ->visibility('public')
                                                    ->extraInputAttributes(['capture' => 'environment'])
                                                    ->openable()
                                                    ->disabled(fn (string $operation) => $operation === 'edit' && ! self::canManageAll())
                                                    ->required(fn (Get $get) => (int) $get('score') === 0)
                                                    // ✅ TAMBAHKAN OPTIMIZE DI SINI
                                                    ->optimize('webp', 75)
                                                    ->saveUploadedFileUsing(
                                                        fn (TemporaryUploadedFile $file): string => ImageCompressorService::compressAndStore($file, 'audit-findings')
                                                    ),
                                            ]),

                                        // PANEL KANAN: TINDAK LANJUT PIC
                                        Section::make('Tindak Lanjut Perbaikan (PIC)')
                                            ->icon('heroicon-o-check-badge')
                                            ->compact()
                                            ->columnSpan(1)
                                            ->visibleOn('edit')
                                            ->schema([
                                                ToggleButtons::make('status')
                                                    ->label('Status Perbaikan')
                                                    ->options(function () {
                                                        $options = [
                                                            'open' => 'Belum Diperbaiki',
                                                            'in_progress' => 'Sedang Diperbaiki',
                                                            'closed' => 'Selesai',
                                                        ];

                                                        // if (self::canManageAll()) {
                                                        //     $options['closed'] = 'Selesai';
                                                        // }

                                                        return $options;
                                                    })
                                                    ->colors([
                                                        'open' => 'danger',
                                                        'in_progress' => 'warning',
                                                        'closed' => 'success',
                                                    ])
                                                    ->icons([
                                                        'open' => 'heroicon-m-x-circle',
                                                        'in_progress' => 'heroicon-m-wrench-screwdriver',
                                                        'closed' => 'heroicon-m-check-circle',
                                                    ])
                                                    ->grouped()
                                                    ->formatStateUsing(fn ($state) => in_array($state, ['open', 'in_progress', 'closed'], true) ? $state : 'open')
                                                    ->default('open')
                                                    ->required(),

                                                DatePicker::make('due_date')
                                                    ->label('Target Selesai (Due Date)')
                                                    ->native(false)
                                                    ->displayFormat('d M Y')
                                                    ->minDate(fn ($record, Get $get) => $record?->audit?->audit_date ?? $get('../../audit_date') ?? now()->toDateString())
                                                    ->afterOrEqual(fn ($record, Get $get) => $record?->audit?->audit_date ?? $get('../../audit_date') ?? now()->toDateString())
                                                    ->validationMessages([
                                                        'after_or_equal' => 'Target selesai tidak boleh lebih awal dari tanggal audit.',
                                                    ])
                                                    ->required(),

                                                Textarea::make('action_plan')
                                                    ->label('Action Plan (Tindakan Perbaikan)')
                                                    ->placeholder('Tuliskan langkah perbaikan yang dilakukan...')
                                                    ->rows(3)
                                                    ->required(),

                                               FileUpload::make('fix_photo')
    ->label('Foto Sesudah Perbaikan (After) *')
    ->image()
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
    ->maxSize(10240)
    ->imageResizeMode('contain')
    ->imageResizeTargetWidth('1280')
    ->imageResizeTargetHeight('1280')
    ->imageResizeUpscale(false)
    ->directory('audit-fixes')
    ->disk('public')
    ->visibility('public')
    ->required()
    ->openable()
    ->optimize('webp', 75)
     ->live()
    ->afterStateUpdated(function ($state, Set $set, Get $get) {
        // Jika ada file fix_photo yang diunggah
        if (! empty($state)) {
            $set('status', 'closed');
        }
    }), // Hapus saveUploadedFileUsing
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}