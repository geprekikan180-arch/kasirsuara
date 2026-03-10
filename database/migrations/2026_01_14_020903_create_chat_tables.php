<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('conversations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
        $table->foreignId('user_one_id')->constrained('users');
        $table->foreignId('user_two_id')->constrained('users');
        $table->timestamps();
    });

    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
        $table->foreignId('sender_id')->constrained('users');
        $table->text('body');
        $table->boolean('is_read')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_tables');
    }
};
