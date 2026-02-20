<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Letter $letter)
    {
        // Jika surat belum di-approve, tolak!
        if ($letter->status !== 'approved') {
            abort(404, 'Surat tidak valid atau belum disetujui.');
        }

        // Tampilan sementara untuk membuktikan QR jalan
        return response("
            <h1>✅ VERIFIKASI BERHASIL</h1>
            <p>Ini adalah surat resmi yang dikeluarkan oleh Prodi Sistem Informasi.</p>
            <p><strong>Atas Nama:</strong> {$letter->user_snapshot['name']}</p>
            <p><strong>NIM:</strong> {$letter->user_snapshot['nim']}</p>
            <p><strong>Nomor Surat:</strong> {$letter->letter_number}</p>
        ");
    }

    public function show($token)
        {
            $letter = Letter::findOrFail($token);

            // Jika surat belum di-approve, tolak!
            if ($letter->status !== 'approved') {
                abort(404, 'Surat tidak valid atau belum disetujui.');
            }

            // Tentukan view berdasarkan letter_type_id
            $viewMap = [
                1 => 'letters.active_student',
                2 => 'letters.kerja_praktek',
            ];

            $view = $viewMap[$letter->letter_type_id] ?? 'letters.active_student';

            return view($view, compact('letter'));
        }

}