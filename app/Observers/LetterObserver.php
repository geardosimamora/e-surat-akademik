<?php

namespace App\Observers;

use App\Models\Letter;
use Illuminate\Support\Facades\Log;

class LetterObserver
{
    public function updated(Letter $letter): void
    {
        // KITA HAPUS wasChanged SEMENTARA UNTUK MEMAKSA MESIN JALAN
        if ($letter->status === 'approved' && empty($letter->file_path)) {
            
            try {
                // 1. Amankan Snapshot (Anti-Error)
                $snapshot = $letter->user_snapshot;
                if (!is_array($snapshot)) {
                    $snapshot = json_decode($snapshot, true) ?? [];
                }
                
                $dataAman = [
                    'name' => $snapshot['name'] ?? $letter->user->name ?? 'Nama Tidak Terdeteksi',
                    'nim'  => $snapshot['nim'] ?? $letter->user->nim ?? '-',
                ];

                // 2. QR Code
                $verifyUrl = route('verify.qr', $letter->id);
                $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($verifyUrl));

                // 3. Generate PDF
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($letter->letterType->template_view, [
                    'letter'   => $letter,
                    'snapshot' => $dataAman, // Gunakan data yang sudah diamankan
                    'qrCode'   => $qrCode,
                    'letterNumber' => $letter->letter_number ?? '-'
                ]);

                // 4. Simpan PDF ke Storage Private
                $fileName = 'surat_' . $letter->id . '.pdf';
                $path = 'private/letters/' . $fileName;
                \Illuminate\Support\Facades\Storage::put($path, $pdf->output());

                // 5. Berhasil! Simpan Path-nya.
                $letter->file_path = $path;
                $letter->saveQuietly();

            } catch (\Exception $e) {
                // JIKA MESIN RUSAK, DIA AKAN MENULIS ERRORNYA DI KOLOM CATATAN!
                $letter->status = 'rejected';
                $letter->rejection_note = "SYSTEM ERROR: " . $e->getMessage();
                $letter->saveQuietly();
            }
        }
    }
}