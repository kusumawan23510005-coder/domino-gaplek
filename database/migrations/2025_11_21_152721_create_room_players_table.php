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
    Schema::create('room_players', function (Blueprint $table) {
        $table->id();

        // relasi ke room
        $table->unsignedBigInteger('room_id');
        $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

        // relasi ke user
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        // seat (posisi pemain 1-4)
        $table->tinyInteger('seat');

        // jumlah kartu sisa
        $table->integer('card_count')->default(7);

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('room_players');
}

};
