<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Keys
            $table->foreignId('product_id')->constrained();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            // warehouse location

            // Columns
            $table->date('expire_date')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('in_transit_quantity')->default(0);

            $table->decimal('cost_per_unit', 20, 2)->default(0);

            // Timestamps (optional, not in schema but commonly used)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
