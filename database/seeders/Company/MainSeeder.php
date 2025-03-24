<?php

namespace Database\Seeders\Company;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CompanySettingSeeder::class,

            CourierSeeder::class,

            AssetTypeSeeder::class,
            AssetLocationSeeder::class,

            AccountSeeder::class,
        ]);
    }
}
