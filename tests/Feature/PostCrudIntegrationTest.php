<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostCrudIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $editor;
    protected $author;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        $permissions = [
            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts',
            'manage_posts', 'manage_categories', 'manage_media', 'manage_users', 'manage_settings'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Create roles
        $adminRole = Role::create(['name' => 'Admin']);
        $editorRole = Role::create(['name' => 'Editor']);
        $authorRole = Role::create(['name' => 'Author']);
        
        // Assign permissions to roles
        $adminRole->givePermissionTo($permissions);
        $editorRole->givePermissionTo(['view posts', 'create posts', 'edit posts', 'publish posts', 'manage_posts']);
        $authorRole->givePermissionTo(['view posts', 'create posts', 'edit posts']);
        
        // Create users
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'name' => 'Admin User'
        ]);
        $this->admin->assignRole('Admin');
        
        $this->editor = User::factory()->create([
            'email' => 'editor@test.com',
            'name' => 'Editor User'
        ]);
        $this->editor->assignRole('Editor');
        
        $this->author = User::factory()->create([
            'email' => 'author@test.com',
            'name' => 'Author User'
        ]);
        $this->author->assignRole('Author');
        
        // Create category
        $this->category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
            'is_active' => true
        ]);
        
        Storage::fake('public');
    }

    public function test_guest_cannot_access_admin_posts()
    {
        $response = $this->get('/admin/posts');
        $response->assertRedirect('/login');
    }

    public function test_admin_login_and_access_posts()
    {
        // Test login
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password'
        ]);
        
        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->admin);
        
        // Test access to posts index
        $response = $this->actingAs($this->admin)->get('/admin/posts');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_post()
    {
        $this->actingAs($this->admin);
        
        // Test create form
        $response = $this->get('/admin/posts/create');
        $response->assertStatus(200);
        
        // Test store post
        $postData = [
            'title' => 'Test Post Title',
            'content' => 'This is test post content',
            'excerpt' => 'Test excerpt',
            'category_id' => $this->category->id,
            'type' => 'berita',
            'is_published' => true,
            'is_featured' => false,
            'is_slider' => false,
            'meta_title' => 'Test Meta Title',
            'meta_description' => 'Test meta description'
        ];
        
        $response = $this->post('/admin/posts', $postData);
        $response->assertRedirect();
        
        // Verify post was created
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'slug' => 'test-post-title',
            'user_id' => $this->admin->id,
            'category_id' => $this->category->id
        ]);
    }

    public function test_admin_can_edit_post()
    {
        $this->actingAs($this->admin);
        
        // Create a post first
        $post = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'excerpt' => 'Original excerpt',
            'user_id' => $this->admin->id,
            'category_id' => $this->category->id,
            'type' => 'berita',
            'is_published' => false
        ]);
        
        // Test edit form
        $response = $this->get("/admin/posts/{$post->id}/edit");
        $response->assertStatus(200);
        
        // Test update post
        $updateData = [
            'title' => 'Updated Title',
            'slug' => '', // Empty slug to trigger auto-generation
            'content' => 'Updated content',
            'excerpt' => 'Updated excerpt',
            'category_id' => $this->category->id,
            'type' => 'berita',
            'is_published' => true,
            'is_featured' => true,
            'is_slider' => false,
            'meta_title' => 'Updated Meta Title',
            'meta_description' => 'Updated meta description'
        ];
        
        $response = $this->put("/admin/posts/{$post->id}", $updateData);
        $response->assertRedirect();
        
        // Verify post was updated
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'is_published' => true,
            'is_featured' => true
        ]);
    }

    public function test_admin_can_delete_post()
    {
        $this->actingAs($this->admin);
        
        // Create a post first
        $post = Post::create([
            'title' => 'Post to Delete',
            'slug' => 'post-to-delete',
            'content' => 'Content to delete',
            'excerpt' => 'Excerpt to delete',
            'user_id' => $this->admin->id,
            'category_id' => $this->category->id,
            'type' => 'berita',
            'is_published' => false
        ]);
        
        // Test delete post
        $response = $this->delete("/admin/posts/{$post->id}");
        $response->assertRedirect();
        
        // Verify post was deleted
        $this->assertDatabaseMissing('posts', [
            'id' => $post->id
        ]);
    }

    public function test_editor_can_manage_posts()
    {
        $this->actingAs($this->editor);
        
        // Test access to posts
        $response = $this->get('/admin/posts');
        $response->assertStatus(200);
        
        // Test create post
        $postData = [
            'title' => 'Editor Post',
            'content' => 'Editor post content',
            'excerpt' => 'Editor excerpt',
            'category_id' => $this->category->id,
            'type' => 'berita',
            'is_published' => true,
            'is_featured' => false,
            'is_slider' => false
        ];
        
        $response = $this->post('/admin/posts', $postData);
        $response->assertRedirect();
        
        $this->assertDatabaseHas('posts', [
            'title' => 'Editor Post',
            'user_id' => $this->editor->id
        ]);
    }

    public function test_author_has_limited_access()
    {
        $this->actingAs($this->author);
        
        // Test access to posts
        $response = $this->get('/admin/posts');
        $response->assertStatus(200);
        
        // Test create post
        $postData = [
            'title' => 'Author Post',
            'content' => 'Author post content',
            'excerpt' => 'Author excerpt',
            'category_id' => $this->category->id,
            'type' => 'berita',
            'is_published' => false,
            'is_featured' => false,
            'is_slider' => false
        ];
        
        $response = $this->post('/admin/posts', $postData);
        $response->assertRedirect();
        
        $this->assertDatabaseHas('posts', [
            'title' => 'Author Post',
            'user_id' => $this->author->id,
            'is_published' => false
        ]);
    }

    public function test_post_toggle_features()
    {
        $this->actingAs($this->admin);
        
        $post = Post::create([
            'title' => 'Toggle Test Post',
            'slug' => 'toggle-test-post',
            'content' => 'Toggle test content',
            'excerpt' => 'Toggle test excerpt',
            'user_id' => $this->admin->id,
            'category_id' => $this->category->id,
            'type' => 'berita',
            'is_published' => false,
            'is_featured' => false,
            'is_slider' => false
        ]);
        
        // Test toggle publish
        $response = $this->patch("/admin/posts/{$post->id}/toggle-publish");
        $response->assertRedirect();
        
        $post->refresh();
        $this->assertTrue($post->is_published);
        
        // Test toggle featured
        $response = $this->patch("/admin/posts/{$post->id}/toggle-featured");
        $response->assertRedirect();
        
        $post->refresh();
        $this->assertTrue($post->is_featured);
        
        // Test toggle slider
        $response = $this->patch("/admin/posts/{$post->id}/toggle-slider");
        $response->assertRedirect();
        
        $post->refresh();
        $this->assertTrue($post->is_slider);
    }

    public function test_post_validation_errors()
    {
        $this->actingAs($this->admin);
        
        // Test validation with empty data
        $response = $this->post('/admin/posts', []);
        $response->assertSessionHasErrors(['title', 'content', 'category_id']);
        
        // Test validation with invalid category
        $response = $this->post('/admin/posts', [
            'title' => 'Test Post',
            'content' => 'Test content',
            'category_id' => 999 // Non-existent category
        ]);
        $response->assertSessionHasErrors(['category_id']);
    }

    public function test_logout_functionality()
    {
        $this->actingAs($this->admin);
        
        // Verify user is authenticated
        $this->assertAuthenticated();
        
        // Test logout
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        
        // Verify user is no longer authenticated
        $this->assertGuest();
        
        // Verify cannot access admin area after logout
        $response = $this->get('/admin/posts');
        $response->assertRedirect('/login');
    }

    public function test_complete_workflow_admin()
    {
        // 1. Login as admin
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password'
        ]);
        $response->assertRedirect('/admin');
        
        // 2. Access dashboard
        $response = $this->get('/admin');
        $response->assertStatus(200);
        
        // 3. Create post
        $postData = [
            'title' => 'Complete Workflow Post',
            'content' => 'This is a complete workflow test',
            'excerpt' => 'Workflow excerpt',
            'category_id' => $this->category->id,
            'type' => 'berita',
            'is_published' => false,
            'is_featured' => false,
            'is_slider' => false
        ];
        
        $response = $this->post('/admin/posts', $postData);
        $response->assertRedirect();
        
        $post = Post::where('title', 'Complete Workflow Post')->first();
        $this->assertNotNull($post);
        
        // 4. Edit post
        $updateData = [
            'title' => 'Updated Complete Workflow Post',
            'content' => 'Updated content for complete workflow',
            'excerpt' => 'Updated excerpt',
            'category_id' => $this->category->id,
            'type' => 'berita',
            'is_published' => true,
            'is_featured' => true,
            'is_slider' => true,
            'meta_title' => 'Updated Meta Title',
            'meta_description' => 'Updated meta description'
        ];
        
        $response = $this->put("/admin/posts/{$post->id}", $updateData);
        $response->assertRedirect();
        
        // 5. Verify changes
        $post->refresh();
        $this->assertEquals('Updated Complete Workflow Post', $post->title);
        $this->assertTrue($post->is_published);
        
        // 6. Delete post
        $response = $this->delete("/admin/posts/{$post->id}");
        $response->assertRedirect();
        
        // 7. Verify deletion
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        
        // 8. Logout
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}