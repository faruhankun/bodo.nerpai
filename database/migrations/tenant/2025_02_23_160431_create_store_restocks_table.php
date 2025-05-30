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
        Schema::create('store_restocks', function (Blueprint $table) {
            // Primary key
            $table->id();
            $table->string('number')->nullable()->unique();

            // Foreign keys
            $table->foreignId('store_id')->nullable()->constrained('stores', 'id')->onDelete('set null');
            $table->foreignId('store_employee_id')->nullable()->constrained('store_employees', 'id');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');

            // Attributes
            $table->date('restock_date');
            $table->decimal('total_amount', 30, 2)->default(0);
            $table->string('status')->default('STR_REQUEST');
            $table->text('admin_notes')->nullable();
            $table->text('team_notes')->nullable();

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
        Schema::dropIfExists('store_restocks');
    }
};
