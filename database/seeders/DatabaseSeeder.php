<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat akun Admin
        User::create([
            'name' => 'Administrator TU',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'), // Enkripsi password
            'role' => 'admin', // INI KUNCINYA. Kita force role-nya jadi admin
            'is_active' => true,
        ]);

        // Opsional: Sekalian buat 1 akun mahasiswa untuk testing nanti
        User::create([
            'name' => 'Geardo Lapista Simamora',
            'email' => 'geardo@student.unimal.ac.id',
            'nim' => '230180121',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        // Seed Letter Types
        $this->call(LetterTypeSeeder::class);
    }
}