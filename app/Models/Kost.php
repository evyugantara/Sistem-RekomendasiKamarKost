<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kost extends Model
{
    protected $table = 'kost';

    protected $fillable = [
        'user_id',
        'kampus_id',
        'name',
        'price',
        'address',
        'latitude',
        'longitude',
        'description',
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
    public function kampus(): BelongsTo
    {
        return $this->belongsTo(Kampus::class, 'kampus_id');
    }

    
    public function fotos(): HasMany
    {
        return $this->hasMany(FotoKost::class, 'kost_id');
    }

    
    public function atributKost(): HasMany
    {
        return $this->hasMany(AtributKost::class, 'kost_id');
    }

    
    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class, 'kost_id');
    }

    
    public function fotoUtama()
    {
        $primary = $this->fotos()->where('is_primary', true)->first();
        if ($primary) {
            return $primary->image_path;
        }
        $any = $this->fotos()->first();
        if ($any) {
            return $any->image_path;
        }
        return 'images/default-kost.jpg';
    }
}
