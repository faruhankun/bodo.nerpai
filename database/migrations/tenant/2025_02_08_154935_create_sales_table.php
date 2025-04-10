<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            // Primary Key
            $table->id();
            $table->string('number')->unique()->nullable();

            // Foreign Keys
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('consignee_type')->nullable()->default('CUST');                   // 'WH', 'SUP', 'CUST'
            $table->unsignedBigInteger('consignee_id')->nullable();

            // Columns
            $table->date('date');
            $table->decimal('total_amount', 30, 2)->default(0);
            $table->string('status')->default('SO_OFFER');
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('estimated_shipping_fee', 15, 2)->default(0);
            $table->decimal('shipping_fee_discount', 15, 2)->default(0);

            // Timestamps (optional, not in schema but commonly used)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
