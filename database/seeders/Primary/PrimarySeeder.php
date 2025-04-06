<?php
 
 namespace Database\Seeders\Primary;
 
 use Illuminate\Database\Console\Seeds\WithoutModelEvents;
 use Illuminate\Database\Seeder;
 
 class PrimarySeeder extends Seeder
 {
     /**
      * Run the database seeds.
      */
     public function run(): void
     {
         $this->call([
             PersonSeeder::class,
         ]);
     }
 }