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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable()->unique();
            
            $table->string('name');
            $table->string('full_name')->nullable();

            $table->json('address')->nullable();
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('regency')->nullable();
            $table->string('district')->nullable();
            $table->string('sub_district')->nullable();
            $table->string('village')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('address_detail')->nullable();

            $table->date('birth_date')->nullable();
            $table->date('death_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('id_card_number')->nullable();

            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
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
        Schema::dropIfExists('persons');
    }
};
