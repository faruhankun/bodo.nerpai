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
        Schema::create('inventory_transfer_items', function (Blueprint $table) {
            // Primary key
            $table->id();

            // foreign key
            $table->foreignId('inventory_transfer_id')->constrained('inventory_transfers', 'id')->onDelete('cascade');
            $table->string('item_type')->nullable()->default('IVT');
            $table->unsignedBigInteger('item_id')->nullable();

            // Attributes
            $table->integer('quantity')->default(1);
            $table->decimal('cost_per_unit', 15, 2)->default(0);
            $table->decimal('total_cost', 25, 2)->default(0);
            $table->string('notes')->nullable();

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
        Schema::dropIfExists('inventory_transfer_items');
    }
};
