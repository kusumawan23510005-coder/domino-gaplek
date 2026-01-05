<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchHistory extends Model
{
    use HasFactory;

    // INI WAJIB SAMA dengan nama tabel di database baru
    protected $table = 'match_histories';

    protected $fillable = [
        'user_id',
        'result',
        'xp_change',
        'desc'
    ];
}
