<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoKost extends Model
{
    protected $table = 'foto_kost';

    protected $fillable = [
        'kost_id',
        'image_path',
        'is_primary',
    ];

    /**
     * Relasi ke Kost
     */
    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class, 'kost_id');
    }
}
