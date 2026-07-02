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

    /**
     * Relasi ke Pengelola (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Kampus
     */
    public function kampus(): BelongsTo
    {
        return $this->belongsTo(Kampus::class, 'kampus_id');
    }

    /**
     * Relasi ke Foto Kost
     */
    public function fotos(): HasMany
    {
        return $this->hasMany(FotoKost::class, 'kost_id');
    }

    /**
     * Relasi ke Atribut/Spesifikasi Kost
     */
    public function atributKost(): HasMany
    {
        return $this->hasMany(AtributKost::class, 'kost_id');
    }

    /**
     * Relasi ke Kamar-kamar Kost
     */
    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class, 'kost_id');
    }

    /**
     * Mendapatkan foto utama kost
     */
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
