# 📊 Webbenchmark - Audit 5R & Standard Kebersihan

Aplikasi berbasis web (Laravel + Docker) untuk mencatat audit 5R dan standard kebersihan Plant. Memudahkan auditor mencatat temuan, upload foto, dan memantau perbaikan PIC.

## 🛠️ Tech Stack
- **Framework:** Laravel (PHP)
- **Database:** MySQL / MariaDB
- **UI Admin:** Filament PHP
- **Environment:** Docker & Docker Compose

## 📁 Struktur Direktori
- `application/` : Kode sumber utama Laravel
- `db/` : Inisialisasi database
- `docker-config/` : Konfigurasi web server
- `docker-compose.yaml` : Konfigurasi Docker

## 🚀 Cara Menjalankan
Pastikan Docker dan Git sudah terinstal.

```bash
# 1. Clone repo
git clone https://github.com/adlanyk/webbenchmark.git
cd webbenchmark

# 2. Setup environment
cp .env.example .env

# 3. Jalankan Docker
docker-compose up -d --build

# 4. Masuk ke container & install dependensi
docker exec -it webbenchmark-app bash
composer install
php artisan key:generate
php artisan migrate --seed