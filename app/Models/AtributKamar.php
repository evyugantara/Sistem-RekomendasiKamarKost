<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtributKamar extends Model
{
    protected $table = 'atribut_kamar';

    protected $fillable = [
        'kamar_id',
        'kriteria_id',
        'opsi_kriteria_id',
    ];

    
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    
    public function opsiKriteria(): BelongsTo
    {
        return $this->belongsTo(OpsiKriteria::class, 'opsi_kriteria_id');
    }
}
