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
        Schema::table('model_has_roles', function (Blueprint $table) {
            // $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unique(['model_id', 'model_type', 'role_id', 'team_id'], 'model_role_team_unique');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            // $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unique(['model_id', 'model_type', 'permission_id', 'team_id'], 'model_permission_team_unique');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unique(['name', 'team_id'], 'role_team_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropUnique('model_role_team_unique');
            $table->dropColumn('team_id');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropUnique('model_permission_team_unique');
            $table->dropColumn('team_id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('role_team_unique');
            $table->dropColumn('team_id');
        });
    }
};
