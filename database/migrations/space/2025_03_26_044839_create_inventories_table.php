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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('sku')->nullable();

            // morph
            $table->string('space_type')->nullable();
            $table->unsignedBigInteger('space_id')->nullable();

            $table->string('parent_type')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();

            $table->string('type_type')->nullable();     
            $table->unsignedBigInteger('type_id')->nullable();

            $table->string('location_type')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();

            $table->string('item_type')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();


            // Attributes
            $table->date('expiry_date')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('balance', 25, 2)->default(0);
            $table->decimal('cost_per_unit', 25, 2)->default(0);

            $table->string('name')->nullable();
            $table->string('status')->default('active');
            $table->string('notes')->nullable();

            
            $table->timestamps();
            $table->softDeletes();

            // unique space, code
            $table->unique(['code', 'space_type', 'space_id'], 'space_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
