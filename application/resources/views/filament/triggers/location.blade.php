<div>
    @if ($selectedLocation)
        <div wire:click="$set('showLocationModal', true)" class="cursor-pointer flex items-center gap-3 p-3.5 bg-amber-50 border-2 border-amber-300 rounded-xl transition hover:bg-amber-100">
            <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-bold text-amber-900 truncate leading-tight">{{ $selectedLocation->name }}</p>
                <!-- <p class="text-[11px] font-semibold text-amber-700/80 mt-0.5">{{ $selectedLocation->pic ? 'PIC: ' . $selectedLocation->pic->name : 'Belum ada PIC' }}</p> -->
            </div>
            <div class="text-amber-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
        </div>
    @else
        <div wire:click="$set('showLocationModal', true)" class="cursor-pointer flex flex-col items-center justify-center p-5 border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 hover:bg-slate-100 hover:border-amber-400 transition group">
            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-500 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
            </div>
            <span class="text-[13px] font-bold text-slate-600 group-hover:text-amber-600">Cari & Pilih Area Lokasi</span>
        </div>
    @endif
</div>