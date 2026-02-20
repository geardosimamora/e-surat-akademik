<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Student\LetterController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\VerificationController;
use App\Http\Middleware\IsStudent;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('student.login');
});

// --- AREA TAMU (GUEST) ---
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [AuthController::class, 'showLogin'])->name('student.login');
    Route::post('/login', [AuthController::class, 'login'])->name('student.login.process');
    
    // Register Routes
    Route::get('/register', [AuthController::class, 'showRegister'])->name('student.register');
    Route::post('/register', [AuthController::class, 'register'])->name('student.register.process');
});

// --- AREA MAHASISWA (PRIVATE - DENGAN PREFIX) ---
Route::middleware(['auth', IsStudent::class])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [LetterController::class, 'index'])->name('dashboard');
        
        // Profile Routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/update', [ProfileController::class, 'updateProfile'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        });
        
        // Letter Routes
        Route::prefix('letters')->name('letters.')->group(function() {
            // Create Letter
            Route::get('/create', [LetterController::class, 'create'])->name('create');
            Route::post('/', [LetterController::class, 'store'])->name('store');
            
            // Cancel Letter (DELETE)
            Route::delete('/{letter}/cancel', [LetterController::class, 'cancel'])->name('cancel');
            
            // Download Letter (GET)
            Route::get('/{letter}/download', [LetterController::class, 'download'])->name('download');
        });
    });

// --- AREA PUBLIK (TANPA LOGIN) ---
// Verifikasi QR Code - Menggunakan UUID agar bisa diakses tanpa login
Route::get('/verify/{letter:uuid}', [VerificationController::class, 'verify'])
    ->name('verify.qr');

// Download/Preview PDF - Menggunakan verification_token
Route::get('/verification/{token}', [VerificationController::class, 'show'])
    ->name('verification.show');

// Catatan: Rute profile sudah didefinisikan di dalam group student.
// Tidak perlu didefinisikan ulang di luar group karena akan menyebabkan konflik.