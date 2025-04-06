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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable()->unique();
            $table->string('class')->nullable();

            // morph
            $table->string('type_type')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->string('input_type')->nullable();
            $table->unsignedBigInteger('input_id')->nullable();
            $table->string('output_type')->nullable();
            $table->unsignedBigInteger('output_id')->nullable();

            $table->string('sender_type')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('receiver_type')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->string('handler_type')->nullable();
            $table->unsignedBigInteger('handler_id')->nullable();


            // Attributes
            $table->json('input_address')->nullable();
            $table->json('output_address')->nullable();

            $table->date('sent_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('handler_number')->nullable();

            $table->decimal('total', 30, 2)->default(0);
            $table->decimal('fee', 20, 2)->default(0);
            $table->string('fee_rules')->nullable();

            $table->text('sender_notes')->nullable();
            $table->text('receiver_notes')->nullable();
            $table->text('handler_notes')->nullable();

            $table->string('status')->default('TX_REQUEST');

            $table->timestamps();
            $table->softDeletes();
        });

        // Details
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            
            // Morph
            $table->string('detail_type')->nullable();
            $table->unsignedBigInteger('detail_id')->nullable();

            // Attributes
            $table->json('data')->nullable();

            $table->decimal('quantity', 20, 2)->default(1);
            $table->decimal('price', 20, 2)->default(0);
            $table->decimal('cost_per_unit', 20, 2)->default(0);

            $table->decimal('debit', 25, 2)->default(0);
            $table->decimal('credit', 25, 2)->default(0);
            $table->string('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
        Schema::dropIfExists('transactions');
    }
};
