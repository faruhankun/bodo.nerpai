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
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();

            // morph
            $table->string('parent_type')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            
            $table->string('type_type')->nullable();     
            $table->unsignedBigInteger('type_id')->nullable();

            // Attributes
            $table->string('name');
            $table->json('address')->nullable();
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
        Schema::dropIfExists('spaces');
    }
};
