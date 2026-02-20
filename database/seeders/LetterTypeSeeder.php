<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterTypeSeeder extends Seeder
{
    /**
     * Seed Letter Types untuk sistem E-Surat
     */
    public function run(): void
    {
        $letterTypes = [
            [
                'code' => 'SKA',
                'name' => 'Surat Keterangan Aktif Kuliah',
                'template_view' => 'letters.active_student',
                'requires_approval' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SKP',
                'name' => 'Surat Permohonan Kerja Praktek',
                'template_view' => 'letters.kerja_praktek',
                'requires_approval' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('letter_types')->insert($letterTypes);
    }
}
