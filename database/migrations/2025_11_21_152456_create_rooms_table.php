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
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();

        // kode room unik
        $table->string('room_code')->unique();

        // status: room menunggu, sedang bermain, atau selesai
        $table->enum('status', ['waiting', 'playing', 'finished'])->default('waiting');

        // siapa yang membuat room
        $table->unsignedBigInteger('created_by');
        $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

        // giliran pemain sekarang (user id)
        $table->unsignedBigInteger('current_turn')->nullable();
        $table->foreign('current_turn')->references('id')->on('users')->onDelete('set null');

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('rooms');
}

};
