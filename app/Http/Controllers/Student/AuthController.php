<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login mahasiswa
     */
    public function showLogin()
    {
        return view('student.auth.login');
    }

    /**
     * Proses login mahasiswa
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nim' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'nim.required' => 'NIM wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        // Cek login pakai NIM dan pastikan rolenya student serta akun aktif
        if (Auth::attempt([
            'nim' => $credentials['nim'], 
            'password' => $credentials['password'], 
            'role' => 'student',
            'is_active' => true
        ])) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('student.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'nim' => 'NIM atau password salah, atau akun Anda tidak aktif.',
        ])->onlyInput('nim');
    }

    /**
     * Tampilkan halaman registrasi mahasiswa
     */
    public function showRegister()
    {
        return view('student.auth.register');
    }

    /**
     * Proses registrasi mahasiswa baru
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:20', 'unique:users,nim'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:15'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'nim.max' => 'NIM maksimal 20 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'phone.max' => 'Nomor telepon maksimal 15 karakter',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'nim' => $validated['nim'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student', // FORCE ROLE STUDENT (Keamanan)
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            // Tidak auto-login, redirect ke halaman login
            // Auth::login($user);

            return redirect()->route('student.login')
                ->with('success', 'Pendaftaran berhasil! Silakan login dengan NIM dan password Anda.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'
            ])->withInput();
        }
    }

    /**
     * Proses logout mahasiswa
     */
    public function logout(Request $request)
    {
        $name = Auth::user()->name ?? 'Pengguna';
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('student.login')
            ->with('success', 'Sampai jumpa kembali, ' . $name . '! Anda telah logout.');
    }
}