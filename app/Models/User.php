<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function profilMahasiswa(): HasOne
    {
        return $this->hasOne(ProfilMahasiswa::class, 'user_id');
    }

    
    public function profilPengelola(): HasOne
    {
        return $this->hasOne(ProfilPengelola::class, 'user_id');
    }

    
    public function kosts(): HasMany
    {
        return $this->hasMany(Kost::class, 'user_id');
    }

    
    public function preferensiMahasiswa(): HasMany
    {
        return $this->hasMany(PreferensiMahasiswa::class, 'user_id');
    }

    
    public function kostFavorit(): HasMany
    {
        return $this->hasMany(KostFavorit::class, 'user_id');
    }

    
    public function logKontak(): HasMany
    {
        return $this->hasMany(LogKontak::class, 'user_id');
    }

    
    public function logRekomendasi(): HasMany
    {
        return $this->hasMany(LogRekomendasi::class, 'user_id');
    }
}
