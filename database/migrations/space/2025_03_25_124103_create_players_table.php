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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();

            // morph
            $table->string('type_type')->nullable();     
            $table->unsignedBigInteger('type_id')->nullable();

            $table->string('size_type')->nullable();     // Group, Person
            $table->unsignedBigInteger('size_id')->nullable();

            $table->string('space_type')->nullable();
            $table->unsignedBigInteger('space_id')->nullable();

            // Attributes
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
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
        Schema::dropIfExists('players');
    }
};
