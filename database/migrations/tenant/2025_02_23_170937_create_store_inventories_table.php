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
        Schema::create('store_inventories', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('store_id')->constrained('stores', 'id')->onDelete('cascade');
            $table->foreignId('store_product_id')->constrained('store_products', 'id')->onDelete('cascade');
            $table->foreignId('warehouse_location_id')->nullable()->constrained('warehouse_locations', 'id')->onDelete('set null');

            // Attributes
            $table->date('expire_date')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('in_transit_quantity')->default(0);
            $table->decimal('cost_per_unit', 20, 2)->default(0);

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
        Schema::dropIfExists('store_inventories');
    }
};
