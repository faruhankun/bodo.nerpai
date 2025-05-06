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
        Schema::create('variables', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();

            // morph
            $table->string('space_type')->nullable();
            $table->unsignedBigInteger('space_id')->nullable();

            $table->string('parent_type')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();

            $table->string('type_type')->nullable();     
            $table->unsignedBigInteger('type_id')->nullable();


            // Attributes
            $table->dateTime('expiry_time')->nullable();
            
            $table->string('name')->nullable();
            $table->string('value')->nullable();
            $table->string('status')->default('active');
            $table->string('notes')->nullable();

            $table->string('deletable')->default(0);
            
            $table->timestamps();
            $table->softDeletes();

            // unique space, code
            $table->unique(['key', 'space_type', 'space_id'], 'space_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variables');
    }
};
