<?php

namespace App\Filament\Widgets;

use App\Models\Letter;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LetterStats extends BaseWidget
{
    // Mengatur urutan widget (semakin kecil semakin di atas)
    protected static ?int $sort = 1;

    // Mengatur agar widget ini otomatis memperbarui datanya setiap 10 detik tanpa perlu refresh halaman
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        return [
            // 1. Widget Total Semua Surat
            Stat::make('Total Pengajuan', Letter::count())
                ->description('Seluruh surat yang masuk ke sistem')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            // 2. Widget Surat yang Menunggu/Diproses (Butuh Perhatian Admin)
            Stat::make('Butuh Tindakan', Letter::whereIn('status', ['pending', 'processing'])->count())
                ->description('Surat menunggu atau sedang diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 8]), // Menambahkan grafik kecil pemanis (Sparkline)

            // 3. Widget Surat Selesai (Performa Admin)
            Stat::make('Selesai Disetujui', Letter::where('status', 'approved')->count())
                ->description('Surat berhasil dicetak PDF')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([1, 4, 9, 12, 15, 20, 25]), // Menambahkan grafik tren naik
        ];
    }
}