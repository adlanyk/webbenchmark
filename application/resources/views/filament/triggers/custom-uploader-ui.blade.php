<div x-data="smartUploader()" class="mt-2">
    <!-- STATE A: BELUM ADA FOTO -->
    <div x-show="!previewUrl && !isProcessing" 
         class="rounded-2xl border-2 border-dashed border-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20 p-5 text-center space-y-3 transition-all hover:bg-emerald-50 dark:hover:bg-emerald-950/40 cursor-pointer"
         x-on:click="$refs.realFileInput.click()">
        <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Ambil Bukti Foto Perbaikan</p>
            <p class="text-xs text-slate-500 mt-0.5">Klik di sini untuk membuka kamera atau galeri</p>
        </div>
    </div>

    <!-- STATE B: PROSES / PREVIEW -->
    <div x-show="previewUrl || isProcessing" x-cloak 
         class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
        
        <div x-show="previewUrl" class="relative bg-slate-900 flex justify-center">
            <img :src="previewUrl" class="max-h-64 object-contain" />
            <span x-show="compressedSizeLabel" x-text="compressedSizeLabel" class="absolute bottom-2 left-2 px-2.5 py-1 rounded-lg bg-black/70 text-white text-[10px] font-mono"></span>
        </div>

        <div class="p-4 space-y-3">
            <!-- Progress Bar -->
            <div x-show="isProcessing || isUploading">
                <div class="flex items-center justify-between text-xs font-bold text-emerald-700 dark:text-emerald-400 mb-1.5">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="statusText"></span>
                    </span>
                    <span class="font-mono bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 px-2 py-0.5 rounded" x-text="progress + '%'"></span>
                </div>
                <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full transition-all duration-300 ease-out" 
                         :style="'width: ' + progress + '%'"></div>
                </div>
            </div>

            <!-- Status Sukses -->
            <div x-show="!isProcessing && !isUploading && uploadSuccess && previewUrl" 
                 class="flex items-center justify-between gap-2 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-3 py-2.5 rounded-xl text-xs font-bold">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Foto berhasil diunggah</span>
                </span>
                <button type="button" x-on:click.prevent="$refs.realFileInput.click()" 
                        class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-emerald-300 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 text-[11px] hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                    Ganti Foto
                </button>
            </div>

            <!-- Status Error -->
            <div x-show="uploadError" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 space-y-2">
                <p class="text-xs font-bold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span x-text="errorMessage"></span>
                </p>
            </div>
        </div>
    </div>
</div>