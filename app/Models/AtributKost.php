<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtributKost extends Model
{
    protected $table = 'atribut_kost';

    protected $fillable = [
        'kost_id',
        'kriteria_id',
        'opsi_kriteria_id',
    ];

    /**
     * Relasi ke Kost
     */
    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class, 'kost_id');
    }

    /**
     * Relasi ke Kriteria
     */
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    /**
     * Relasi ke Opsi Kriteria
     */
    public function opsiKriteria(): BelongsTo
    {
        return $this->belongsTo(OpsiKriteria::class, 'opsi_kriteria_id');
    }
}
