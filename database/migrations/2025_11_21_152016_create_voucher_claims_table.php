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
    Schema::create('voucher_claims', function (Blueprint $table) {
        $table->id();
        
        // relasi ke users
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        // relasi ke vouchers
        $table->unsignedBigInteger('voucher_id');
        $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('voucher_claims');
}

};
