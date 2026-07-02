<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpsiKriteria extends Model
{
    protected $table = 'opsi_kriteria';

    protected $fillable = [
        'kriteria_id',
        'value',
    ];

    
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    
    public function atributKost(): HasMany
    {
        return $this->hasMany(AtributKost::class, 'opsi_kriteria_id');
    }

    
    public function preferensiMahasiswa(): HasMany
    {
        return $this->hasMany(PreferensiMahasiswa::class, 'opsi_kriteria_id');
    }
}
