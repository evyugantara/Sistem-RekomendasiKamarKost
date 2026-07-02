<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilMahasiswa extends Model
{
    protected $table = 'profil_mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'university',
        'major',
        'gender',
        'phone',
        'address',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
