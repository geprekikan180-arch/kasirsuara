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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
        $table->string('code'); // Barcode/SKU
        $table->string('name');
        $table->string('category')->nullable();
        $table->bigInteger('price');
        $table->integer('stock')->default(0);
        $table->string('unit'); // pcs, kg, dll
        $table->timestamps();
        
        // Kode barang harus unik di dalam satu toko (toko A dan B boleh punya kode '123' masing-masing)
        $table->unique(['shop_id', 'code']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
