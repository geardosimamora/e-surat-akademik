<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\LetterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LetterController extends Controller
{
    /**
     * Menampilkan Dashboard (Daftar Surat Saya)
     */
    public function index()
    {
        $letters = Letter::with('letterType')
                        ->where('user_id', Auth::id())
                        ->latest()
                        ->get();
                        
        return view('student.dashboard', compact('letters'));
    }

    /**
     * Menampilkan Form Pengajuan Surat
     */
    public function create()
    {
        $letterTypes = LetterType::all();
        return view('student.letters.create', compact('letterTypes'));
    }

    /**
     * Memproses Pengajuan Surat (Snapshot Pattern & UUID)
     */
    public function store(Request $request)
    {
        // Validasi dasar
        $rules = [
            'letter_type_id' => ['required', 'exists:letter_types,id'],
        ];

        // Validasi bersyarat untuk Kerja Praktek (ID = 2)
        // CATATAN: Sesuaikan ID '2' dengan ID Kerja Praktek di database Anda
        if ($request->letter_type_id == 2) {
            $rules['nama_instansi']  = ['required', 'string', 'max:100'];
            $rules['alamat_instansi'] = ['required', 'string', 'max:500'];
            $rules['tanggal_mulai']  = ['required', 'date', 'after_or_equal:today'];
        }

        $validated = $request->validate($rules, [
            // nama_instansi
            'nama_instansi.required' => 'Nama instansi wajib diisi untuk Surat Kerja Praktek.',
            'nama_instansi.string'   => 'Nama instansi harus berupa teks.',
            'nama_instansi.max'      => 'Nama instansi maksimal 100 karakter.',
            // alamat_instansi
            'alamat_instansi.required' => 'Alamat instansi wajib diisi untuk Surat Kerja Praktek.',
            'alamat_instansi.string'   => 'Alamat instansi harus berupa teks.',
            'alamat_instansi.max'      => 'Alamat instansi maksimal 500 karakter.',
            // tanggal_mulai
            'tanggal_mulai.required'      => 'Tanggal mulai KP wajib diisi untuk Surat Kerja Praktek.',
            'tanggal_mulai.date'          => 'Tanggal mulai KP harus berupa tanggal yang valid.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai KP tidak boleh kurang dari hari ini.',
        ]);

        $user = Auth::user();

        // 1. SNAPSHOT: "Membekukan" data profil saat pengajuan dibuat
        $snapshot = [
            'name'  => $user->name,
            'nim'   => $user->nim,
            'phone' => $user->phone,
        ];

        // 2. DATA DINAMIS: Mengambil semua input form selain field utama
        $dynamicData = $request->except(['_token', 'letter_type_id']);

        // 3. SIMPAN KE DATABASE
        Letter::create([
            'id'              => Str::uuid(), // KRUSIAL: Untuk keamanan link QR Code
            'user_id'         => $user->id,
            'letter_type_id'  => $request->letter_type_id,
            'status'          => 'pending',
            'user_snapshot'   => $snapshot, 
            'additional_data' => empty($dynamicData) ? null : $dynamicData,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Surat berhasil diajukan!');
    }

    /**
     * Membatalkan Pengajuan Surat (Mencegah IDOR)
     */
    public function cancel(Letter $letter)
    { 
        // Keamanan 1: Pastikan milik sendiri
        if ($letter->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        // Keamanan 2: Hanya bisa batal jika status masih pending
        if ($letter->status !== 'pending') {
            return back()->with('error', 'Surat sedang diproses dan tidak bisa dibatalkan.');
        }

        $letter->delete();

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    /**
     * Mengunduh PDF yang sudah disetujui
     */
    public function download(Letter $letter)
    {
        // 1. FIX 403 FORBIDDEN: Izinkan pemilik surat ATAU user dengan role 'admin'
        if ($letter->user_id !== \Illuminate\Support\Facades\Auth::id() && \Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak. Halaman ini khusus untuk mahasiswa pemilik surat atau Admin.');
        }

        if ($letter->status !== 'approved') {
            return back()->with('error', 'Surat belum disetujui oleh admin.');
        }

        // 2. FIX FILE NOT FOUND (MANUAL UPLOAD): Filament biasanya simpan di disk 'public'
        if (!empty($letter->manual_file_path)) {
            // Cek di disk public dulu (default Filament)
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($letter->manual_file_path)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->download($letter->manual_file_path);
            }
            // Fallback jika diset ke local di Filament
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($letter->manual_file_path)) {
                return \Illuminate\Support\Facades\Storage::disk('local')->download($letter->manual_file_path);
            }
        }

        // 3. FIX FILE NOT FOUND (AUTO-GENERATE): Cek di disk 'local'
        if (!empty($letter->file_path)) {
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($letter->file_path)) {
                return \Illuminate\Support\Facades\Storage::disk('local')->download($letter->file_path);
            }
        }

        // 4. Jika sampai di sini, record ada di DB tapi file fisik gaib. Tambahkan log untuk debug.
        \Illuminate\Support\Facades\Log::error("Download failed. Physical file missing for Letter ID: {$letter->id}. Path in DB: " . ($letter->file_path ?? 'NULL'));
        
        return back()->with('error', 'Surat tidak ditemukan secara fisik di server. Silakan hubungi admin untuk memverifikasi status file.');
    }

    public function verify($uuid)
    {
$letter = Letter::with('letterType')->findOrFail($uuid);
        // Tampilkan halaman khusus verifikasi

        return view('student.letters.verify', compact('letter'));
    }
}
