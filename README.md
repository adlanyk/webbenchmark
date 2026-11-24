# 📊 Webbenchmark - Audit 5R & Standard Kebersihan

Aplikasi berbasis web untuk melakukan audit 5R (Seiri, Seiton, Seiso, Seiketsu, Shitsuke) dan pencatatan Standard Kebersihan di lingkungan Plant. Project ini dibangun menggunakan **Laravel** dan dikontainerisasi menggunakan **Docker**.

## 🛠️ Teknologi yang Digunakan
- **Framework:** Laravel (PHP)
- **Database:** MySQL / MariaDB (via Docker)
- **Web Server:** Nginx / Apache (via Docker)
- **Containerization:** Docker & Docker Compose
- **UI Framework:** Filament PHP (Admin Panel)

## 📁 Struktur Direktori
```text
webbenchmark/
├── application/        # Kode sumber utama Laravel (App, Routes, Resources, dll)
├── db/                 # Inisialisasi database / volume data
├── docker-config/      # Konfigurasi Nginx/Apache/PHP
├── .env.example        # Template environment variables
├── custom-php.ini      # Konfigurasi custom PHP
└── docker-compose.yaml # Konfigurasi Docker Compose
