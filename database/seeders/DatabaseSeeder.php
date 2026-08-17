<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
// use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run PermissionSeeder first
        $this->call(PermissionSeeder::class);

        $roleMaster = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin Master']);
        $roleAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $roleClient = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Client']);

        $userMaster = User::firstOrCreate(['email' => 'admin@master.com'], [
            'name' => 'Super Admin',
            'username' => 'adminmaster',
            'password' => Hash::make('password'),
            'role' => 'Admin Master',
            'status' => 'Aktif',
        ]);
        $userMaster->assignRole($roleMaster);

        $userAdmin = User::firstOrCreate(['email' => 'admin@admin.com'], [
            'name' => 'Admin',
            'username' => 'admin123',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'status' => 'Aktif',
        ]);
        $userAdmin->assignRole($roleAdmin);

        $userClient = User::firstOrCreate(['email' => 'client@client.com'], [
            'name' => 'Client Satu',
            'username' => 'client123',
            'password' => Hash::make('password'),
            'role' => 'Client',
            'status' => 'Aktif',
        ]);
        $userClient->assignRole($roleClient);
    }
}
