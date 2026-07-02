<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $fillable = [
        'name',
        'type',     // 'select', 'checkbox'
        'category', // 'umum', 'pribadi', 'bersama'
    ];

    /**
     * Relasi ke Opsi Kriteria
     */
    public function opsiKriteria(): HasMany
    {
        return $this->hasMany(OpsiKriteria::class, 'kriteria_id');
    }

    /**
     * Relasi ke Atribut Kost
     */
    public function atributKost(): HasMany
    {
        return $this->hasMany(AtributKost::class, 'kriteria_id');
    }

    /**
     * Relasi ke Preferensi Mahasiswa
     */
    public function preferensiMahasiswa(): HasMany
    {
        return $this->hasMany(PreferensiMahasiswa::class, 'kriteria_id');
    }
}
