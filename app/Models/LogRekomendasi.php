<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogRekomendasi extends Model
{
    protected $table = 'log_rekomendasi';

    protected $fillable = [
        'user_id',
        'preference_summary',
        'results_count',
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
