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
        // PERBAIKAN 1: Nama tabel pakai akhiran 'ies' (jamak)
        Schema::create('match_histories', function (Blueprint $table) {
            $table->id();

            // ID User yang main (Wajib ada)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Hasil: 'win', 'lose', atau 'draw'
            $table->string('result');

            // Berapa XP yang didapat (Bisa positif atau negatif)
            $table->integer('xp_change')->default(0);

            // Keterangan: Misal "VS Bot" atau "Online Room X"
            // Ini penting biar tau history ini dari game apa
            $table->string('desc')->nullable();

            // room_id kita hapus atau buat nullable (opsional) karena VS Bot tidak punya room_id
            // $table->foreignId('room_id')->nullable()->constrained('rooms'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_histories');
    }
};
