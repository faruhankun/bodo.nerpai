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
        Schema::create('relations', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable()->unique();

            // morph
            $table->string('model1_type')->nullable();
            $table->unsignedBigInteger('model1_id')->nullable();
            $table->integer('model1_quantity')->nullable();
            
            $table->string('model2_type')->nullable();     
            $table->unsignedBigInteger('model2_id')->nullable();
            $table->integer('model2_quantity')->nullable();

            // Attributes
            $table->string('name')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('relations');
    }
};
