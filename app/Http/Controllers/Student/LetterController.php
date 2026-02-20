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
        $letters = Letter::where('user_id', Auth::id())
                        ->with('letterType') 
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
            $rules['nama_instansi'] = ['required', 'string', 'max:255'];
            $rules['alamat_instansi'] = ['required', 'string', 'max:500'];
            $rules['tanggal_mulai'] = ['required', 'date', 'after_or_equal:today'];
        }

        $validated = $request->validate($rules, [
            'nama_instansi.required' => 'Nama instansi wajib diisi untuk Surat Kerja Praktek',
            'alamat_instansi.required' => 'Alamat instansi wajib diisi untuk Surat Kerja Praktek',
            'tanggal_mulai.required' => 'Tanggal mulai KP wajib diisi untuk Surat Kerja Praktek',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai KP tidak boleh kurang dari hari ini',
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
        if ($letter->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        if ($letter->status !== 'approved' || empty($letter->file_path)) {
            return back()->with('error', 'File surat belum tersedia atau belum disetujui.');
        }

        // Cek fisik file sebelum download
        if (!Storage::exists($letter->file_path)) {
            return back()->with('error', 'Maaf, file fisik tidak ditemukan di server.');
        }

        return Storage::download($letter->file_path);
    }
}
