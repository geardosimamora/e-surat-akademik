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
-Mahasiswa/User
1.Login Page Mahasiswa
<img width="1919" height="891" alt="image" src="https://github.com/user-attachments/assets/75a4886e-b177-4b4c-a881-a35908793406" />

2.Register Page Mahasiswa
<img width="1919" height="887" alt="image" src="https://github.com/user-attachments/assets/77468766-e200-4812-a208-f24001e8b538" />

3.Dashboard Mahasiswa
<img width="1919" height="889" alt="image" src="https://github.com/user-attachments/assets/5555d175-9123-457b-b077-9531ed54dc50" />

4.Form Pengajuan surat Mahasiswa
<img width="1896" height="896" alt="image" src="https://github.com/user-attachments/assets/6bf4bb16-eb15-417e-af7e-3eb30dc1df36" />

5.Edit Profile Mahasiswa
<img width="1919" height="891" alt="image" src="https://github.com/user-attachments/assets/e2dfb1c4-679e-4863-ba23-71e0c864ea2b" />



-Admin-
1.Admin Login Page
<img width="1919" height="887" alt="image" src="https://github.com/user-attachments/assets/09d0102e-54ef-4cd0-bca1-f1bdb519556f" />

2.Dashboard Admin
<img width="1896" height="890" alt="image" src="https://github.com/user-attachments/assets/22aa5d00-7615-49a4-bdad-41e82d558d4b" />

3.Pengajuan Surat Admin
<img width="1919" height="890" alt="image" src="https://github.com/user-attachments/assets/7526239e-788e-48db-ac2c-c57c0050c8c5" />

4.Jenis Surat
<img width="1919" height="885" alt="image" src="https://github.com/user-attachments/assets/2bde0c08-c0fd-439a-9cc7-0545dc5fec4c" />

5.Users/Pengguna
<img width="1919" height="886" alt="image" src="https://github.com/user-attachments/assets/7e1b087e-83e3-4213-a2a0-1802cd598921" />










## 🚀 Cara Instalasi (Local Development)
1. Clone repository ini:
   `git clone https://github.com/geardosimamora/e-surat-akademik`
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
