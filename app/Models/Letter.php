<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Letter extends Model
{
    use HasUuids; // INI JAWABAN DARI PERTANYAAN SAYA SEBELUMNYA

    protected $fillable = [
        'user_id',
        'letter_type_id',
        'status',
        'letter_number',
        'rejection_note',
        'user_snapshot',
        'additional_data',
        'file_path',
        'manual_file_path',
        'catatan_admin',
        'approved_at',
        'approved_by',
    ];

    // Mengubah tipe data secara otomatis (Mutators/Casts)
    protected $casts = [
        'user_snapshot' => 'array',
        'additional_data' => 'array',
        'approved_at' => 'datetime',
    ];

    // Relasi ke User (Pemohon)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Jenis Surat
    public function letterType(): BelongsTo
    {
        return $this->belongsTo(LetterType::class);
    }

    // Relasi ke Admin/Kaprodi yang menyetujui
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // --- Query Scopes ---

    /**
     * Scope: hanya surat yang sudah disetujui.
     * Penggunaan: Letter::approved()->get()
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: hanya surat yang masih menunggu.
     * Penggunaan: Letter::pending()->get()
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}