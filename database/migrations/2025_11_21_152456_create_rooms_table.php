<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            
            // Kode Unik Room (Misal: "A1B2") buat join
            $table->string('room_code')->unique();
            
            // Siapa Host (Player 1) & Guest (Player 2)
            $table->foreignId('host_id')->constrained('users');
            $table->foreignId('guest_id')->nullable()->constrained('users');
            
            // Status Game: 'waiting', 'playing', 'finished'
            $table->string('status')->default('waiting');
            
            // ID User yang sedang giliran jalan
            $table->unsignedBigInteger('current_turn_id')->nullable();
            
            // Siapa pemenangnya (jika sudah selesai)
            $table->unsignedBigInteger('winner_id')->nullable();

            // --- PENYIMPANAN DATA KARTU (JSON) ---
            // Kartu di meja (List kartu yang sudah ditaruh)
            $table->json('board_cards')->nullable();
            
            // Kartu di tangan Host
            $table->json('host_hand')->nullable();
            
            // Kartu di tangan Guest
            $table->json('guest_hand')->nullable();
            
            // Ujung kiri & kanan meja (untuk validasi)
            $table->integer('left_end')->nullable();
            $table->integer('right_end')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};