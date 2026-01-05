<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PENTING: Saya ubah nama tabel jadi jamak 'topup_histories' 
        // karena standar Laravel mengharuskan nama tabel jamak (plural).
        // Jika tetap 'topup_history' (singular), nanti Model kamu bingung nyarinya.
        Schema::create('topup_histories', function (Blueprint $table) {
            $table->id();

            // Relasi ke user (Syntax modern & lebih aman)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Jumlah koin/XP
            $table->integer('amount');

            // --- PERUBAHAN UTAMA DI SINI ---
            // DULU: $table->enum('method', ['button', 'voucher',...]); -> INI MENGIKAT LEHERMU
            // SEKARANG: String bebas. Bisa diisi 'gameplay', 'quest', 'admin', terserah.
            $table->string('method');

            // Deskripsi
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topup_histories');
    }

};
