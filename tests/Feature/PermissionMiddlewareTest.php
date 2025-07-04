<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'manage_posts']);
        Permission::create(['name' => 'view posts']);
        Permission::create(['name' => 'manage_users']);
        
        // Create roles
        $adminRole = Role::create(['name' => 'Admin']);
        $editorRole = Role::create(['name' => 'Editor']);
        
        // Assign permissions to roles
        $adminRole->givePermissionTo(['view posts', 'manage_users']);
        $editorRole->givePermissionTo(['view posts']);
    }

    public function test_permission_middleware_allows_access_with_correct_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view posts');
        
        $response = $this->actingAs($user)
            ->get('/admin/posts');
            
        $response->assertStatus(200);
    }

    public function test_permission_middleware_denies_access_without_permission()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/admin/posts');
            
        $response->assertStatus(403);
    }

    public function test_role_based_access_to_admin_posts()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('Admin');
        
        $response = $this->actingAs($adminUser)
            ->get('/admin/posts');
            
        $response->assertStatus(200);
    }

    public function test_editor_can_access_admin_posts()
    {
        $editorUser = User::factory()->create();
        $editorUser->assignRole('Editor');
        
        $response = $this->actingAs($editorUser)
            ->get('/admin/posts');
            
        $response->assertStatus(200);
    }

    public function test_user_without_role_cannot_access_admin_posts()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/admin/posts');
            
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_posts()
    {
        $response = $this->get('/admin/posts');
        
        $response->assertRedirect('/login');
    }

    public function test_permission_middleware_registration()
    {
        $this->assertTrue(class_exists('\Spatie\Permission\Middleware\PermissionMiddleware'));
        $this->assertTrue(class_exists('\Spatie\Permission\Middleware\RoleMiddleware'));
        $this->assertTrue(class_exists('\Spatie\Permission\Middleware\RoleOrPermissionMiddleware'));
    }

    public function test_spatie_permission_service_provider_loaded()
    {
        $providers = app()->getLoadedProviders();
        $this->assertArrayHasKey('Spatie\Permission\PermissionServiceProvider', $providers);
    }
}