<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->string('room_code')->unique();
        $table->foreignId('host_id')->constrained('users'); // User A
        $table->foreignId('guest_id')->nullable()->constrained('users'); // User B
        $table->string('status')->default('waiting'); // waiting, playing, finished
        
        // Data Kartu (JSON)
        $table->json('board_cards')->nullable();
        $table->json('host_hand')->nullable();
        $table->json('guest_hand')->nullable();

        // <--- TAMBAHAN PENTING (Ini yang bikin error kalau tidak ada)
        $table->unsignedBigInteger('current_turn_id')->nullable(); // Siapa yang jalan
        $table->integer('pass_count')->default(0); // Hitung berapa kali pass (buat gaplek)
        $table->unsignedBigInteger('winner_id')->nullable(); // Siapa pemenangnya
        // <--- SELESAI TAMBAHAN

        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};