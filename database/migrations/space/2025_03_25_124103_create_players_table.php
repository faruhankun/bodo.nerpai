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


            // address
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('regency')->nullable();
            $table->string('district')->nullable();
            $table->string('sub_district')->nullable();
            $table->string('village')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('address_detail')->nullable();


            // contact
            $table->string('shopee_username')->nullable();
            $table->string('tokopedia_username')->nullable();
            $table->string('whatsapp_number')->nullable();


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
