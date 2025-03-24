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
        Schema::create('inventory_transfers', function (Blueprint $table) {
            // Primary key
            $table->id();
            $table->string('number')->nullable()->unique();

            // Polymorphic shipper (WH/ST)
            $table->string('shipper_type')->nullable();  // 'WH', 'ST'
            $table->unsignedBigInteger('shipper_id')->nullable();

            // Polymorphic consignee (WH/SUP/CUST)
            $table->string('consignee_type')->nullable(); // 'WH', 'ST'
            $table->unsignedBigInteger('consignee_id')->nullable();

            // Addresses
            $table->json('origin_address')->nullable();
            $table->json('destination_address')->nullable();

            // courier

            // Details
            $table->date('date')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable()->constrained('employees', 'id');     // Admin
            $table->unsignedBigInteger('team_id')->nullable()->constrained('employees', 'id');    // Team
            $table->text('admin_notes')->nullable();
            $table->text('team_notes')->nullable();
            $table->string('status')->default('ITF_REQUEST');       // process, in_transit, delivered, completed, cancelled

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
        Schema::dropIfExists('inventory_transfers');
    }
};
