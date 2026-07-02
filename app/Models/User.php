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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Profil Mahasiswa
     */
    public function profilMahasiswa(): HasOne
    {
        return $this->hasOne(ProfilMahasiswa::class, 'user_id');
    }

    /**
     * Relasi ke Profil Pengelola
     */
    public function profilPengelola(): HasOne
    {
        return $this->hasOne(ProfilPengelola::class, 'user_id');
    }

    /**
     * Relasi ke Kost (Milik Pengelola)
     */
    public function kosts(): HasMany
    {
        return $this->hasMany(Kost::class, 'user_id');
    }

    /**
     * Relasi ke Preferensi Kriteria (Milik Mahasiswa)
     */
    public function preferensiMahasiswa(): HasMany
    {
        return $this->hasMany(PreferensiMahasiswa::class, 'user_id');
    }

    /**
     * Relasi ke Kost Favorit (Milik Mahasiswa)
     */
    public function kostFavorit(): HasMany
    {
        return $this->hasMany(KostFavorit::class, 'user_id');
    }

    /**
     * Relasi ke Log Kontak (Milik Mahasiswa)
     */
    public function logKontak(): HasMany
    {
        return $this->hasMany(LogKontak::class, 'user_id');
    }

    /**
     * Relasi ke Log Rekomendasi (Milik Mahasiswa)
     */
    public function logRekomendasi(): HasMany
    {
        return $this->hasMany(LogRekomendasi::class, 'user_id');
    }
}
