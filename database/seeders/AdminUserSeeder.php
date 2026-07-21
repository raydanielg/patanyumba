<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@patanyumba.com'],
            [
                'name' => 'System Admin',
                'email' => 'admin@patanyumba.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'kyc_status' => 'approved',
                'verification_level' => 'full',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
