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
    Schema::create('domino_cards', function (Blueprint $table) {
        $table->id();
        $table->integer('left_value');
        $table->integer('right_value');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('domino_cards');
}

};
