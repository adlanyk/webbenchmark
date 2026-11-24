<?php

use App\Models\Audit;
use App\Models\ChecklistItem;
use App\Models\Location;
use App\Services\ImageCompressorService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.public-audit')] class extends Component implements HasSchemas
{
    use InteractsWithSchemas;
    use WithFileUploads;

    public ?array $data = [];

    /** @var TemporaryUploadedFile|null */
    public $photoUpload = null;

    public bool $submitted = false;
    public ?string $submittedNumber = null;
    public ?string $submittedLocation = null;
    public ?string $submittedCategory = null;

    public bool $showLocationModal = false;
    public bool $showChecklistModal = false;
    public string $searchLocation = '';
    public string $searchChecklist = '';

    public function mount(): void
    {
        $this->form->fill([
            'audit_date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Dipanggil otomatis oleh Livewire segera setelah file hasil kompresi browser selesai diunggah.
     */
    public function updatedPhotoUpload(): void
    {
        $this->validate([
            'photoUpload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:12288'],
        ], [
            'photoUpload.image' => 'File harus berupa gambar.',
            'photoUpload.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'photoUpload.max' => 'Ukuran foto maksimal 12 MB.',
        ]);

        // Hapus foto lama jika user mengganti foto
        $this->deleteStoredFindingPhoto();

        // Proses & simpan lewat ImageCompressorService
        $storedPath = ImageCompressorService::compressAndStore($this->photoUpload, 'audit-findings');

        $this->data['finding_photo'] = $storedPath;
        $this->photoUpload = null;
        $this->resetErrorBag('photoUpload');
        $this->resetErrorBag('data.finding_photo');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Langkah 1 • Lokasi & Auditor')
                    ->description('Tentukan siapa auditor dan di mana temuan ditemukan')
                    ->schema([
                        DatePicker::make('audit_date')
                            ->label('Tanggal Inspeksi')
                            ->default(now())
                            ->native(true)
                            ->required(),

                        TextInput::make('reporter_name')
                            ->label('Nama Auditor')
                            ->placeholder('Ketik nama lengkap Anda...')
                            ->required(),

                        Hidden::make('location_id'),

                        Placeholder::make('trigger_location')
                            ->label('Lokasi')
                            ->content(fn (Get $get) => view('filament.triggers.location', [
                                'selectedLocation' => $get('location_id') ? Location::find($get('location_id')) : null,
                            ]))
                            ->columnSpanFull()
                            ->extraAttributes(['class' => '!mt-0 !pt-0']),
                    ]),

                Section::make('Langkah 2 • Kategori & Bukti Temuan')
                    ->description('Pilih item ketidaksesuaian dan lampirkan bukti')
                    ->visible(fn (Get $get) => filled($get('location_id')))
                    ->schema([
                        Hidden::make('checklist_item_id'),
                        Hidden::make('finding_photo'),

                        Placeholder::make('trigger_checklist')
                            ->content(fn (Get $get) => view('filament.triggers.checklist', [
                                'selectedItem' => $get('checklist_item_id') ? ChecklistItem::find($get('checklist_item_id')) : null,
                            ]))
                            ->columnSpanFull()
                            ->extraAttributes(['class' => '!mt-0 !pt-0']),

                        Textarea::make('finding_note')
                            ->label('Keterangan / Uraian Temuan')
                            ->placeholder('Tuliskan kondisi temuan secara singkat & jelas...')
                            ->rows(4)
                            ->columnSpanFull()
                            ->required()
                            ->visible(fn (Get $get) => filled($get('checklist_item_id'))),

                        // KOMPONEN KAMERA + AUTO KOMPRES BROWSER + RETRY ANTI-GAGAL
                        Placeholder::make('camera_uploader')
                            ->label('Foto Kondisi Lapangan *')
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => filled($get('checklist_item_id')))
                            ->content(new HtmlString(<<<'HTML'
                                <div x-data="smartCameraUploader()" class="space-y-3">
                                    <!-- Input File Hidden (Kamera & Galeri) -->
                                    <input
                                        x-ref="cameraInput"
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        capture="environment"
                                        class="hidden"
                                        x-on:change="handleFileSelect($event)"
                                    />

                                    <input
                                        x-ref="galleryInput"
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        x-on:change="handleFileSelect($event)"
                                    />

                                    <!-- STATE A: BELUM ADA FOTO (2 TOMBOL BESAR & JELAS) -->
                                    <div x-show="!previewUrl && !isProcessing"
                                         class="rounded-2xl border-2 border-dashed border-amber-400 bg-amber-50/70 p-4 text-center space-y-3">
                                        <div class="w-12 h-12 mx-auto rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-extrabold text-slate-800">Ambil Bukti Foto Temuan</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Foto otomatis dikompres ringan sebelum dikirim</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 pt-1">
                                            <button type="button"
                                                    x-on:click="$refs.cameraInput.click()"
                                                    class="py-2.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-bold text-xs shadow flex items-center justify-center gap-1.5 transition">
                                                <span>📷</span>
                                                <span>Buka Kamera</span>
                                            </button>
                                            <button type="button"
                                                    x-on:click="$refs.galleryInput.click()"
                                                    class="py-2.5 px-3 rounded-xl bg-white hover:bg-slate-50 active:scale-95 text-slate-700 border border-slate-200 font-bold text-xs shadow-sm flex items-center justify-center gap-1.5 transition">
                                                <span>🖼️</span>
                                                <span>Pilih Galeri</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- STATE B: PREVIEW FOTO + PROGRESS BAR 0% - 100% -->
                                    <div x-show="previewUrl || isProcessing"
                                         x-cloak
                                         class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                                        <!-- Gambar Preview -->
                                        <div x-show="previewUrl" class="relative bg-slate-900">
                                            <img :src="previewUrl" alt="Preview Temuan" class="w-full max-h-60 object-contain mx-auto" />
                                            <span x-show="compressedSizeLabel"
                                                  x-text="compressedSizeLabel"
                                                  class="absolute bottom-2 left-2 px-2.5 py-1 rounded-lg bg-black/70 text-white text-[10px] font-mono"></span>
                                        </div>

                                        <div class="p-3.5 space-y-2.5">
                                            <!-- Progress Bar 0% - 100% -->
                                            <div x-show="isProcessing || isUploading">
                                                <div class="flex items-center justify-between text-xs font-bold text-amber-800 mb-1.5">
                                                    <span x-text="statusText"></span>
                                                    <span class="font-mono bg-amber-100 text-amber-800 px-2 py-0.5 rounded" x-text="progress + '%'"></span>
                                                </div>
                                                <div class="w-full h-2.5 bg-amber-100 rounded-full overflow-hidden">
                                                    <div class="h-full bg-gradient-to-r from-amber-500 to-orange-600 rounded-full transition-all duration-200"
                                                         :style="'width: ' + progress + '%'"></div>
                                                </div>
                                            </div>

                                            <!-- Status Berhasil 100% -->
                                            <div x-show="!isProcessing && !isUploading && uploadSuccess"
                                                 class="flex items-center justify-between gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2 rounded-xl text-xs font-bold">
                                                <span class="flex items-center gap-1.5">
                                                    <span class="w-4 h-4 rounded-full bg-emerald-500 text-white inline-flex items-center justify-center text-[10px]">✓</span>
                                                    <span>Foto siap dikirim (100%)</span>
                                                </span>
                                                <button type="button"
                                                        x-on:click="resetAll()"
                                                        class="px-2.5 py-1 rounded-lg bg-white border border-emerald-300 text-emerald-700 text-[11px] font-bold hover:bg-emerald-100">
                                                    Ganti Foto
                                                </button>
                                            </div>

                                            <!-- Status Gagal + Tombol COBA LAGI (Tanpa Perlu Foto Ulang!) -->
                                            <div x-show="uploadError"
                                                 class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 space-y-2">
                                                <p class="text-xs font-bold" x-text="'⚠ ' + errorMessage"></p>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <button type="button"
                                                            x-show="lastCompressedFile"
                                                            x-on:click="retryUpload()"
                                                            class="py-2 px-3 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow">
                                                        🔄 Coba Kirim Ulang
                                                    </button>
                                                    <button type="button"
                                                            x-on:click="resetAll()"
                                                            class="py-2 px-3 rounded-lg bg-white border border-rose-300 text-rose-700 font-bold text-xs">
                                                        📷 Foto Ulang
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            HTML)),
                    ]),
            ]);
    }

    public function selectLocation(int $id): void
    {
        $this->deleteStoredFindingPhoto();
        $this->data['location_id'] = $id;
        $this->data['checklist_item_id'] = null;
        $this->data['finding_photo'] = null;
        $this->showLocationModal = false;
        $this->searchLocation = '';
        $this->form->fill($this->data);
    }

    public function selectChecklist(int $id): void
    {
        $this->deleteStoredFindingPhoto();
        $this->data['checklist_item_id'] = $id;
        $this->data['finding_photo'] = null;
        $this->showChecklistModal = false;
        $this->searchChecklist = '';
        $this->form->fill($this->data);
    }

    public function resetPhoto(): void
    {
        $this->deleteStoredFindingPhoto();
        $this->data['finding_photo'] = null;
        $this->photoUpload = null;
        $this->resetErrorBag('photoUpload');
        $this->resetErrorBag('data.finding_photo');
    }

    protected function deleteStoredFindingPhoto(): void
    {
        $path = $this->data['finding_photo'] ?? null;

        if (is_string($path) && filled($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function submit(): void
    {
        if (blank($this->data['finding_photo'] ?? null)) {
            Notification::make()
                ->title('Foto kondisi lapangan wajib diunggah terlebih dahulu.')
                ->danger()
                ->send();

            return;
        }

        $state = $this->form->getState();

        $audit = DB::transaction(function () use ($state) {
            do {
                $auditNumber = 'AUD-' . now()->format('Ymd-His')
                    . '-' . strtoupper(Str::random(6));
            } while (Audit::where('audit_number', $auditNumber)->exists());

            $audit = Audit::create([
                'audit_number' => $auditNumber,
                'location_id' => $state['location_id'],
                'reporter_name' => $state['reporter_name'],
                'audit_date' => $state['audit_date'],
                'total_score' => 0,
                'total_items' => 0,
                'passed_items' => 0,
                'findings_count' => 0,
            ]);

            $allItems = ChecklistItem::where('location_id', $state['location_id'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            foreach ($allItems as $item) {
                $isFinding = (int) $item->id === (int) $state['checklist_item_id'];

                $audit->details()->create([
                    'checklist_item_id' => $item->id,
                    'score' => $isFinding ? 0 : 1,
                    'status' => $isFinding ? 'open' : 'none',
                    'finding_note' => $isFinding ? $state['finding_note'] : null,
                    'finding_photo' => $isFinding ? $this->data['finding_photo'] : null,
                ]);
            }

            $audit->recalculateScore();

            return $audit;
        });

        if ($audit->location?->pic) {
            Notification::make()
                ->title("Temuan 5R: {$audit->location->name}")
                ->body("Pelapor ({$audit->reporter_name}): {$state['finding_note']}")
                ->danger()
                ->sendToDatabase($audit->location->pic);
        }

        $selectedItem = ChecklistItem::find($state['checklist_item_id']);

        $this->submitted = true;
        $this->submittedNumber = $audit->audit_number;
        $this->submittedLocation = $audit->location?->name;
        $this->submittedCategory = $selectedItem?->question;
    }

    public function resetForm(): void
    {
        $this->submitted = false;
        $this->submittedNumber = null;
        $this->submittedLocation = null;
        $this->submittedCategory = null;
        $this->photoUpload = null;
        $this->form->fill([
            'audit_date' => now()->format('Y-m-d'),
            'finding_photo' => null,
        ]);
    }
};
?>

@php
    $hasLocation = filled($data['location_id'] ?? null);
    $hasCategory = filled($data['checklist_item_id'] ?? null);
    $currentStep = $hasCategory ? 3 : ($hasLocation ? 2 : 1);
@endphp

<div
    x-data="{ globalUploading: false }"
    x-on:photo-upload-busy.window="globalUploading = $event.detail"
    class="flex-1 flex flex-col relative"
>
    {{-- HERO HEADER --}}
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-amber-900 text-white pt-6 pb-12 px-5 rounded-b-[2rem] shadow-lg relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-36 h-36 bg-amber-500/20 rounded-full blur-2xl"></div>
        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-400 flex items-center justify-center font-extrabold text-slate-950 text-lg shadow-md">5R</div>
                <div>
                    <span class="text-[10px] uppercase tracking-widest text-amber-300 font-bold">Smart Patrol System</span>
                    <h1 class="text-lg font-extrabold tracking-tight leading-snug">Lapor Temuan 5R</h1>
                </div>
            </div>
        </div>
        @if (! $submitted)
            <div class="relative mt-5 bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3">
                <div class="flex items-center justify-between text-[11px] font-semibold">
                    <div class="flex items-center gap-1.5 {{ $currentStep >= 1 ? 'text-amber-300' : 'text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $currentStep >= 1 ? 'bg-amber-400 text-slate-950' : 'bg-slate-700 text-slate-300' }}">1</span>
                        <span>Lokasi</span>
                    </div>
                    <div class="h-0.5 flex-1 mx-2 rounded {{ $currentStep >= 2 ? 'bg-amber-400' : 'bg-slate-700' }}"></div>
                    <div class="flex items-center gap-1.5 {{ $currentStep >= 2 ? 'text-amber-300' : 'text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $currentStep >= 2 ? 'bg-amber-400 text-slate-950' : 'bg-slate-700 text-slate-300' }}">2</span>
                        <span>Kategori</span>
                    </div>
                    <div class="h-0.5 flex-1 mx-2 rounded {{ $currentStep >= 3 ? 'bg-amber-400' : 'bg-slate-700' }}"></div>
                    <div class="flex items-center gap-1.5 {{ $currentStep >= 3 ? 'text-amber-300' : 'text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $currentStep >= 3 ? 'bg-amber-400 text-slate-950' : 'bg-slate-700 text-slate-300' }}">3</span>
                        <span>Kirim</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 px-4 -mt-6 pb-28 z-10">
        @if ($submitted)
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-center text-white">
                    <div class="w-16 h-16 mx-auto bg-white text-emerald-600 rounded-full flex items-center justify-center text-3xl font-black shadow-lg mb-3">✓</div>
                    <h2 class="text-xl font-extrabold">Laporan Terkirim!</h2>
                    <p class="text-xs text-emerald-100 mt-1">Notifikasi otomatis telah dikirim ke PIC Area</p>
                </div>
                <div class="relative flex items-center justify-between px-4 bg-white">
                    <div class="w-5 h-5 -ml-6 bg-slate-50 rounded-full border-r border-slate-200"></div>
                    <div class="border-b-2 border-dashed border-slate-200 flex-1 mx-2"></div>
                    <div class="w-5 h-5 -mr-6 bg-slate-50 rounded-full border-l border-slate-200"></div>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-slate-400 text-xs font-medium">No. Tiket</span>
                        <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg">{{ $submittedNumber }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-slate-400 text-xs font-medium">Lokasi</span>
                        <span class="font-bold text-amber-600">{{ $submittedLocation }}</span>
                    </div>
                    <div class="py-1">
                        <span class="text-slate-400 text-xs font-medium block mb-1">Kategori Temuan</span>
                        <p class="font-semibold text-slate-700 text-xs leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $submittedCategory }}</p>
                    </div>
                    <button type="button"
                            wire:click="resetForm"
                            class="w-full mt-2 py-3.5 bg-slate-900 hover:bg-slate-800 active:scale-[0.98] transition text-white font-bold rounded-2xl shadow-lg flex items-center justify-center gap-2">
                        <span>+ Buat Laporan Baru</span>
                    </button>
                </div>
            </div>
        @else
            <form wire:submit="submit" class="space-y-4 relative z-10">
                {{ $this->form }}

                <div class="fixed bottom-0 left-0 right-0 z-30">
                    <div class="max-w-md mx-auto bg-white/90 backdrop-blur-lg border-t border-slate-200/80 px-4 py-3.5 shadow-[0_-8px_20px_rgba(15,23,42,0.06)]">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                x-bind:disabled="globalUploading"
                                x-bind:class="globalUploading ? 'opacity-60 cursor-not-allowed' : 'hover:brightness-105 active:scale-[0.99]'"
                                class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-extrabold text-sm rounded-2xl shadow-lg transition flex justify-center items-center gap-2">
                            <span x-show="globalUploading">Sedang Mengunggah Foto...</span>
                            <span x-show="!globalUploading" wire:loading.remove wire:target="submit">Kirim Laporan Temuan</span>
                            <span x-show="!globalUploading" wire:loading wire:target="submit">Menyimpan Laporan...</span>
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </main>

    {{-- MODAL LOKASI --}}
    @if ($showLocationModal)
        <div class="fixed inset-0 z-50 flex items-end justify-center">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showLocationModal', false)"></div>
            <div class="relative w-full max-w-md bg-white rounded-t-3xl shadow-2xl flex flex-col max-h-[85vh] animate-slide-up">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 rounded-t-3xl">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Pilih Area</h3>
                        <p class="text-[11px] text-slate-500">Temukan lokasi temuan Anda</p>
                    </div>
                    <button wire:click="$set('showLocationModal', false)" class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-300 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 pb-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchLocation" placeholder="Ketik nama area..." class="w-full pl-9 pr-4 py-2.5 bg-slate-100 border-transparent focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-400 rounded-xl text-sm transition-all outline-none">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-2">
                    @php
                        $locations = \App\Models\Location::where('is_active', true)
                            ->when($this->searchLocation, fn($q) => $q->where('name', 'like', '%'.$this->searchLocation.'%'))
                            ->orderBy('name')->get();
                    @endphp
                    @forelse ($locations as $loc)
                        <button wire:click="selectLocation({{ $loc->id }})"
                                class="w-full text-left p-3 rounded-xl border border-slate-200 bg-white hover:border-amber-400 hover:shadow-md transition-all group flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-[13px] text-slate-800 truncate">{{ $loc->name }}</p>
                            </div>
                        </button>
                    @empty
                        <p class="text-center text-sm text-slate-500 py-10">Area tidak ditemukan</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL KATEGORI --}}
    @if ($showChecklistModal)
        <div class="fixed inset-0 z-50 flex items-end justify-center">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showChecklistModal', false)"></div>
            <div class="relative w-full max-w-md bg-white rounded-t-3xl shadow-2xl flex flex-col max-h-[85vh] animate-slide-up">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 rounded-t-3xl">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Pilih Kategori</h3>
                        <p class="text-[11px] text-slate-500">Item apa yang bermasalah?</p>
                    </div>
                    <button wire:click="$set('showChecklistModal', false)" class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-300 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 pb-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchChecklist" placeholder="Ketik kategori..." class="w-full pl-9 pr-4 py-2.5 bg-slate-100 border-transparent focus:bg-white focus:border-rose-400 focus:ring-2 focus:ring-rose-400 rounded-xl text-sm transition-all outline-none">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-2">
                    @php
                        $items = \App\Models\ChecklistItem::where('location_id', $this->data['location_id'] ?? null)
                            ->where('is_active', true)
                            ->when($this->searchChecklist, fn($q) => $q->where('question', 'like', '%'.$this->searchChecklist.'%'))
                            ->orderBy('sort_order')->get();
                    @endphp
                    @forelse ($items as $item)
                        <button wire:click="selectChecklist({{ $item->id }})"
                                class="w-full text-left p-3 rounded-xl border border-slate-200 bg-white hover:border-rose-400 hover:shadow-md transition-all group flex items-start gap-3">
                            <div class="w-7 h-7 rounded-md bg-rose-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                            <p class="flex-1 text-[12.5px] font-semibold text-slate-700 leading-snug pt-1">{{ $item->question }}</p>
                        </button>
                    @empty
                        <p class="text-center text-sm text-slate-500 py-10">Kategori tidak ditemukan</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    Alpine.data('smartCameraUploader', () => ({
        previewUrl: null,
        compressedSizeLabel: '',
        lastCompressedFile: null,
        isProcessing: false,
        isUploading: false,
        uploadSuccess: false,
        uploadError: false,
        errorMessage: '',
        progress: 0,
        statusText: 'Menyiapkan foto...',

        setBusy(state) {
            window.dispatchEvent(new CustomEvent('photo-upload-busy', { detail: state }));
        },

        async handleFileSelect(event) {
            const file = event.target.files?.[0];
            if (!file) return;

            this.uploadError = false;
            this.uploadSuccess = false;
            this.isProcessing = true;
            this.progress = 10;
            this.statusText = 'Mengompres foto di HP...';
            this.setBusy(true);

            try {
                // 1. Kompres gambar di browser HP (maksimal 1280px, JPEG 78%)
                const compressedFile = await this.compressImageInBrowser(file, 1280, 0.78);
                this.lastCompressedFile = compressedFile;

                // Tampilkan preview lokal instan
                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = URL.createObjectURL(compressedFile);

                const sizeKB = Math.round(compressedFile.size / 1024);
                this.compressedSizeLabel = `Terkompres: ${sizeKB} KB`;
                this.progress = 25;

                // 2. Unggah file ringan (~150 KB) ke Livewire
                await this.uploadToLivewire(compressedFile);
            } catch (err) {
                this.isProcessing = false;
                this.isUploading = false;
                this.uploadError = true;
                this.errorMessage = err?.message || 'Gagal memproses gambar. Coba lagi.';
                this.setBusy(false);
            }
        },

        compressImageInBrowser(file, maxDim = 1280, quality = 0.78) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > maxDim || height > maxDim) {
                            const ratio = Math.min(maxDim / width, maxDim / height);
                            width = Math.round(width * ratio);
                            height = Math.round(height * ratio);
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillRect(0, 0, width, height);
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                reject(new Error('Gagal mengompres gambar di perangkat.'));
                                return;
                            }
                            const cleanName = file.name.replace(/\.[^/.]+$/, '') + '.jpg';
                            resolve(new File([blob], cleanName, { type: 'image/jpeg' }));
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => reject(new Error('Format gambar tidak dapat dibaca.'));
                    img.src = e.target.result;
                };
                reader.onerror = () => reject(new Error('Gagal membaca file dari kamera/galeri.'));
                reader.readAsDataURL(file);
            });
        },

        uploadToLivewire(fileToUpload) {
            return new Promise((resolve) => {
                this.isProcessing = false;
                this.isUploading = true;
                this.uploadError = false;
                this.statusText = 'Mengunggah ke server...';
                this.setBusy(true);

                $wire.upload(
                    'photoUpload',
                    fileToUpload,
                    () => {
                        // Berhasil 100%
                        this.progress = 100;
                        this.isUploading = false;
                        this.uploadSuccess = true;
                        this.setBusy(false);
                        resolve();
                    },
                    () => {
                        // Gagal koneksi / timeout
                        this.isUploading = false;
                        this.uploadError = true;
                        this.errorMessage = 'Koneksi terputus saat mengunggah. Tekan "Coba Kirim Ulang".';
                        this.setBusy(false);
                        resolve();
                    },
                    (event) => {
                        // Progress 25% -> 99%
                        const pct = event.detail.progress || 0;
                        this.progress = Math.max(25, Math.min(99, pct));
                        if (pct >= 99) {
                            this.statusText = 'Menyimpan di server...';
                        }
                    }
                );
            });
        },

        async retryUpload() {
            if (!this.lastCompressedFile) return;
            await this.uploadToLivewire(this.lastCompressedFile);
        },

        resetAll() {
            if (this.previewUrl) {
                URL.revokeObjectURL(this.previewUrl);
            }
            this.previewUrl = null;
            this.compressedSizeLabel = '';
            this.lastCompressedFile = null;
            this.isProcessing = false;
            this.isUploading = false;
            this.uploadSuccess = false;
            this.uploadError = false;
            this.progress = 0;
            if (this.$refs.cameraInput) this.$refs.cameraInput.value = '';
            if (this.$refs.galleryInput) this.$refs.galleryInput.value = '';
            this.setBusy(false);
            $wire.resetPhoto();
        }
    }));
</script>
@endscript