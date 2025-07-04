<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Post permissions
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
            'manage_posts',
            
            // Category permissions
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'manage_categories',
            
            // Media permissions
            'view media',
            'upload media',
            'delete media',
            'manage_media',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage_users',
            
            // Site settings permissions
            'view site settings',
            'edit site settings',
            'manage_settings',
            
            // Role permissions
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permission permissions
            'view permissions',
            'assign permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $editorRole = Role::firstOrCreate(['name' => 'Editor']);
        $authorRole = Role::firstOrCreate(['name' => 'Author']);

        // Assign permissions to roles
        
        // Super Admin gets all permissions
        $superAdminRole->givePermissionTo(Permission::all());
        
        // Admin gets most permissions except role management
        $adminRole->givePermissionTo([
            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts', 'manage_posts',
            'view categories', 'create categories', 'edit categories', 'delete categories', 'manage_categories',
            'view media', 'upload media', 'delete media', 'manage_media',
            'view users', 'create users', 'edit users', 'manage_users',
            'view site settings', 'edit site settings', 'manage_settings',
        ]);
        
        // Editor gets content management permissions
        $editorRole->givePermissionTo([
            'view posts', 'create posts', 'edit posts', 'publish posts', 'manage_posts',
            'view categories', 'create categories', 'edit categories', 'manage_categories',
            'view media', 'upload media', 'manage_media',
            'view users',
        ]);
        
        // Author gets basic content creation permissions
        $authorRole->givePermissionTo([
            'view posts', 'create posts', 'edit posts',
            'view categories',
            'view media', 'upload media',
        ]);

        // Create Super Admin user
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@webprofile.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        
        $superAdmin->assignRole($superAdminRole);

        // Create sample Admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        
        $admin->assignRole($adminRole);

        // Create sample Editor user
        $editor = User::create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        
        $editor->assignRole($editorRole);

        // Create sample Author user
        $author = User::create([
            'name' => 'Author',
            'email' => 'author@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        
        $author->assignRole($authorRole);

        $this->command->info('Roles, permissions, and sample users created successfully!');
        $this->command->info('Super Admin: admin@webprofile.com / password');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Editor: editor@example.com / password');
        $this->command->info('Author: author@example.com / password');
    }
}
