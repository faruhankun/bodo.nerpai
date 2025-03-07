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
        Schema::create('store_pos_products', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('store_pos_id')->nullable()->constrained('store_pos', 'id')->onDelete('cascade');
            $table->foreignId('store_product_id')->nullable()->constrained('store_products', 'id')->onDelete('cascade');

            // Attributes
            $table->integer('quantity')->default(1);
            $table->decimal('price', 20, 2)->default(0);
            $table->decimal('subtotal', 25, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('cost_per_unit', 20, 2)->default(0);
            $table->decimal('total_cost', 25, 2)->default(0);
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_pos_products');
    }
};
