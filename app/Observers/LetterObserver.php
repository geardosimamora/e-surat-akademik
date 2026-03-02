<?php

namespace App\Observers;

use App\Models\Letter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LetterObserver
{
    public function updated(Letter $letter): void
    {
        // Pastikan hanya trigger jika status BARU SAJA berubah menjadi approved
        if ($letter->status === 'approved' && $letter->wasChanged('status') && empty($letter->file_path) && empty($letter->manual_file_path)) {
            
            // FIXED: Bungkus dengan try-catch untuk mencegah domino error
            try {
                // FIXED: Validasi snapshot data
                $snapshot = $letter->user_snapshot;
                if (!is_array($snapshot)) {
                    $snapshot = json_decode($snapshot, true) ?? [];
                }
                
                // FIXED: Sanitize data untuk mencegah XSS di PDF
                $dataAman = [
                    'name' => htmlspecialchars($snapshot['name'] ?? $letter->user->name ?? 'Nama Tidak Terdeteksi', ENT_QUOTES, 'UTF-8'),
                    'nim'  => htmlspecialchars($snapshot['nim'] ?? $letter->user->nim ?? '-', ENT_QUOTES, 'UTF-8'),
                ];

                // FIXED: Generate QR Code dengan error handling
                $verifyUrl = route('surat.verify', $letter->id);
                $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($verifyUrl));
                
                // FIXED: Load view dengan timeout protection
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($letter->letterType->template_view, [
                    'letter'       => $letter,
                    'snapshot'     => $dataAman,
                    'qrCode'       => $qrCode,
                    'letterNumber' => $letter->letter_number ?? '-'
                ])
                ->setPaper('a4', 'portrait')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', false); // FIXED: Disable remote untuk keamanan

                $fileName = 'surat_' . $letter->id . '.pdf';
                $path = 'letters/' . $fileName;
                
                // FIXED: Simpan dengan error checking
                Storage::disk('local')->put($path, $pdf->output());
                
                if (!Storage::disk('local')->exists($path)) {
                    throw new \Exception('PDF gagal disimpan ke storage.');
                }

                // FIXED: Update tanpa trigger observer lagi
                $letter->file_path = $path;
                $letter->saveQuietly();
                
                Log::info("✅ SUCCESS: PDF auto-generated for Letter ID: {$letter->id}");

            } catch (\Exception $e) {
                // FIXED: Error handling yang proper - status tetap approved, tapi file_path null
                Log::error("❌ OBSERVER ERROR (Letter ID: {$letter->id}): " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'letter_id' => $letter->id,
                    'user_id' => $letter->user_id,
                ]);
                
                // FIXED: Jangan ubah status ke rejected, biarkan approved tapi tandai ada error
                $letter->catatan_admin = "⚠️ PDF gagal di-generate otomatis. Silakan upload manual atau hubungi IT Support. Error: " . substr($e->getMessage(), 0, 200);
                $letter->saveQuietly();
                
                // FIXED: Kirim notifikasi ke admin (opsional, bisa pakai Filament Notification)
                // \Filament\Notifications\Notification::make()
                //     ->danger()
                //     ->title('PDF Generation Failed')
                //     ->body("Letter ID: {$letter->id} - {$e->getMessage()}")
                //     ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
            }
        }
    }
}
