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
        Schema::create('outbound_items', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('outbound_id')->constrained();
            $table->foreignId('inventory_id')->constrained();
            // $table->foreignId('warehouse_location_id')->constrained();

            // Columns
            $table->integer('quantity')->nullable()->default(1);
            $table->decimal('cost_per_unit', 15, 2)->nullable()->default(0);
            $table->decimal('total_cost', 25, 2)->nullable()->default(0);
            $table->text('notes')->nullable();

            // Timestamps (optional, not in ERD but commonly used)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_items');
    }
};
