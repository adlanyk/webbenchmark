<div align="center">

# 📊 Webbenchmark
### Audit 5R & Standard Kebersihan Plant

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com)
[![Filament](https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logo=laravel&logoColor=black)](https://filamentphp.com)
[![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)

*Aplikasi berbasis web untuk melakukan audit 5R (Seiri, Seiton, Seiso, Seiketsu, Shitsuke) dan pencatatan Standard Kebersihan di lingkungan Plant secara efisien dan terpusat.*

</div>

---

## 📖 Tentang Project
**Webbenchmark** dibangun menggunakan **Laravel** dan dikontainerisasi menggunakan **Docker** untuk memastikan *environment* yang konsisten di berbagai mesin. Aplikasi ini memudahkan auditor dalam mencatat temuan lapangan, mengunggah bukti foto, dan memantau tindak lanjut perbaikan oleh PIC (Person In Charge).

## 🛠️ Teknologi yang Digunakan
| Kategori | Teknologi |
| :--- | :--- |
| **Framework** | Laravel (PHP) |
| **Database** | MySQL / MariaDB (via Docker) |
| **Web Server** | Nginx / Apache (via Docker) |
| **Containerization** | Docker & Docker Compose |
| **UI Framework** | Filament PHP (Admin Panel) |

## 📁 Struktur Direktori
```text
webbenchmark/
├── application/        # Kode sumber utama Laravel (App, Routes, Resources, dll)
├── db/                 # Inisialisasi database / volume data
├── docker-config/      # Konfigurasi Nginx/Apache/PHP
├── .env.example        # Template environment variables
├── custom-php.ini      # Konfigurasi custom PHP
└── docker-compose.yaml # Konfigurasi Docker Compose


🚀 Cara Menjalankan Project
<details> <summary><b>Klik untuk melihat panduan instalasi (Local Development)</b></summary>
Prasyarat
Pastikan Anda sudah menginstal:

Docker

Docker Compose

Git

Langkah-langkah:
1. Clone repository ini:

bash
git clone https://github.com/adlanyk/webbenchmark.git
cd webbenchmark
2. Setup Environment File:
Salin file .env.example menjadi .env dan sesuaikan konfigurasi database.

bash
cp .env.example .env
3. Jalankan Docker Compose:

bash
docker-compose up -d --build
4. Masuk ke container aplikasi (opsional):

bash
docker exec -it webbenchmark-app bash
5. Install dependensi Laravel:

bash
composer install
6. Generate Application Key:

bash
php artisan key:generate
7. Jalankan Migrasi Database & Seeder:

bash
php artisan migrate --seed
8. Akses Aplikasi:
Buka browser dan akses http://localhost atau sesuai dengan port yang diatur di docker-compose.yaml.

</details>
✨ Fitur Utama
☑ Manajemen Lokasi Audit — Pengelompokan area berdasarkan departemen.
☑ Checklist Dinamis — Penilaian item berdasarkan kriteria 5R (Sesuai / Tidak Sesuai).
☑ Upload Foto Temuan & Perbaikan — Dilengkapi dengan optimasi gambar otomatis (resize/compress).
☑ Manajemen Tindak Lanjut (PIC) — Penentuan Action Plan, Due Date, dan status perbaikan.
☑ Dashboard Visual (PDF) — Cetak laporan format PDF landscape dengan grafik KPI, Donut Chart, dan Line Chart via DomPDF.
🔒 Keamanan (Penting)
[!WARNING]
Jangan pernah meng-commit file .env asli ke repository ini karena berisi kredensial sensitif (password database, API keys, dll).

Selalu gunakan file .env.example sebagai template publik.

Folder vendor dan node_modules sudah di-ignore secara otomatis.

Jangan lupa untuk mengatur Personal Access Token (PAT) saat melakukan push dari server.