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
        Schema::create('store_pos', function (Blueprint $table) {
            // Primary key
            $table->id();
            $table->string('number')->nullable()->unique();

            // Foreign keys
            $table->foreignId('store_id')->nullable()->constrained('stores', 'id')->onDelete('cascade');
            $table->foreignId('store_customer_id')->nullable()->constrained('store_customers', 'id')->onDelete('set null');
            $table->foreignId('store_employee_id')->nullable()->constrained('store_employees', 'id')->onDelete('set null');

            // Attributes
            $table->date('date');
            $table->decimal('tax_amount', 20, 2)->nullable()->default(0);
            $table->decimal('total_amount', 25, 2)->nullable()->default(0);
            $table->string('payment_method')->nullable()->default('CASH');
            $table->decimal('payment_amount', 25, 2)->nullable()->default(0);
            $table->string('status')->default('PAID');
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
        Schema::dropIfExists('store_pos');
    }
};
