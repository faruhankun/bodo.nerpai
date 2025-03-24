<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            // Primary Key
            $table->id('id');

            // Foreign Keys
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->string('item_type')->nullable()->default('PRD');            // PRD, SRV
            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreignId('inventory_id')->constrained();

            // Columns
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('subtotal', 25, 2)->default(0);
            $table->decimal('cost_per_unit', 20, 2)->default(0);
            $table->decimal('total_cost', 25, 2)->default(0);
            $table->string('notes')->nullable();

            // Timestamps (optional, not in schema but commonly used)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
