<?php

namespace Database\Seeders\Primary;

use App\Models\Space\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $person1 = Person::create([
            'name' => 'Person Anjing',
            'birth_date' => '2000-01-01',
            'gender' => 'male',
            'email' => 'anjing@gmail.com',
            'address' => json_encode([
                'street'=>  'Jl. Person Ajing',
                'city'=> 'Contoh Kota',
                'province'=> 'Contoh Provinsi',
                'country'=> 'Contoh Negara',
                'postal_code'=> '12345',
            ]),
            'phone_number' => '08123456789',
            'status' => 'Active',
            'notes' => 'Seeder',
        ]);

        $person2 = Person::create([
            'name' => 'Person Babi',
            'birth_date' => '1995-01-01',
            'gender' => 'female',
            'email' => 'baby@gmail.com',
            'address' => json_encode([
                'street'=>  'Jl. Person Baby',
                'city'=> 'Contoh Kota',
                'province'=> 'Contoh Provinsi',
                'country'=> 'Contoh Negara',
                'postal_code'=> '12345',
            ]),
            'phone_number' => '081298763543',
            'status' => 'Active',
            'notes' => 'Seeder',
        ]);

        $person3 = Person::create([
            'name' => 'Person Cat',
            'birth_date' => '1990-01-01',
            'gender' => 'female',
            'email' => 'cat@gmail.com',
            'address' => json_encode([
                'street'=>  'Jl. Person Cat',
                'city'=> 'Contoh Kota',
                'province'=> 'Contoh Provinsi',
                'country'=> 'Contoh Negara',
                'postal_code'=> '12345',
            ]),
            'phone_number' => '081295663543',
            'status' => 'Active',
            'notes' => 'Seeder',
        ]);
    }
}
