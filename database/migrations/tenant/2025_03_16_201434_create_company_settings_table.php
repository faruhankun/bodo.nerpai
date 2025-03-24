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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            
            // foreign keys
            $table->string('source_type')->nullable();                  // null, ST, WH
            $table->unsignedBigInteger('source_id')->nullable();
            
            // attributes
            $table->string('module')->nullable();
            $table->text('value')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // unique
            $table->unique(['source_type', 'source_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
