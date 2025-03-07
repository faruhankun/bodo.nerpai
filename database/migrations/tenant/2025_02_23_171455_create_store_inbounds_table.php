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
        Schema::create('store_inbounds', function (Blueprint $table) {
            // Primary key
            $table->id();
            $table->string('number')->nullable()->unique();

            // Foreign keys
            $table->foreignId('store_id')->nullable()->constrained('stores', 'id')->onDelete('set null');
            $table->foreignId('shipment_confirmation_id')->nullable()->constrained('shipment_confirmations', 'id')->onDelete('set null');
            $table->foreignId('store_employee_id')->nullable()->constrained('store_employees', 'id')->onDelete('set null');

            // Attributes
            $table->text('notes')->nullable();
            $table->string('status')->nullable();
            $table->date('date')->nullable();

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
        Schema::dropIfExists('store_inbounds');
    }
};
