<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder creating baseline Administrator and Viewer accounts.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Account
        User::firstOrCreate(
            ['email' => 'admin@jajalmedical.com'],
            [
                'name' => 'Dr. Alexander Wright (Admin)',
                'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'Admin@12345')),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Viewer Account
        User::firstOrCreate(
            ['email' => 'viewer@jajalmedical.com'],
            [
                'name' => 'Sarah Jenkins (Viewer)',
                'password' => Hash::make(env('SEED_VIEWER_PASSWORD', 'Viewer@12345')),
                'role' => 'viewer',
                'email_verified_at' => now(),
            ]
        );
    }
}
