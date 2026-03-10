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
    Schema::table('products', function (Blueprint $table) {
        // Kolom enum untuk menyimpan status saat ini, default null (kosong)
        $table->enum('current_condition', ['good', 'damaged', 'expired'])->nullable()->after('stock');
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('current_condition');
    });
}
};
