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

🚀 Cara Menjalankan Project (Local Development)
Prasyarat
Pastikan Anda sudah menginstal:

Docker

Docker Compose

Git

Langkah-langkah:
Clone repository ini:

bash
git clone https://github.com/adlanyk/webbenchmark.git
cd webbenchmark
Setup Environment File:
Salin file .env.example menjadi .env (jika belum ada di folder utama) dan sesuaikan konfigurasi database.

bash
cp .env.example .env
Jalankan Docker Compose:

bash
docker-compose up -d --build
Masuk ke container aplikasi (jika diperlukan):

bash
docker exec -it webbenchmark-app bash
Install dependensi Laravel:

bash
composer install
Generate Application Key:

bash
php artisan key:generate
Jalankan Migrasi Database & Seeder:

bash
php artisan migrate --seed
Akses Aplikasi:
Buka browser dan akses http://localhost atau sesuai dengan port yang diatur di docker-compose.yaml.

✨ Fitur Utama
Manajemen Lokasi Audit: Pengelompokan area berdasarkan departemen.

Checklist Dinamis: Penilaian item berdasarkan kriteria 5R (Sesuai / Tidak Sesuai).

Upload Foto Temuan & Perbaikan: Dilengkapi dengan optimasi gambar (resize/compress).

Manajemen Tindak Lanjut (PIC): Penentuan Action Plan, Due Date, dan status perbaikan.

Dashboard Visual (PDF): Cetak laporan dalam format PDF landscape yang dilengkapi grafik KPI, Donut Chart, dan Line Chart (menggunakan DomPDF).

🔒 Keamanan (Penting)
Jangan pernah meng-commit file .env asli ke repository ini karena berisi kredensial sensitif (password database, API keys).

Selalu gunakan file .env.example sebagai template.

Folder vendor dan node_modules sudah di-ignore secara otomatis.