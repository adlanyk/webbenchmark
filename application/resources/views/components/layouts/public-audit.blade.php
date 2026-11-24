<!DOCTYPE html>
<html lang="id" class="fi min-h-screen">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <title>5R Smart Patrol</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

   <style>
    [x-cloak] { display: none !important; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }

    /* FIX SVG & ICON */
    svg { max-width: 100%; max-height: 100%; }
    .fi-section-icon svg { width: 1.5rem !important; height: 1.5rem !important; }
    .fi-input-wrp-prefix svg { width: 1.25rem !important; height: 1.25rem !important; }

    .fi-fo-field-wrp:has(.fi-fo-placeholder) > .fi-fo-field-wrp-label { display: none !important; }
    .fi-fo-field-wrp { width: 100% !important; }

    /* SECTION & SPACING */
    .fi-section {
        border-radius: 1.25rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 8px 30px -4px rgba(15, 23, 42, 0.08) !important;
        background: #ffffff !important;
        padding-bottom: 1.5rem;
        margin-bottom: 1rem;
    }
    .fi-section-header {
        background: #ffffff !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 1.25rem 1.25rem 0.75rem 1.25rem !important;
    }
    .fi-section-header-heading {
        font-size: 1rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        letter-spacing: -0.01em;
    }
    .fi-section-header-description {
        font-size: 0.75rem !important;
        color: #64748b !important;
        margin-top: 0.25rem !important;
        line-height: 1.4;
    }
    .fi-section-content-ctn { padding: 1rem 1.25rem !important; }
    .fi-section-content { gap: 1.5rem !important; display: flex !important; flex-direction: column !important; }

    /* LABEL FORM */
    .fi-fo-field-wrp-label { margin-bottom: 0.6rem !important; padding-bottom: 0 !important; }
    .fi-fo-field-wrp-label label {
        font-weight: 700 !important;
        color: #334155 !important;
        font-size: 0.85rem !important;
        margin-bottom: 0 !important;
        letter-spacing: 0.01em;
    }

    /* INPUT & TEXTAREA */
    .fi-input, .fi-select-input, .fi-fo-date-time-picker-input, textarea {
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        background-color: #f8fafc !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 0.85rem !important;
        padding: 1rem 1.25rem !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02) !important;
        width: 100% !important;
    }
    .fi-input:focus, .fi-select-input:focus, textarea:focus {
        background-color: #ffffff !important;
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15) !important;
        outline: none !important;
    }
    .fi-input::placeholder, textarea::placeholder { color: #94a3b8 !important; font-weight: 500 !important; }

    /* ============================================================ */
    /* CUSTOM FILEPOND UPLOADER (FULL IMAGE CLICKABLE)              */
    /* ============================================================ */
    .fi-fo-file-upload .filepond--root {
        border-radius: 1rem !important;
        min-height: 150px !important;
        background-color: #f0fdf4 !important;
        border: 2px dashed #34d399 !important;
        transition: all 0.2s ease;
        overflow: hidden !important;
    }
    .fi-fo-file-upload .filepond--root:hover {
        background-color: #dcfce7 !important;
        border-color: #10b981 !important;
    }
    
    /* Label saat belum ada gambar */
    .fi-fo-file-upload .filepond--drop-label {
        color: transparent !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 100% !important;
        min-height: 150px !important;
        cursor: pointer !important;
    }
    .fi-fo-file-upload .filepond--label-action { display: none !important; }

    /* Ikon & Teks Custom saat belum ada gambar */
    .fi-fo-file-upload .filepond--drop-label::before {
        content: "";
        display: block;
        width: 48px; height: 48px;
        background-color: #10b981;
        border-radius: 12px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z'/%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M15 13a3 3 0 11-6 0 3 3 0 016 0z'/%3E%3C/svg%3E");
        background-size: 24px;
        background-position: center;
        background-repeat: no-repeat;
        position: absolute;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .fi-fo-file-upload .filepond--drop-label::after {
        content: "Ambil Bukti Foto\A Klik di sini untuk membuka kamera atau galeri";
        white-space: pre-wrap;
        text-align: center;
        color: #065f46;
        font-weight: 700;
        font-size: 13px;
        line-height: 1.5;
        position: absolute;
        top: 55%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        padding: 0 1rem;
        pointer-events: none;
    }

    /* Info & Action Button FilePond (Diperbaiki agar tidak raksasa) */
    .fi-fo-file-upload .filepond--file-info,
    .fi-fo-file-upload .filepond--file-action-button {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    .fi-fo-file-upload .filepond--file-action-button svg {
        width: 1.25rem !important;
        height: 1.25rem !important;
        max-width: 1.25rem !important;
        max-height: 1.25rem !important;
    }

    /* ============================================================ */
    /* TRIK AGAR FULL GAMBAR BISA DIKLIK (OPEN IN NEW TAB)          */
    /* ============================================================ */
    .fi-fo-file-upload .filepond--image-preview {
        cursor: zoom-in !important; /* Kursor jadi kaca pembesar */
        position: relative !important;
    }
    .fi-fo-file-upload .filepond--image-preview img {
        pointer-events: none !important; /* Klik menembus ke container */
    }
    
    /* Sembunyikan ikon kecil "openable" bawaan Filament agar bersih */
    .fi-fo-file-upload .filepond--file-action-button[title="Open"] {
        display: none !important;
    }

    /* ANIMASI */
    @keyframes slideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideUpModal { from { transform: translateY(100%); } to { transform: translateY(0); } }
    .animate-slide-up { animation: slideUpModal 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .fi-section { animation: slideUp 0.35s ease-out; }
</style>

    @filamentStyles
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="fi-body text-slate-900 antialiased selection:bg-amber-500 selection:text-white">
    <div class="min-h-screen max-w-md mx-auto bg-slate-50 shadow-2xl flex flex-col relative overflow-x-hidden">
        {{ $slot }}
    </div>

    @filamentScripts
    @livewireScripts
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            // 1. Cek apakah user mengklik area gambar preview
            const previewContainer = e.target.closest('.filepond--image-preview');
            if (!previewContainer) return;

            // 2. Ambil URL gambar asli
            let imageUrl = previewContainer.getAttribute('data-image-url'); // Coba dari atribut
            if (!imageUrl) {
                const bg = previewContainer.style.backgroundImage;
                if (bg && bg !== 'none') {
                    imageUrl = bg.slice(5, -2).replace(/["']/g, ""); // Ekstrak url("...")
                }
            }

            // 3. Buka di tab baru jika URL valid
            if (imageUrl && imageUrl.includes('/storage/')) {
                e.preventDefault();
                e.stopPropagation();
                window.open(imageUrl, '_blank');
            }
        });
    });
</script>
    
</body>
</html>