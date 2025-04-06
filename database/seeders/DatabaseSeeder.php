<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Seeders\Space\SpaceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,

            PrimarySeeder::class,

            SpaceSeeder::class,
            //BackupUsersSeeder::class,
        ]);
    }
}
