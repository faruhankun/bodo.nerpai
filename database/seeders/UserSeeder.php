<?php

namespace Database\Seeders;

use App\Models\Primary\Player;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Owner',
            'email' => 'owner@gmail.com',
            'role_id' => '3',
            'password' => Hash::make('owner123'),
        ]);

        $player = Player::create([
            'name' => $user->name,
            'address' => json_encode([
                'street' =>  'Contoh Jalan',
                'city' => 'Contoh Kota',
                'province' => 'Contoh Provinsi',
                'country' => 'Contoh Negara',
                'postal_code' => '12345',
            ]),
            'status' => 'Active',
            'notes' => 'Akun Utama',
        ]);

        $user->player_id = $player->id;
        $user->markEmailAsVerified();
        $user->save();

        $role = Role::findOrFail(3);
        $user->syncRoles($role);
    }
}
