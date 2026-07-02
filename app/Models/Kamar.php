<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    protected $table = 'kamar';

    protected $fillable = [
        'kost_id',
        'name',
        'price',
        'status',
        'description',
        'image_path',
    ];

    
    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class, 'kost_id');
    }

    
    public function atributKamar(): HasMany
    {
        return $this->hasMany(AtributKamar::class, 'kamar_id');
    }

    
    public function kamarFavorit(): HasMany
    {
        return $this->hasMany(KamarFavorit::class, 'kamar_id');
    }

    
    public function logKontak(): HasMany
    {
        return $this->hasMany(LogKontak::class, 'kamar_id');
    }

    
    public function fotoKamar()
    {
        if ($this->image_path) {
            return $this->image_path;
        }
        return $this->kost->fotoUtama();
    }
}
