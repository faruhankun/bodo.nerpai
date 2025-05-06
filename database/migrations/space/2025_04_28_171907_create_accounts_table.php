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
        Schema::create('accounts', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key
            $table->foreignId('parent_id')->nullable()->constrained('accounts', 'id');
            $table->foreignId('type_id')->nullable()->constrained('account_types', 'id');

            // Columns
            $table->string('code')->unique();
            $table->string('name');
            $table->string('status')->default('active');
            $table->decimal('balance', 25, 2)->default(0);
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
