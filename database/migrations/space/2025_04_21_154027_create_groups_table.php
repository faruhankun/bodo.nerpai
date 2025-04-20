<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            
            $table->string('name');
            $table->json('address')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();

            $table->timestamps(); // Otomatis buat created_at & updated_at
            $table->softDeletes(); // Untuk deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};

