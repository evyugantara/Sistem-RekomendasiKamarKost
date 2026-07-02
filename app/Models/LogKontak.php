<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogKontak extends Model
{
    protected $table = 'log_kontak';

    protected $fillable = [
        'user_id',
        'kamar_id',
        'contact_type',
    ];

    /**
     * Relasi ke User (Mahasiswa)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Kamar
     */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
}
