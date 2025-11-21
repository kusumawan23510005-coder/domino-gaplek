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
    Schema::create('board_cards', function (Blueprint $table) {
        $table->id();

        // room terkait
        $table->unsignedBigInteger('room_id');
        $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

        // kartu yang dimainkan
        $table->unsignedBigInteger('card_id');
        $table->foreign('card_id')->references('id')->on('domino_cards')->onDelete('cascade');

        // posisi kartu di papan
        $table->integer('position');

        // kiri / kanan
        $table->enum('direction', ['left', 'right']);

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('board_cards');
}

};
