<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'players sidebar',
            'persons sidebar',
            'companies sidebar',
            'users sidebar',
            'roles sidebar',
            'permissions sidebar',
            
            'crud roles',
            
            'crud user',
            
            'companies',
            'persons',
            'players',
            'users',
            'roles',
            'permissions',

            'crud company',

            'crud permissions',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and their permissions
        $roles = [
            'Guest' => [

            ],
            'User' => [
                'companies sidebar',

                'companies',
                
                'crud company',
            ],
            'Admin' => [
                'players sidebar',
                'persons sidebar',
                'companies sidebar',
                'users sidebar',
                'roles sidebar',
                'permissions sidebar',
                
                'crud roles',
                
                'crud user',
                
                'companies',
                'persons',
                'players',
                'users',
                'roles',
                'permissions',

                'crud company',

                'crud permissions',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
