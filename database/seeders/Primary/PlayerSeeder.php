<?php
 
 namespace Database\Seeders\Primary;
 
 use App\Models\Primary\Player;
 use Illuminate\Database\Seeder;
 
 class PlayerSeeder extends Seeder
 {
     /**
      * Run the database seeds.
      */
     public function run(): void
     {
         $person1 = Player::create([
             'name' => 'Player Anjing',
             'birth_date' => '2000-01-01',
             'gender' => 'male',
             'email' => 'anjing@gmail.com',
             'address' => json_encode([
                 'street'=>  'Jl. Player Ajing',
                 'city'=> 'Contoh Kota',
                 'province'=> 'Contoh Provinsi',
                 'country'=> 'Contoh Negara',
                 'postal_code'=> '12345',
             ]),
             'phone_number' => '08123456789',
             'status' => 'Active',
             'notes' => 'Seeder',
         ]);
 
         $person2 = Player::create([
             'name' => 'Player Babi',
             'birth_date' => '1995-01-01',
             'gender' => 'female',
             'email' => 'baby@gmail.com',
             'address' => json_encode([
                 'street'=>  'Jl. Player Baby',
                 'city'=> 'Contoh Kota',
                 'province'=> 'Contoh Provinsi',
                 'country'=> 'Contoh Negara',
                 'postal_code'=> '12345',
             ]),
             'phone_number' => '081298763543',
             'status' => 'Active',
             'notes' => 'Seeder',
         ]);
     }
 }