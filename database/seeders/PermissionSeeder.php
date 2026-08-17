<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar semua permissions dasar yang ada di sistem
        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'campaigns.view',
            'campaigns.create',
            'campaigns.edit',
            'campaigns.delete',
            'operasional-konten.view',
            'operasional-konten.create',
            'operasional-konten.delete',
            'master-data.view',
            'laporan.view',
            'profile.edit',
            'update-saw.view',
            'update-saw.process',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Berikan semua akses penuh ke Admin Master
        $adminMaster = Role::firstOrCreate(['name' => 'Admin Master']);
        $adminMaster->syncPermissions(Permission::all());
        
        // Berikan sebagian akses ke Admin
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([
            'dashboard.view',
            'campaigns.view',
            'operasional-konten.view',
            'operasional-konten.create',
            'laporan.view',
            'profile.edit'
        ]);

        // Berikan akses minim ke Client
        $client = Role::firstOrCreate(['name' => 'Client']);
        $client->syncPermissions([
            'dashboard.view',
            'campaigns.view',
            'laporan.view',
            'profile.edit'
        ]);
    }
}
