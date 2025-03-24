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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable()->unique();
            $table->string('type')->default('operational');         // operational, logistics, marketing, payroll, maintenance, rental, utility, other

            // forign keys
            $table->foreignId('account_id')->nullable()->constrained('accounts', 'id');
            $table->foreignId('requested_by')->constrained('employees', 'id');
            $table->foreignId('approved_by')->nullable()->constrained('employees', 'id');
            $table->string('consignee_type')->nullable();                   // 'WH', 'SUP', 'CUST'
            $table->unsignedBigInteger('consignee_id')->nullable();

            // columns
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 20, 2)->default(0);
            $table->string('payment_method')->nullable()->default('CASH');
            $table->string('status')->default('requested');                 // requested, approved, rejected, paid
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
