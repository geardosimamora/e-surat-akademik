<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // WAJIB DIIMPORT
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil/pengaturan.
     */
    public function edit()
    {
        // Pastikan Anda mengirim data user agar form bisa menampilkan nama/email saat ini
        $user = Auth::user();
        return view('student.profile', compact('user'));
    }

    /**
     * Memperbarui Password Mahasiswa.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'], // Validasi otomatis cek password lama
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.'
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }
    
    /**
     * Memperbarui Data Dasar Profil (Nama & Email).
     */
    public function updateProfile(Request $request) // Diubah jadi camelCase
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Rule unique mengecualikan ID user saat ini agar tidak error saat klik simpan
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'name.required' => 'Nama lengkap wajib diisi.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
