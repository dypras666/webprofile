<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing users first to avoid conflicts
        User::whereIn('email', [
            'admin@webprofile.com',
            'admin@iimsabak.ac.id', 
            'editor@iimsabak.ac.id'
        ])->delete();

        // Create Super Admin user
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@webprofile.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'bio' => 'Super Administrator dengan akses penuh ke sistem',
            'phone' => '+62812345678',
        ]);
        
        // Assign Super Admin role if exists
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Create Admin user
        $admin = User::create([
            'name' => 'Admin LPM IIMS',
            'email' => 'admin@iimsabak.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'bio' => 'Administrator Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak',
            'phone' => '+62282113175',
        ]);
        
        // Assign Admin role if exists
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        // Create Editor user
        $editor = User::create([
            'name' => 'Editor LPM IIMS',
            'email' => 'editor@iimsabak.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'bio' => 'Editor konten website LPM Institut Islam Al-Mujaddid Sabak',
            'phone' => '+62282113176',
        ]);
        
        // Assign Editor role if exists
        $editorRole = Role::where('name', 'Editor')->first();
        if ($editorRole) {
            $editor->assignRole($editorRole);
        }

        $this->command->info('Admin users created successfully!');
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('Super Admin:');
        $this->command->info('  Email: admin@webprofile.com');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Admin LPM IIMS:');
        $this->command->info('  Email: admin@iimsabak.ac.id');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Editor LPM IIMS:');
        $this->command->info('  Email: editor@iimsabak.ac.id');
        $this->command->info('  Password: password');
        $this->command->info('========================');
    }
}