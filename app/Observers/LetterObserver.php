<?php

namespace App\Observers;

use App\Models\Letter;
use Illuminate\Support\Facades\Log;

class LetterObserver
{
    public function updated(Letter $letter): void
    {
        // Pastikan hanya trigger jika status BARU SAJA berubah menjadi approved
        if($letter->status === 'approved' && $letter->wasChanged('status') && empty($letter->file_path) && empty($letter->manual_file_path))
        {            
            try {
                $snapshot = $letter->user_snapshot;
                if (!is_array($snapshot)) {
                    $snapshot = json_decode($snapshot, true) ?? [];
                }
                
                $dataAman = [
                    'name' => $snapshot['name'] ?? $letter->user->name ?? 'Nama Tidak Terdeteksi',
                    'nim'  => $snapshot['nim'] ?? $letter->user->nim ?? '-',
                ];

                $verifyUrl = route('surat.verify', $letter->id);
                $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($verifyUrl));
                
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($letter->letterType->template_view, [
                    'letter'   => $letter,
                    'snapshot' => $dataAman,
                    'qrCode'   => $qrCode,
                    'letterNumber' => $letter->letter_number ?? '-'
                ]);

                $fileName = 'surat_' . $letter->id . '.pdf';
                
                // FIX PATH: Biarkan Laravel yang mengatur root-nya (storage/app/private/letters)
                $path = 'letters/' . $fileName;
                
                \Illuminate\Support\Facades\Storage::disk('local')->put($path, $pdf->output());
                
                if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
                    throw new \Exception('PDF gagal disimpan ke storage.');
                }

                $letter->file_path = $path;
                $letter->saveQuietly();
                
                \Illuminate\Support\Facades\Log::info("SUCCESS: PDF auto-generated for Letter ID: {$letter->id}");

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("OBSERVER ERROR (Letter ID: {$letter->id}): " . $e->getMessage());
                $letter->status = 'rejected';
                $letter->catatan_admin = "SYSTEM ERROR: " . $e->getMessage();
                $letter->saveQuietly();
            }
        }    }
}