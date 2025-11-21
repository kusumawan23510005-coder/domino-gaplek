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
    Schema::create('room_cards', function (Blueprint $table) {
        $table->id();

        // relasi ke room
        $table->unsignedBigInteger('room_id');
        $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

        // relasi ke pemain
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        // relasi ke kartu domino
        $table->unsignedBigInteger('card_id');
        $table->foreign('card_id')->references('id')->on('domino_cards')->onDelete('cascade');

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('room_cards');
}

};
