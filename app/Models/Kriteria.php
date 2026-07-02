<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $fillable = [
        'name',
        'type',     
        'category', 
    ];

    
    public function opsiKriteria(): HasMany
    {
        return $this->hasMany(OpsiKriteria::class, 'kriteria_id');
    }

    
    public function atributKost(): HasMany
    {
        return $this->hasMany(AtributKost::class, 'kriteria_id');
    }

    
    public function preferensiMahasiswa(): HasMany
    {
        return $this->hasMany(PreferensiMahasiswa::class, 'kriteria_id');
    }
}
