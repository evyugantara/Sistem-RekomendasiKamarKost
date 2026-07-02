<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferensiMahasiswa extends Model
{
    protected $table = 'preferensi_mahasiswa';

    protected $fillable = [
        'user_id',
        'kriteria_id',
        'opsi_kriteria_id',
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
