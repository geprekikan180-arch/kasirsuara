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
    Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users'); // Siapa yang input
        $table->integer('quantity');
        $table->enum('type', ['in', 'out', 'adjustment']);
        $table->enum('condition', ['good', 'damaged', 'expired']);
        $table->text('notes')->nullable();
        $table->date('date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
