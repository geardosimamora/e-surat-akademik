# 📋 Panduan Setup Surat Kerja Praktek

## ✅ Masalah yang Sudah Diperbaiki

### 1. Tombol Submit Tidak Bisa Ditekan
**Penyebab:** Field `required` pada form dinamis Kerja Praktek mencegah submit ketika form tersebut hidden (x-show="false").

**Solusi:** Menggunakan Alpine.js binding `:required="jenisSurat == '2'"` agar validasi required hanya aktif ketika form Kerja Praktek ditampilkan.

### 2. Data Letter Type Kerja Praktek Belum Ada
**Solusi:** Membuat seeder untuk menambahkan data jenis surat otomatis.

---

## 🚀 Cara Setup Database

### Opsi 1: Menggunakan Seeder (Recommended)

Jalankan perintah berikut di terminal:

```bash
# Jika database masih kosong (fresh install)
php artisan migrate:fresh --seed

# Jika database sudah ada data dan hanya ingin menambah Letter Types
php artisan db:seed --class=LetterTypeSeeder
```

Seeder akan otomatis membuat 2 jenis surat:
- **ID 1**: SKA - Surat Keterangan Aktif Kuliah
- **ID 2**: SKP - Surat Permohonan Kerja Praktek ✅

### Opsi 2: Input Manual via Filament Admin Panel

1. Login ke admin panel: `http://localhost:8000/admin`
2. Buka menu **Letter Types**
3. Klik **New Letter Type**
4. Isi data berikut:

**Untuk Surat Kerja Praktek:**
```
Code: SKP
Name: Surat Permohonan Kerja Praktek
Template View: letters.kerja_praktek
Requires Approval: Yes (checked)
```

5. Klik **Create**
6. **PENTING:** Catat ID yang dibuat (biasanya ID 2)

### Opsi 3: Insert Manual via Database

Jalankan query SQL berikut:

```sql
INSERT INTO letter_types (code, name, template_view, requires_approval, created_at, updated_at) 
VALUES 
('SKA', 'Surat Keterangan Aktif Kuliah', 'letters.active_student', 1, NOW(), NOW()),
('SKP', 'Surat Permohonan Kerja Praktek', 'letters.kerja_praktek', 1, NOW(), NOW());
```

---

## 🔧 Menyesuaikan ID Kerja Praktek

Jika ID Kerja Praktek di database Anda **BUKAN 2**, sesuaikan di 2 file berikut:

### 1. File: `resources/views/student/letters/create.blade.php`

Cari baris ini (sekitar baris 107):
```html
<div x-show="jenisSurat == '2'"
```

Ubah `'2'` menjadi ID yang sesuai, misalnya:
```html
<div x-show="jenisSurat == '3'"
```

### 2. File: `app/Http/Controllers/Student/LetterController.php`

Cari baris ini (sekitar baris 42):
```php
if ($request->letter_type_id == 2) {
```

Ubah `2` menjadi ID yang sesuai, misalnya:
```php
if ($request->letter_type_id == 3) {
```

---

## 📝 Cara Menggunakan Form Kerja Praktek

### Langkah-langkah untuk Mahasiswa:

1. **Login** sebagai mahasiswa
2. Buka **Dashboard** → Klik **Ajukan Surat Baru**
3. Pada dropdown **Pilih Jenis Surat**, pilih **"SKP - Surat Permohonan Kerja Praktek"**
4. **Form khusus akan muncul otomatis** dengan animasi smooth! 🎉
5. Isi 3 field yang muncul:
   - **Nama Instansi Tujuan**: Contoh: PT. Teknologi Indonesia Tbk
   - **Alamat Instansi**: Contoh: Jl. Sudirman No. 123, Jakarta Pusat
   - **Tanggal Mulai KP**: Pilih tanggal dari kalender
6. Klik **Kirim Pengajuan**

### Data yang Tersimpan:

Data akan tersimpan di tabel `letters` dengan struktur:

```json
{
  "user_snapshot": {
    "name": "Geardo Lapista Simamora",
    "nim": "230180121",
    "phone": "081234567890"
  },
  "additional_data": {
    "nama_instansi": "PT. Teknologi Indonesia Tbk",
    "alamat_instansi": "Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10110",
    "tanggal_mulai": "2026-03-15"
  }
}
```

---

## 🎨 Fitur yang Sudah Diimplementasikan

✅ **Single Dynamic Form** - Form berubah otomatis sesuai jenis surat yang dipilih
✅ **Alpine.js Integration** - Animasi smooth dan reaktif tanpa reload halaman
✅ **Conditional Validation** - Validasi otomatis hanya untuk field yang ditampilkan
✅ **Tailwind CSS Design** - UI modern dan responsive
✅ **JSON Storage** - Data tersimpan rapi di kolom `additional_data`
✅ **User Snapshot** - Data mahasiswa "dibekukan" saat pengajuan dibuat

---

## 🧪 Testing

### Test Form Dinamis:
1. Pilih jenis surat selain Kerja Praktek → Form khusus tidak muncul ✅
2. Pilih Kerja Praktek → Form khusus muncul dengan animasi ✅
3. Ganti pilihan → Form khusus hilang dengan animasi ✅
4. Submit tanpa isi form KP → Validasi error muncul ✅
5. Submit dengan form KP lengkap → Data tersimpan ✅

### Test Validasi Backend:
```bash
# Coba submit tanpa nama_instansi (harus error)
# Coba submit dengan tanggal_mulai di masa lalu (harus error)
# Coba submit lengkap (harus sukses)
```

---

## 🐛 Troubleshooting

### Tombol Submit Masih Tidak Bisa Ditekan?
- Pastikan Alpine.js sudah loaded (cek console browser)
- Pastikan tidak ada error JavaScript di console
- Clear cache browser (Ctrl + Shift + R)

### Form Dinamis Tidak Muncul?
- Pastikan ID di `x-show="jenisSurat == '2'"` sesuai dengan ID di database
- Cek console browser untuk error Alpine.js
- Pastikan Alpine.js CDN sudah ditambahkan di layout

### Data Tidak Tersimpan?
- Cek validasi di `LetterController.php`
- Pastikan kolom `additional_data` di tabel `letters` bertipe JSON
- Cek log Laravel: `storage/logs/laravel.log`

---

## 📞 Kontak

Jika ada masalah, hubungi developer atau cek dokumentasi Laravel & Alpine.js.

**Happy Coding! 🚀**
