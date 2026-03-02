<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\LetterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
        // FIXED: Validasi yang lebih ketat
        $rules = [
            'letter_type_id' => ['required', 'exists:letter_types,id'],
        ];

        // Validasi bersyarat untuk Kerja Praktek (ID = 2)
        if ($request->letter_type_id == 2) {
            $rules['nama_instansi']  = ['required', 'string', 'max:100'];
            $rules['alamat_instansi'] = ['required', 'string', 'max:500'];
            $rules['tanggal_mulai']  = ['required', 'date', 'after_or_equal:today'];
        }

        $validated = $request->validate($rules, [
            'nama_instansi.required' => 'Nama instansi wajib diisi untuk Surat Kerja Praktek.',
            'nama_instansi.max'      => 'Nama instansi maksimal 100 karakter.',
            'alamat_instansi.required' => 'Alamat instansi wajib diisi untuk Surat Kerja Praktek.',
            'alamat_instansi.max'      => 'Alamat instansi maksimal 500 karakter.',
            'tanggal_mulai.required'      => 'Tanggal mulai KP wajib diisi.',
            'tanggal_mulai.date'          => 'Tanggal mulai KP harus berupa tanggal yang valid.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai KP tidak boleh kurang dari hari ini.',
        ]);

        $user = Auth::user();

        // FIXED: Snapshot dengan sanitasi
        $snapshot = [
            'name'  => htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'),
            'nim'   => htmlspecialchars($user->nim, ENT_QUOTES, 'UTF-8'),
            'phone' => htmlspecialchars($user->phone ?? '', ENT_QUOTES, 'UTF-8'),
        ];

        // FIXED: Sanitasi data dinamis
        $dynamicData = [];
        foreach ($request->except(['_token', 'letter_type_id']) as $key => $value) {
            $dynamicData[$key] = is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
        }

        // FIXED: Try-catch untuk mencegah error
        try {
            Letter::create([
                'id'              => Str::uuid(),
                'user_id'         => $user->id,
                'letter_type_id'  => $request->letter_type_id,
                'status'          => 'pending',
                'user_snapshot'   => $snapshot, 
                'additional_data' => empty($dynamicData) ? null : $dynamicData,
            ]);

            return redirect()->route('student.dashboard')->with('success', 'Surat berhasil diajukan!');
            
        } catch (\Exception $e) {
            Log::error('Letter creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat mengajukan surat. Silakan coba lagi.');
        }
    }

    /**
     * Membatalkan Pengajuan Surat (Mencegah IDOR)
     */
    public function cancel(Letter $letter)
    { 
        // FIXED: Keamanan berlapis
        if ($letter->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        if ($letter->status !== 'pending') {
            return back()->with('error', 'Surat sedang diproses dan tidak bisa dibatalkan.');
        }

        // FIXED: Try-catch untuk delete
        try {
            $letter->delete();
            return back()->with('success', 'Pengajuan berhasil dibatalkan.');
        } catch (\Exception $e) {
            Log::error('Letter cancellation failed', [
                'letter_id' => $letter->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal membatalkan pengajuan. Silakan coba lagi.');
        }
    }

    /**
     * Mengunduh PDF yang sudah disetujui
     * FIXED: Dengan try-catch dan logging yang proper
     */
    public function download(Letter $letter)
    {
        // FIXED: Validasi akses
        if ($letter->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak.');
        }

        if ($letter->status !== 'approved') {
            return back()->with('error', 'Surat belum disetujui oleh admin.');
        }

        // FIXED: Try-catch untuk download
        try {
            // Prioritas 1: Manual upload
            if (!empty($letter->manual_file_path)) {
                if (Storage::disk('local')->exists($letter->manual_file_path)) {
                    return Storage::disk('local')->download($letter->manual_file_path);
                }
            }

            // Prioritas 2: Auto-generate
            if (!empty($letter->file_path)) {
                if (Storage::disk('local')->exists($letter->file_path)) {
                    return Storage::disk('local')->download($letter->file_path);
                }
            }

            // FIXED: Jika file tidak ada, log dan beri pesan yang jelas
            Log::warning('PDF file not found', [
                'letter_id' => $letter->id,
                'file_path' => $letter->file_path,
                'manual_file_path' => $letter->manual_file_path,
            ]);
            
            return back()->with('error', 'File PDF tidak ditemukan. Silakan hubungi admin untuk upload manual.');
            
        } catch (\Exception $e) {
            Log::error('PDF download failed', [
                'letter_id' => $letter->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat mengunduh PDF. Silakan coba lagi atau hubungi admin.');
        }
    }

    /**
     * Verifikasi surat via UUID
     */
    public function verify($uuid)
    {
        // FIXED: Try-catch untuk verifikasi
        try {
            $letter = Letter::with('letterType')->findOrFail($uuid);
            return view('student.letters.verify', compact('letter'));
        } catch (\Exception $e) {
            Log::warning('Letter verification failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage()
            ]);
            abort(404, 'Surat tidak ditemukan atau tidak valid.');
        }
    }
}
