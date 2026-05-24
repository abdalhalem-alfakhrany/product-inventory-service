<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('sku');
            $table->string('name');
            $table->text('description');
            $table->decimal('price');
            $table->unsignedInteger('stock_quantity');
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->enum('status', ['active', 'inactive', 'discontinued']);
            $table->timestamps();
            $table->softDeletes();
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
