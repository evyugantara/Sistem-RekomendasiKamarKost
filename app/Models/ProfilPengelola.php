<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilPengelola extends Model
{
    protected $table = 'profil_pengelola';

    protected $fillable = [
        'user_id',
        'ktp_number',
        'phone',
        'address',
        'ktp_file',
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
