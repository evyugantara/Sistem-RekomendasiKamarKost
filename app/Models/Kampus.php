<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kampus extends Model
{
    protected $table = 'kampus';

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];

    
    public function kosts(): HasMany
    {
        return $this->hasMany(Kost::class, 'kampus_id');
    }
}
