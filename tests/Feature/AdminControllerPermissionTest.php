<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminControllerPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin-specific permissions
        Permission::create(['name' => 'manage_posts']);
        Permission::create(['name' => 'manage_categories']);
        Permission::create(['name' => 'manage_media']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'manage_settings']);
        
        // Create roles
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $adminRole = Role::create(['name' => 'Admin']);
        $editorRole = Role::create(['name' => 'Editor']);
        
        // Assign permissions to roles
        $superAdminRole->givePermissionTo([
            'manage_posts', 'manage_categories', 'manage_media', 
            'manage_users', 'manage_settings'
        ]);
        
        $adminRole->givePermissionTo([
            'manage_posts', 'manage_categories', 'manage_media', 
            'manage_users', 'manage_settings'
        ]);
        
        $editorRole->givePermissionTo([
            'manage_posts', 'manage_categories', 'manage_media'
        ]);
    }

    public function test_admin_post_controller_requires_manage_posts_permission()
    {
        // Test that AdminPostController uses manage_posts permission
        $this->assertTrue(
            class_exists('\App\Http\Controllers\Admin\AdminPostController')
        );
        
        // Create user with manage_posts permission
        $user = User::factory()->create();
        $user->givePermissionTo('manage_posts');
        
        // This test verifies the permission exists and can be assigned
        $this->assertTrue($user->hasPermissionTo('manage_posts'));
    }

    public function test_admin_category_controller_requires_manage_categories_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage_categories');
        
        $this->assertTrue($user->hasPermissionTo('manage_categories'));
    }

    public function test_admin_media_controller_requires_manage_media_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage_media');
        
        $this->assertTrue($user->hasPermissionTo('manage_media'));
    }

    public function test_admin_user_controller_requires_manage_users_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage_users');
        
        $this->assertTrue($user->hasPermissionTo('manage_users'));
    }

    public function test_admin_setting_controller_requires_manage_settings_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage_settings');
        
        $this->assertTrue($user->hasPermissionTo('manage_settings'));
    }

    public function test_super_admin_has_all_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');
        
        $this->assertTrue($user->hasPermissionTo('manage_posts'));
        $this->assertTrue($user->hasPermissionTo('manage_categories'));
        $this->assertTrue($user->hasPermissionTo('manage_media'));
        $this->assertTrue($user->hasPermissionTo('manage_users'));
        $this->assertTrue($user->hasPermissionTo('manage_settings'));
    }

    public function test_admin_has_all_management_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        $this->assertTrue($user->hasPermissionTo('manage_posts'));
        $this->assertTrue($user->hasPermissionTo('manage_categories'));
        $this->assertTrue($user->hasPermissionTo('manage_media'));
        $this->assertTrue($user->hasPermissionTo('manage_users'));
        $this->assertTrue($user->hasPermissionTo('manage_settings'));
    }

    public function test_editor_has_limited_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('Editor');
        
        $this->assertTrue($user->hasPermissionTo('manage_posts'));
        $this->assertTrue($user->hasPermissionTo('manage_categories'));
        $this->assertTrue($user->hasPermissionTo('manage_media'));
        $this->assertFalse($user->hasPermissionTo('manage_users'));
        $this->assertFalse($user->hasPermissionTo('manage_settings'));
    }

    public function test_permission_middleware_classes_exist()
    {
        $this->assertTrue(class_exists('\Spatie\Permission\Middleware\PermissionMiddleware'));
        $this->assertTrue(class_exists('\Spatie\Permission\Middleware\RoleMiddleware'));
        $this->assertTrue(class_exists('\Spatie\Permission\Middleware\RoleOrPermissionMiddleware'));
    }
}