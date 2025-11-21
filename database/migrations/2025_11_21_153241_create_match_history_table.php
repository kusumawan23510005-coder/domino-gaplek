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
    Schema::create('match_history', function (Blueprint $table) {
        $table->id();

        // room tempat pertandingan
        $table->unsignedBigInteger('room_id');
        $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

        // pemenang pertandingan
        $table->unsignedBigInteger('winner_id');
        $table->foreign('winner_id')->references('id')->on('users')->onDelete('cascade');

        // poin kemenangan (jumlah pip lawan dsb)
        $table->integer('points')->default(0);

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('match_history');
}

};
