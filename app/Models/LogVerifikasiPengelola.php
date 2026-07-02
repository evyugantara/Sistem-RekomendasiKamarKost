<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogVerifikasiPengelola extends Model
{
    protected $table = 'log_verifikasi_pengelola';

    protected $fillable = [
        'admin_name',
        'owner_name',
        'owner_email',
        'owner_phone',
        'kost_name',
        'status',
    ];
}
