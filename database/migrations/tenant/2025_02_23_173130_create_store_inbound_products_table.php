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
        Schema::create('store_inbound_products', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('store_inbound_id')->nullable()->constrained('store_inbounds', 'id')->onDelete('cascade');
            $table->foreignId('store_product_id')->nullable()->constrained('store_products', 'id')->onDelete('cascade');
            $table->foreignId('warehouse_location_id')->nullable()->constrained('warehouse_locations', 'id')->onDelete('set null');

            // Attributes
            $table->decimal('cost_per_unit', 20, 2)->default(0);
            $table->decimal('total_cost', 25, 2)->default(0);
            $table->integer('quantity');
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
        Schema::dropIfExists('store_inbound_products');
    }
};
