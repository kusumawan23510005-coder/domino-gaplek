<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopupHistory extends Model
{
    use HasFactory;

    // PENTING: Sambungkan ke tabel yang baru kamu buat migrasinya
    protected $table = 'topup_histories';

    protected $fillable = [
        'user_id',
        'amount',      // Ini adalah jumlah XP yang didapat
        'method',      // Contoh: 'quest', 'voucher', 'admin_gift'
        'description'  // Contoh: 'Hadiah naik level'
    ];
}
