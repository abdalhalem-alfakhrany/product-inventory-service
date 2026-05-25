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
        DB::statement("CREATE TYPE product_status AS ENUM ('active', 'inactive', 'discontinued')");

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sku');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE products ADD COLUMN status product_status NOT NULL DEFAULT 'active'");

        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_price_positive CHECK (price >= 0)');
        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_stock_non_negative CHECK (stock_quantity >= 0)');
        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_threshold_non_negative CHECK (low_stock_threshold >= 0)');

        DB::statement('CREATE UNIQUE INDEX idx_products_sku ON products (sku) WHERE deleted_at IS NULL');
        DB::statement('CREATE INDEX idx_products_active ON products (id) WHERE deleted_at IS NULL');
        DB::statement('CREATE INDEX idx_products_low_stock ON products (stock_quantity, low_stock_threshold) WHERE deleted_at IS NULL AND status = \'active\'');
        DB::statement('CREATE INDEX idx_products_status_created ON products (status, created_at DESC) WHERE deleted_at IS NULL');
        DB::statement('CREATE INDEX idx_products_updated_at ON products (updated_at DESC) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        DB::statement('DROP TYPE IF EXISTS product_status');
    }
};
