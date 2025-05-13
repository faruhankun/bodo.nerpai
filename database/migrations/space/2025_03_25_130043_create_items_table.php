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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('primary_code')->nullable()->unique();
            $table->string('code')->nullable()->unique();
            $table->string('sku')->nullable()->unique();

            // morph
            $table->string('parent_type')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            
            $table->string('type_type')->nullable();     
            $table->unsignedBigInteger('type_id')->nullable();

            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            

            // Attributes
            $table->string('name');
            $table->decimal('price', 20, 2)->default(0);
            $table->decimal('cost', 20, 2)->default(0);

            $table->decimal('weight', 10, 2)->default(0);
            $table->json('dimension')->nullable();

            $table->string('status')->default('active');
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
        Schema::dropIfExists('items');
    }
};
