<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Kolom-kolom yang boleh diisi secara massal (Mass Assignment)
    protected $fillable = [
        'room_code',
        'host_id',
        'guest_id',
        'status',
        'current_turn_id',
        'winner_id',
        'board_cards', // Kartu di meja
        'host_hand',   // Kartu pegangan Host
        'guest_hand',  // Kartu pegangan Guest
        'left_end',
        'right_end'
    ];

    // PENTING: Mengubah format JSON di database menjadi Array di PHP otomatis
    // Jadi kamu tidak perlu pakai json_decode/json_encode manual
    protected $casts = [
        'board_cards' => 'array',
        'host_hand' => 'array',
        'guest_hand' => 'array',
    ];

    // Relasi: Room dimiliki oleh Host (User)
    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    // Relasi: Room ditempati oleh Guest (User)
    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }
}