<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nim',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Security Gate untuk Filament Admin Panel
    public function canAccessPanel(Panel $panel): bool
    {
        // Hanya user dengan role admin atau head yang bisa masuk Filament
        return in_array($this->role, ['admin', 'head_of_program']) && $this->is_active;
    }

    // Relasi: Satu user bisa punya banyak pengajuan surat
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }
}