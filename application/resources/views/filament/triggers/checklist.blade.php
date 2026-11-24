<div>
    @if ($selectedItem)
        <div wire:click="$set('showChecklistModal', true)" class="cursor-pointer flex items-start gap-3 p-3.5 bg-rose-50 border-2 border-rose-300 rounded-xl transition hover:bg-rose-100">
            <div class="w-7 h-7 rounded-md bg-rose-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-[13px] font-bold text-rose-900 leading-snug">{{ $selectedItem->question }}</p>
            </div>
            <div class="text-rose-500 mt-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
        </div>
    @else
        <div wire:click="$set('showChecklistModal', true)" class="cursor-pointer flex flex-col items-center justify-center p-5 border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 hover:bg-slate-100 hover:border-rose-400 transition group">
            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
            </div>
            <span class="text-[13px] font-bold text-slate-600 group-hover:text-rose-600">Cari & Pilih Kategori Temuan</span>
        </div>
    @endif
</div>