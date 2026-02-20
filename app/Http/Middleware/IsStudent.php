<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('student.login');
        }

        // 2. CEK ROLE (Inilah logic yang Anda maksud tadi!)
        if (Auth::user()->role !== 'student') {
            // Jika dia bukan student (misal: Admin), kita tendang dia dengan error 403 Forbidden
            abort(403, 'Akses Ditolak. Halaman ini khusus untuk Mahasiswa.');
        }

        // Jika dia benar-benar student, silakan lewat
        return $next($request);
    }
}