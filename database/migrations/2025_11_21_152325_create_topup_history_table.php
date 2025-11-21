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
    Schema::create('topup_history', function (Blueprint $table) {
        $table->id();

        // relasi ke user
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        // jumlah koin
        $table->integer('amount');

        // metode top up
        $table->enum('method', ['button', 'voucher', 'admin', 'daily']);

        // deskripsi opsional
        $table->text('description')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('topup_history');
}

};
