<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    // --- TAMBAHKAN BAGIAN INI ---
    // Ini memerintahkan Laravel: "Setiap kolom ini dipanggil, tolong otomatis ubah jadi Array"
    protected $casts = [
        'board_cards' => 'array',
        'host_hand'   => 'array',
        'guest_hand'  => 'array',
    ];
}
