<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Keys
            $table->foreignId('company_user_id')->nullable()->constrained();
            $table->foreignId('role_id')->nullable()->constrained();
            
            // Columns
            $table->date('reg_date');
            $table->date('out_date')->nullable();
            $table->string('status')->default('active');    // active or inactive

            // Timestamps (optional, not in schema but commonly used)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
