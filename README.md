# 🎓 Sistem Informasi E-Surat Akademik (SILA)

Sistem Informasi E-Surat Akademik adalah aplikasi manajemen administrasi persuratan mahasiswa berbasis web. Sistem ini mendigitalkan proses pengajuan surat manual menjadi alur kerja otomatis (Automated Workflow) yang dilengkapi dengan pembuatan PDF dinamis dan verifikasi keamanan menggunakan QR Code & UUID.

## ✨ Fitur Unggulan
- **Dynamic Form System:** Formulir pengajuan surat yang beradaptasi secara otomatis (menggunakan Alpine.js & JSON columns) berdasarkan jenis surat yang dipilih.
- **Automated Document Generation:** Menghasilkan dokumen PDF secara otomatis saat pengajuan disetujui oleh Admin.
- **Secure Verification:** Dilengkapi validasi keaslian dokumen menggunakan QR Code dan UUID (Anti-Pemalsuan Dokumen).
- **Role-Based Access Control:** Portal terpisah untuk Mahasiswa (Frontend) dan Admin Prodi (Filament Admin Panel).
- **Status Tracking:** Mahasiswa dapat melacak status surat beserta catatan penolakan secara *real-time*.

## 🛠️ Tech Stack
- **Framework:** Laravel 11
- **Admin Panel:** Filament PHP v3
- **Frontend:** Tailwind CSS, Alpine.js, Laravel Blade
- **PDF Generator:** Barryvdh / DomPDF

## 📸 Screenshots
*(Nanti Anda bisa drag & drop foto screenshot web Anda ke bagian ini langsung di GitHub)*

## 🚀 Cara Instalasi (Local Development)
1. Clone repository ini:
   `git clone https://github.com/geardosimamora/nama-repo-anda.git`
2. Install dependencies:
   `composer install` & `npm install`
3. Copy `.env.example` ke `.env` dan atur database Anda.
4. Generate App Key:
   `php artisan key:generate`
5. Jalankan Migrasi & Seeder:
   `php artisan migrate:fresh --seed`
6. Jalankan Server lokal:
   `php artisan serve` & `npm run dev`

---
*Developed by [Geardo Lapista Simamora](https://github.com/geardosimamora)*