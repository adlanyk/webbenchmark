<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuditMasterSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. Seed Master Department
        // ==========================================
        $departmentsData = [
            ['code' => 'HRD',     'name' => 'HRD'],
            ['code' => 'HSE',     'name' => 'HSE'],
            ['code' => 'LAB',     'name' => 'LAB'],
            ['code' => 'FG',      'name' => 'FG'],
            ['code' => 'PREMIX',  'name' => 'PREMIX'],
            ['code' => 'RAWMATE', 'name' => 'RAWMATE'],
            ['code' => 'MTC',     'name' => 'MAINTENANCE'],
            ['code' => 'PRDSILO', 'name' => 'PRODUKSI (SILO)'],
            ['code' => 'PRD',     'name' => 'PRODUKSI'],
        ];

        $deptMap = [];
        foreach ($departmentsData as $dept) {
            $record = Department::updateOrCreate(
                ['code' => $dept['code']],
                ['name' => $dept['name']]
            );
            $deptMap[$dept['code']] = $record->id;
        }

        // ==========================================
        // 2. Seed User Admin & User PIC per Department
        // ==========================================
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Audit 5R',
                'password' => Hash::make('password'),
                'department_id' => null,
                'email_verified_at' => now(),
            ]
        );

        $deptUsers = [
            ['dept' => 'HRD',     'name' => 'PIC HRD',             'email' => 'hrd@gmail.com'],
            ['dept' => 'HSE',     'name' => 'PIC HSE',             'email' => 'hse@gmail.com'],
            ['dept' => 'LAB',     'name' => 'PIC LAB',             'email' => 'lab@gmail.com'],
            ['dept' => 'FG',      'name' => 'PIC Finish Good',     'email' => 'fg@gmail.com'],
            ['dept' => 'PREMIX',  'name' => 'PIC Premix',          'email' => 'premix@gmail.com'],
            ['dept' => 'RAWMATE', 'name' => 'PIC Raw Material',    'email' => 'rawmate@gmail.com'],
            ['dept' => 'MTC',     'name' => 'PIC Maintenance',     'email' => 'mtc@gmail.com'],
            ['dept' => 'PRDSILO', 'name' => 'PIC Produksi (Silo)', 'email' => 'prdsilo@gmail.com'],
            ['dept' => 'PRD',     'name' => 'PIC Produksi',        'email' => 'prd@gmail.com'],
        ];

        $picUserMap = [];
        foreach ($deptUsers as $u) {
            $deptId = $deptMap[$u['dept']] ?? null;

            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'department_id' => $deptId,
                    'email_verified_at' => now(),
                ]
            );

            if ($deptId) {
                $picUserMap[$deptId] = $user->id;
            }
        }

        // ==========================================
        // 3. Seed 32 Lokasi & Checklist Items
        // ==========================================
        $locationsData = [
            // 1. HRD
            [
                'code' => 'A', 'dept' => 'HRD', 'name' => 'POS SECURITY',
                'items' => [
                    'GERBANG PLANT (TIDAK ADA RUSAK DAN BERSIH)',
                    'RAMBU K3 "DILARANG MEROKOK" (BERSIH)',
                    'RAMBU K3 "PERATURAN PERUSAHAAN" (BERSIH)',
                    'BENDERA INDONESIA TERPASANG (KONDISI BAIK)',
                    'BENDERA PERUSAHAAN TERPASANG (KONDISI BAIK)',
                    'BENDERA K3 TERPASANG (KONDISI BAIK)',
                    'BUKU / FORM TAMU (RAPI DAN TERISI)',
                    'TEMPAT CUCI TANGAN (BERSIH)',
                    'ALAT DAN KUNCI AMANO (LENGKAP DAN RAPI)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'TERSEDIA HANDYTALKY (BERFUNGSI)',
                    'TERSEDIA NO. KONTAK DARURAT',
                    'KELENGKAPAN SERAGAM SECURITY (LENGKAP DAN BERSIH)',
                    'KEBERSIHAN POS SECURITY',
                    'PAPAN INFORMASI (BERSIH DAN TERUPDATE)',
                    'ID CARD MASUK PABRIK',
                    'KOTAK P3K (LENGKAP)',
                    'LANTAI SECURITY (BERSIH)',
                ],
            ],
            // 2. HSE
            [
                'code' => 'AA', 'dept' => 'HSE', 'name' => 'TPS B3',
                'items' => [
                    'RAMBU K3 TERPASANG',
                    'TITIK KOORDINAT',
                    'PENATAAN JENIS LIMBAH RAPI',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'BOTOL TANPA IDENTITAS',
                    'AREA LIMBAH B3 (BERSIH DAN RAPI)',
                ],
            ],
            // 3 - 7. LAB
            [
                'code' => 'AB', 'dept' => 'LAB', 'name' => 'DRYER',
                'items' => [
                    'AREA PANEL DRYER (BERSIH)',
                    'JALUR DRYER (TIDAK ADA YANG BOCOR)',
                    'BUCKET DRYER (BERSIH & TIDAK BOCOR)',
                    'DRYER (TIDAK ADA KEROPOS)',
                    'AREA DRYER (BERSIH)',
                    'APAR (BERSIH DAN BERFUNGSI)',
                    'ALAT KERJA DRYER (RAPI)',
                    'APD KARYAWAN LENGKAP (HELM, SEPATU)',
                ],
            ],
            [
                'code' => 'AC', 'dept' => 'LAB', 'name' => 'WET LABORATORIUM (INC HPLC)',
                'items' => [
                    'PINTU AKSES LAB TERKUNCI AKTIF',
                    'LANTAI BERSIH (TIDAK ADA CECERAN REAGEN, SAMPEL, LIMBAH, DAN AIR)',
                    'TERDAPAT SARANG LABA-LABA',
                    'SEMUA LAMPU RUANGAN BERFUNGSI DAN PENERANGAN CUKUP',
                    'SEMUA KABINET PENYIMPANAN DAN AREA KERJA LAB TERIDENTIFIKASI',
                    'TEMPERATUR RUANGAN TERCONTROL MAX 27 C',
                    'RAK LABORATORIUM DAN INSTRUMEN BERSIH BEBAS DEBU, FORM MAINTENANCE DAILY TERISI LENGKAP',
                    'SAMPEL ANALISA TERIDENTIFIKASI DAN TERTATA RAPI PADA TEMPATNYA',
                    'GLASSWARE DILETAKKAN PADA RAK GLASSWARE',
                    'SELURUH REAGEN TERLABEL DENGAN BAIK DAN TERKONTROL',
                    'REAGEN DAN SPAREPART DISIMPAN SESUAI LOKASI SIMPAN',
                    'MSDS REAGEN TERJANGKAU DI SEKITAR LOKASI PENYIMPANAN REAGEN',
                    'LEMARI REAGEN TERTUTUP RAPAT, TERKUNCI, SUHU TERKONTROL MAX 30 C',
                    'TEMPERATUR LEMARI ES REAGEN TERCONTROL DI BAWAH 8 C',
                    'RAMBU K3 TERPASANG',
                    'APD DIGUNAKAN DENGAN BENAR SESUAI KEBUTUHAN',
                    'SAFETY SHOWER DAN EYEWASH BERSIH, DICEK BERKALA DAN BERFUNGSI',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'SPILL KIT DAN CHEMICAL ABSORBANT TERSEDIA',
                    'LIMBAH REAGEN TERIDENTIFIKASI DISERTAI WADAH SEKUNDER, TIDAK ADA JIRIGEN LIMBAH PENUH BELUM DIPINDAHKAN KE TPS',
                    'SELURUH INSTRUMEN DISERTAI IK (IK PROSEDUR DAN ALAT TERJANGKAU DALAM AREA KERJA)',
                    'INSTRUMEN RUSAK ATAU AREA SEDANG PERBAIKAN TERIDENTIFIKASI',
                    'SELURUH KABEL DAN SELANG INSTRUMEN TERTATA DENGAN RAPI, TIDAK MENGELUPAS/BOCOR',
                    'EXHAUST FAN/BLOWER/FUME HOOD/AC BERFUNGSI DENGAN BAIK',
                    'ALAS KAKI TERSIMPAN DI LEMARI/LOKER',
                    'DOKUMEN TERTATA RAPI PADA LEMARI DOKUMEN',
                    'LINGKUNGAN KERJA BERSIH, TERTATA RAPI, TIDAK ADA SAWANG, DAN KUTU',
                    'JALUR EVAKUASI UPDATE DAN TERIDENTIFIKASI',
                    'TEMPAT SAMPAH TERSEDIA, TERJANGKAU, DAN TERTUTUP',
                    'TIDAK ADA MAKANAN DI AREA KERJA LABORATORIUM',
                    'PENGUKURAN KELISTRIKAN TERDOKUMENTASI',
                ],
            ],
            [
                'code' => 'AD', 'dept' => 'LAB', 'name' => 'DRY LABORATORIUM',
                'items' => [
                    'MEJA KERJA DAN LANTAI BERSIH (TIDAK ADA TUMPAHAN SAMPEL)',
                    'LINGKUNGAN KERJA BERSIH, TERTATA RAPI, TIDAK ADA SAWANG, DAN KUTU',
                    'ALAT SAMPLING BERSIH DAN BERFUNGSI SERTA TERSIMPAN DI AREA TERIDENTIFIKASI',
                    'SIRKULASI UDARA/TEMPERATUR RUANGAN TERCONTROL MAX 25 C',
                    'SEMUA LAMPU RUANGAN BERFUNGSI DAN PENERANGAN CUKUP',
                    'APD DIGUNAKAN DENGAN BENAR SESUAI KEBUTUHAN',
                    'DOKUMEN TERTATA RAPI PADA LEMARI DOKUMEN',
                    'SEMUA SAMPLE TERIDENTIFIKASI/BERLABEL',
                ],
            ],
            [
                'code' => 'AE', 'dept' => 'LAB', 'name' => 'RUANG NIR',
                'items' => [
                    'TERDAPAT SARANG LABA-LABA',
                    'INSTRUMEN BERSIH DARI DEBU, JADWAL MAINTENANCE ALAT TERISI LENGKAP',
                    'KABEL KELISTRIKAN TERPASANG RAPI',
                    'SIRKULASI UDARA/TEMPERATUR RUANGAN TERCONTROL MAX 25 C',
                    'SAMPEL ANALISA TERIDENTIFIKASI',
                    'SEMUA LAMPU RUANGAN BERFUNGSI DAN PENERANGAN CUKUP',
                    'RUANGAN BERSIH DAN BEBAS DEBU',
                    'APD DIGUNAKAN DENGAN BENAR SESUAI KEBUTUHAN',
                ],
            ],
            [
                'code' => 'AF', 'dept' => 'LAB', 'name' => 'RUANG GRINDING',
                'items' => [
                    'SUCTION HOOD/EXHAUST FAN BERFUNGSI DENGAN BAIK MENYEDOT DEBU',
                    'KABEL KELISTRIKAN TERPASANG RAPI',
                    'SISA SAMPLE GRINDING DIBERSIHKAN SETELAH PROSES GRINDING',
                    'SEMUA LAMPU RUANGAN BERFUNGSI DAN PENERANGAN CUKUP',
                    'APD DIGUNAKAN DENGAN BENAR SESUAI KEBUTUHAN',
                    'SPAREPART DISIMPAN SESUAI LOKASI SIMPAN',
                ],
            ],
            // 8 - 9. HRD
            [
                'code' => 'B', 'dept' => 'HRD', 'name' => 'RESEPSIONIST',
                'items' => [
                    'MEJA, KURSI (BERSIH DAN RAPI)',
                    'LOGO MALINDO (BERSIH)',
                    'LANTAI RESEPSIONIS (TIDAK KOTOR DAN BERSIH)',
                    'FREEZER (BERSIH DAN FUNGSI)',
                    'AC (TIDAK BOCOR / MATI)',
                    'TV (BERFUNGSI DAN BERSIH)',
                    'TELEPON EXTENSION (BERFUNGSI)',
                    'KOMPUTER / PC (BERFUNGSI)',
                    'TEMPAT SAMPAH (RAPI)',
                    'KURSI DAN MEJA TUNGGU BERSIH',
                    'FINGER ABSEN (BERFUNGSI)',
                    'DOKUMEN TERSUSUN RAPI',
                ],
            ],
            [
                'code' => 'C', 'dept' => 'HRD', 'name' => 'OFFICE',
                'items' => [
                    'LANTAI (HARUS BERSIH)',
                    'TIDAK ADA SARANG LABA-LABA',
                    'BANGUNAN DAN DINDING (DALAM KONDISI BAGUS)',
                    'RUANG MEETING (RAPI DAN BERSIH)',
                    'MEJA, LEMARI DAN KURSI (HARUS RAPI DAN BERSIH)',
                    'TOILET (HARUS BERSIH)',
                ],
            ],
            // 10 - 11. FG
            [
                'code' => 'D', 'dept' => 'FG', 'name' => 'AREA FINISH GOOD (FG)',
                'items' => [
                    'LANTAI (TIDAK ADA CECERAN PAKAN)',
                    'TIANG (TIDAK ADA SAWANG)',
                    'DINDING (TIDAK ADA SAWANG)',
                    'JARING-JARING BURUNG (TIDAK ADA SAWANG)',
                    'TIDAK TERDAPAT BURUNG',
                    'LAMPU (BERFUNGSI DAN TIDAK KOTOR)',
                    'KOMPUTER / PC (BERFUNGSI)',
                    'FORKLIFT (BERFUNGSI DAN BERSIH)',
                    'TEMPAT BOTOL (KONDISI BERSIH)',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'PAPAN INFORMASI (BERSIH DAN TERUPDATE)',
                    'KOTAK P3K (LENGKAP)',
                    'APD KARYAWAN LENGKAP DAN DIGUNAKAN (HELM, SEPATU DAN FULL BODY HARNESS)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'TEMPAT SAMPAH SESUAI PROSEDUR',
                    'PALET (KONDISI BERSIH)',
                    'PAKAN (TIDAK ADA YANG TUMPAH / BOCOR)',
                    'PENATAAN PAKAN (HARUS RAPI)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'E', 'dept' => 'FG', 'name' => 'AREA WAREHOUSE (BAG)',
                'items' => [
                    'LANTAI (TIDAK ADA CECERAN RAW MATERIAL)',
                    'TIANG (TIDAK ADA SAWANG)',
                    'DINDING (TIDAK ADA SAWANG)',
                    'ATAP (BERSIH, TIDAK ADA KEBOCORAN)',
                    'PALET (TIDAK KOTOR DAN RUSAK)',
                    'BAG RAW MATERIAL (TIDAK BOCOR)',
                    'SETIAP RAW MATERIAL (ADA LABEL)',
                    'BAG RAW MATERIAL (PENATAAN RAPIH)',
                    'TIDAK TERDAPAT BURUNG',
                    'JARING-JARING BURUNG (TIDAK ADA SAWANG)',
                    'TIDAK ADA KUTU',
                    'APD KARYAWAN LENGKAP DAN DIGUNAKAN (HELM, MASKER DAN SEPATU)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'FORKLIFT (KONDISI BERSIH)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            // 12. PREMIX
            [
                'code' => 'F', 'dept' => 'PREMIX', 'name' => 'GUDANG MEDICINE (LUAR)',
                'items' => [
                    'PALET (TIDAK KOTOR DAN RUSAK)',
                    'BAG MEDICINE (TIDAK BOCOR)',
                    'PENATAAN MEDICINE RAPI',
                    'MEDICINE TIDAK TERCAMPUR MEDICINE LAIN',
                    'MEMILIKI LABEL SETIAP MEDICINE',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            // 13 - 14. RAWMATE
            [
                'code' => 'G', 'dept' => 'RAWMATE', 'name' => 'GUDANG PVC',
                'items' => [
                    'PAGAR (BERSIH DAN TIDAK BERSAWANG)',
                    'LANTAI (BERSIH)',
                    'DINDING (TIDAK BERSAWANG)',
                    'KARUNG / PVC (RAPI DAN TIDAK BERSAWANG)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'BENANG (RAPIH)',
                    'HANGTAG (RAPI DAN TIDAK BERSAWANG)',
                    'PALET (BERSIH DAN TIDAK KOTOR)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'H', 'dept' => 'RAWMATE', 'name' => 'GUDANG W',
                'items' => [
                    'LANTAI (TIDAK ADA CECERAN RM)',
                    'TIANG (TIDAK ADA SAWANG)',
                    'DINDING (TIDAK ADA SAWANG)',
                    'ATAP (TIDAK ADA SAWANG)',
                    'TIDAK ADA KUTU',
                    'CHAIN CONVEYOR (BERSIH)',
                    'PINTU GERBANG GUDANG W (TERTUTUP)',
                    'KANOPI GUDANG W (BERSIH)',
                    'RAW MATERIAL (TIDAK TERCAMPUR)',
                    'MEMILIKI LABEL RAW MATERIAL / STACK CARD',
                ],
            ],
            // 15 - 16. PREMIX
            [
                'code' => 'I', 'dept' => 'PREMIX', 'name' => 'AREA PREMIX',
                'items' => [
                    'LANTAI (BERSIH DAN TIDAK CECERAN MEDICINE)',
                    'PAGAR (BERSIH DAN TIDAK BERSAWANG)',
                    'DINDING (BERSIH)',
                    'BAG MEDICINE (RAPI, TERTUTUP RAPAT DAN TIDAK BOCOR)',
                    'PREMIX (RAPI DAN TIDAK BOCOR)',
                    'PALET (BERSIH TIDAK BERSAWANG)',
                    'TIMBANG (BERSIH DAN BERFUNGSI)',
                    'SEKOP/GAYUNG (MEMILIKI LABEL)',
                    'TIMBANGAN HAND ADD (BERSIH)',
                    'MATERIAL HAND ADD (RAPI)',
                    'KIPAS ANGIN (BERSIH DAN BERFUNGSI)',
                    'PANEL MIXER (BERSIH DAN TIDAK BERSAWANG)',
                    'MESIN MIXER (BERSIH DAN TIDAK BOCOR)',
                    'HOPPER MIXER (BERSIH DAN TIDAK BOCOR)',
                    'APD KARYAWAN LENGKAP DAN DIGUNAKAN (HELM, MASKER RESPIRATED DAN SEPATU)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'INTAKE PREMIX (BERSIH TIDAK ADA BENDA ASING)',
                    'MIXER KECIL (BERSIH DAN BERFUNGSI)',
                    'EMBER PREMIX (BERSIH DAN TIDAK TERCAMPUR)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'J', 'dept' => 'PREMIX', 'name' => 'COOLROOM PREMIX',
                'items' => [
                    'LANTAI, PINTU DAN JENDELA (BERSIH)',
                    'RUANGAN COOLROOM (TIDAK BERSAWANG)',
                    'PANEL LISTRIK (BERSIH DAN TIDAK BERSAWANG)',
                    'LAMPU (BERFUNGSI)',
                    'ALAT PENGUKUR SUHU (SUDAH DIKALIBRASI DAN BERFUNGSI)',
                    'PENATAAN KABEL (RAPI)',
                    'BAG MEDICINE (DITUTUP RAPAT DAN TIDAK TERCAMPUR)',
                    'BAG MEDICINE (TIDAK ADA YANG BOCOR)',
                    'AC (BERFUNGSI DAN TIDAK BOCOR)',
                    'MEMILIKI MSDS UNTUK SETIAP MEDICINE',
                    'PALET KAYU / PLASTIK(BERSIH)',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            // 17 - 19. MAINTENANCE
            [
                'code' => 'K', 'dept' => 'MTC', 'name' => 'AREA POMPA DAN TANKI OLEIN',
                'items' => [
                    'MESIN POMPA (BERSIH DAN BERFUNGSI)',
                    'MATERIAL LIQUID (TIDAK TUMPAH / BOCOR)',
                    'DINDING (BERSIH)',
                    'JALUR POMPA (BERSIH DAN TIDAK BOCOR)',
                    'LANTAI (BERSIH DAN TIDAK ADA TUMPAHAN LIQUID)',
                    'JARING-JARING (TIDAK BERSAWANG)',
                    'TANGKI OLEIN (BERSIH)',
                    'JALUR OLEIN (TERGEMBOK)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL)',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'L', 'dept' => 'MTC', 'name' => 'BOILER',
                'items' => [
                    'LANTAI (BERSIH)',
                    'TIANG (TIDAK ADA SAWANG)',
                    'PANEL BOILER (BERSIH DAN TIDAK BERSAWANG)',
                    'TANGKI AIR BOILER (BERSIH DAN TIDAK BOCOR)',
                    'APD KARYAWAN LENGKAP (HELM, MASKER DAN SEPATU)',
                    'PENATAAN ALAT KERJA (RAPI)',
                    'PENYIMPANAN CANGKANG SESUAI BATAS (TIDAK TERCAMPUR)',
                    'BOTOL TANPA IDENTIAS',
                    'KOTAK P3K (LENGKAP)',
                    'JALUR BOILER (TIDAK ADA YANG BOCOR)',
                    'BUCKET BOILER (BERSIH)',
                    'LIMBAH HASIL PEMBAKARAN (RAPI)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'M', 'dept' => 'MTC', 'name' => 'WORKSHOP DAN SPAREPART',
                'items' => [
                    'PENATAAN ALAT KERJA (RAPI)',
                    'TIDAK ADA SAWANG',
                    'DINDING, ATAP, LANTAI (BERSIH)',
                    'TIDAK ADA KUTU',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'APD KARYAWAN LENGKAP (HELM, SEPATU)',
                    'LAMPU (BERFUNGSI)',
                    'BARANG SPAREPART MEMILIKI IDENTITAS',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            // 20 - 21. PRODUKSI (SILO)
            [
                'code' => 'N', 'dept' => 'PRDSILO', 'name' => 'SILO',
                'items' => [
                    'LANTAI, DINDING, ATAP AREA INTAKE SILO (BERSIH)',
                    'TIDAK ADA SARANG LABA',
                    'TEMPAT SAMPAH (ADA LABEL DAN RAPI)',
                    'TIDAK ADA KUTU',
                    'TIDAK TERDAPAT BURUNG',
                    'PANEL SILO (BERSIH)',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'KABEL TERTATA DENGAN RAPI',
                    'APD KARYAWAN LENGKAP (MASKER, HELM)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'PENATAAN ALAT KERJA (RAPI)',
                    'CONVEYOR SILO (BERSIH DAN TIDAK BOCOR)',
                    'BUCKET ELEVATOR SILO (BERSIH TIDAK BOCOR)',
                    'BLOWER (BERFUNGSI)',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'KOTAK P3K (LENGKAP)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'O', 'dept' => 'PRDSILO', 'name' => 'BASEMENT SILO',
                'items' => [
                    'PENERANGAN CUKUP',
                    'DINDING (BERSIH)',
                    'TIDAK ADA GENANGAN AIR DI BASEMENT SILO',
                    'AREA BASEMENT SILO BERSIH',
                    'TIDAK ADA KEBOCORAN JALUR SILO',
                    'MESIN (KONDISI BERSIH DAN BERFUNGSI)',
                    'TIDAK ADA KUTU (DI AREA BASEMENT)',
                ],
            ],
            // 22. MAINTENANCE
            [
                'code' => 'P', 'dept' => 'MTC', 'name' => 'KOMPRESSOR DAN GENSET',
                'items' => [
                    'PENERANGAN CUKUP',
                    'DINDING (BERSIH)',
                    'KABEL TERTATA DENGAN RAPI',
                    'AREA BERSIH (TIDAK BERSAWANG)',
                    'TIDAK ADA BOCOR PADA JALUR KOMPRESSOR',
                    'PANEL KOMPRESSOR (BERSIH DAN BERFUNGSI)',
                    'MESIN GENSET (BERSIH DAN BERFUNGSI)',
                    'ALAT KERJA TERTATA RAPI',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            // 23 - 32. PRODUKSI
            [
                'code' => 'Q', 'dept' => 'PRD', 'name' => 'BASEMENT PRODUKSI',
                'items' => [
                    'PENERANGAN CUKUP',
                    'TIDAK ADA GENANGAN AIR DI BASEMENT PRODUKSI',
                    'AREA BASEMENT PRODUKSI BERSIH',
                    'TIDAK ADA KEBOCORAN JALUR PRODUKSI',
                    'MESIN (KONDISI BERSIH DAN BERFUNGSI)',
                    'TIDAK ADA KUTU (DI AREA BASEMENT)',
                ],
            ],
            [
                'code' => 'R', 'dept' => 'PRD', 'name' => 'LANTAI 1 TOWER MILL',
                'items' => [
                    'BUCKET ELEVATOR (BERSIH DAN TIDAK BOCOR)',
                    'HOOPER MIXER (BERSIH DAN TIDAK BOCOR)',
                    'CHAIN CONVEYOR MIXER (BERSIH DAN TIDAK BOCOR)',
                    'LANTAI HAND ADD (BERSIH)',
                    'AREA PANEL LVMDP',
                    'RC PAKAN (RAPI DAN BERLABEL)',
                    'RC PAKAN (TIDAK BOCOR)',
                    'MESIN INTAKE (BERSIH)',
                    'PANEL INTAKE (BERSIH DAN BERFUNGSI)',
                    'LUBANG INTAKE (BERSIH DAN TIDAK ADA BENDA ASING)',
                    'WHOLEBAG (BERSIH DAN BERFUNGSI)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'LIFT (MEMILIKI RAMBU BAHAYA)',
                    'SAMPAH (RAPI DAN MEMILIKI LABEL)',
                    'PENATAAN ALAT KERJA (RAPI)',
                    'TANGGA LANTAI 1 (BERSIH)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'S', 'dept' => 'PRD', 'name' => 'RUANGAN ADMIN PRODUKSI',
                'items' => [
                    'PENATAAN ALAT KERJA (RAPI)',
                    'DOKUMEN (TERTATA RAPI)',
                    'AC (BERFUNGSI DAN TIDAK BOCOR)',
                    'KOTAK P3K (LENGKAP)',
                    'KABEL TERTATA (RAPI)',
                    'TEMPAT SAMPAH (RAPI)',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                ],
            ],
            [
                'code' => 'T', 'dept' => 'PRD', 'name' => 'BAGGING',
                'items' => [
                    'AREA BAGGING (BERSIH)',
                    'AREA ROBOT (BERSIH DAN BERFUNGSI)',
                    'JALUR CONVEYOR BAGGING (BERSIH DAN BERFUNGSI)',
                    'PANEL BAGGING DAN ROBOT (BERSIH)',
                    'MESIN JAHIT (BERSIH DAN BERFUNGSI)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'TIDAK ADA CECERAN PAKAN DAN KARUNG (HARUS RAPI)',
                    'DOKUMEN (TERTATA RAPI)',
                    'AIRLOCK BAGGING (BERSIH DAN BERFUNGSI)',
                    'APD KARYAWAN LENGKAP (HELM, MASKER DAN SEPATU)',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'U', 'dept' => 'PRD', 'name' => 'CONTROL ROOM',
                'items' => [
                    'PENATAAN ALAT KERJA (RAPI)',
                    'LANTAI, DINDING, ATAP (BERSIH DAN TIDAK BERSAWANG)',
                    'SERVER (BERSIH)',
                    'KABEL PANEL (RAPI)',
                    'AC (BERFUNGSI DAN TIDAK BOCOR)',
                    'DOKUMEN TERSUSUN RAPI',
                    'AREA PANEL CONTROL ROOM (BERSIH)',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'APD KARYAWAN LENGKAP',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'V', 'dept' => 'PRD', 'name' => 'LANTAI 2 (CRUMBLE, MIXER, COOLER, HAMMERMILL)',
                'items' => [
                    'LANTAI 2 (BERSIH DAN TIDAK BERSAWANG)',
                    'MESIN COOLER (BERSIH DAN TIDAK ADA TUMPAHAN PAKAN)',
                    'MESIN MIXER (BERSIH DAN BERFUNGSI)',
                    'MESIN CRUMBLE (BERSIH DAN TIDAK ADA TUMPAHAN PAKAN)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'JALUR POMPA LIQUID (TIDAK BOCOR)',
                    'PENATAAN ALAT KERJA (RAPI)',
                    'LAMPU (BERSIH DAN BERFUNGSI)',
                    'AREA PANEL (BERSIH)',
                    'JALUR COOLER (TIDAK BOCOR DAN BERSIH)',
                    'AREA HAMMER MILL (BERSIH DAN BERFUNGSI)',
                    'APD KARYAWAN LENGKAP (HELM, SEPATU)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'W', 'dept' => 'PRD', 'name' => 'LANTAI 3 (PELLET, HAND ADD, MACRO)',
                'items' => [
                    'MESIN PELLET (BERSIH DAN TIDAK ADA KEBOCORAN)',
                    'JALUR STEAM (BERSIH DAN TIDAK BOCOR)',
                    'TANGKI LIQUID (BERSIH DAN TIDAK BOCOR)',
                    'TIDAK ADA SAWANG DI AREA LANTAI 3',
                    'EMBER MEMILIKI IDENTITAS',
                    'BAG PREMIX TERTATA RAPI',
                    'AREA LANTAI 3 (TIDAK ADA CECERAN PAKAN DAN BERSIH)',
                    'APD KARYAWAN LENGKAP (HELM, MASKER DAN SEPATU)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'LIFT (BERSIH DAN TERTUTUP)',
                    'PANEL (MESIN PELLET DAN MESIN HAND ADD BERSIH)',
                    'SCALE (BERSIH DAN TIDAK ADA BOCOR)',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'DOKUMEN TERTATA RAPI',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'X', 'dept' => 'PRD', 'name' => 'LANTAI 4 (MICRO)',
                'items' => [
                    'AREA LANTAI MACRO-MICRO (BERSIH DAN TIDAK BERSAWANG)',
                    'PENATAAN ALAT KERJA (RAPI)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'RAW MATERIAL (TERSUSUN RAPI)',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'APD KARYAWAN LENGKAP (HELM, MASKER DAN SEPATU)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'KABEL (RAPI)',
                    'LAMPU (BERSIH DAN BERFUNGSI)',
                    'LIFT (BERSIH DAN TERTUTUP)',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'Y', 'dept' => 'PRD', 'name' => 'LANTAI 5 (BIN)',
                'items' => [
                    'AREA TOP FLOOR (BERSIH DAN TIDAK BERSAWANG)',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'DISTRIBUTOR (BERSIH DAN TIDAK BOCOR)',
                    'JALUR DISTRIBUTOR (BERSIH DAN TIDAK BOCOR)',
                    'PENUTUP BIN (TERTUTUP RAPAT)',
                    'SPOUTING JALUR TIDAK ADA YANG BOCOR',
                    'APD KARYAWAN LENGKAP (HELM, SEPATU)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'LIFT (BERSIH DAN TERTUTUP)',
                    'PENATAAN ALAT KERJA (RAPI)',
                    'TIDAK ADA BOTOL TANPA IDENTITAS',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
            [
                'code' => 'Z', 'dept' => 'PRD', 'name' => 'LANTAI 6 (BLOWER, CYCLONE, SHIFTER)',
                'items' => [
                    'AREA LANTAI 6 (BERSIH DAN TIDAK BERSAWANG)',
                    'SHIFTER (BERSIH DAN TIDAK BOCOR)',
                    'BLOWER (BERSIH)',
                    'CYCLONE (BERSIH DAN TIDAK BOCOR)',
                    'DRUMSIEVETER (BERSIH)',
                    'LIFT (BERSIH DAN TERTUTUP)',
                    'JALUR (TIDAK ADA YANG BOCOR)',
                    'LAMPU (BERFUNGSI)',
                    'APD KARYAWAN LENGKAP (HELM, SEPATU)',
                    'SERAGAM KERJA DIGUNAKAN DAN SESUAI',
                    'ALAT PROTEKSI KEBAKARAN (APAR, HOSEREEL, HYDRANT) KONDISI BERSIH DAN NORMAL',
                    'KONDISI PANEL LISTRIK BERSIH (LUAR DAN DALAM)',
                    'RAMBU K3 TERPASANG',
                ],
            ],
        ];

        foreach ($locationsData as $locData) {
            $defaultDeptId = $deptMap[$locData['dept']] ?? null;

            $location = Location::updateOrCreate(
                ['code' => $locData['code']],
                [
                    'department_id' => $defaultDeptId,
                    'name' => $locData['name'],
                    'pic_user_id' => $defaultDeptId ? ($picUserMap[$defaultDeptId] ?? null) : null,
                    'is_active' => true,
                ]
            );

            foreach ($locData['items'] as $index => $question) {
                $itemDeptCode = $this->resolveResponsibleDept($question, $locData['dept']);

                ChecklistItem::updateOrCreate(
                    [
                        'location_id' => $location->id,
                        'sort_order' => $index + 1,
                    ],
                    [
                        'department_id' => $deptMap[$itemDeptCode] ?? $defaultDeptId,
                        'question' => $question,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function resolveResponsibleDept(string $question, string $defaultDept): string
    {
        $q = strtoupper($question);

        if (str_contains($q, 'APAR') || str_contains($q, 'HOSEREEL') || str_contains($q, 'HYDRANT')
            || str_contains($q, 'RAMBU K3') || str_contains($q, 'BENDERA K3') || str_contains($q, 'KOTAK P3K')
            || str_contains($q, 'SAFETY SHOWER') || str_contains($q, 'SPILL KIT') || str_contains($q, 'JALUR EVAKUASI')
            || str_contains($q, 'MSDS') || str_contains($q, 'LIMBAH B3')) {
            return 'HSE';
        }

        if (str_contains($q, 'PANEL LISTRIK') || str_contains($q, 'KELISTRIKAN') || str_contains($q, 'LAMPU')
            || str_contains($q, 'AC (') || str_contains($q, 'GENSET') || str_contains($q, 'KOMPRESSOR')
            || str_contains($q, 'POMPA') || str_contains($q, 'EXHAUST FAN') || str_contains($q, 'KIPAS ANGIN')) {
            return 'MTC';
        }

        return $defaultDept;
    }
}