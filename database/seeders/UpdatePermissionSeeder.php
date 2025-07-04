<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UpdatePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create new permissions
        $newPermissions = [
            'manage_posts',
            'manage_categories', 
            'manage_media',
            'manage_users',
            'manage_settings',
        ];

        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get existing roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $editorRole = Role::where('name', 'Editor')->first();

        // Assign new permissions to roles
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($newPermissions);
        }

        if ($adminRole) {
            $adminRole->givePermissionTo($newPermissions);
        }

        if ($editorRole) {
            $editorRole->givePermissionTo([
                'manage_posts',
                'manage_categories',
                'manage_media'
            ]);
        }

        $this->command->info('New permissions created and assigned successfully!');
    }
}