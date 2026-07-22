<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LandlordUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'landlord@patanyumba.com'],
            [
                'name' => 'Joseph Mwakyusa',
                'email' => 'landlord@patanyumba.com',
                'phone' => '+255712345678',
                'password' => Hash::make('password'),
                'role' => 'landlord',
                'kyc_status' => 'approved',
                'verification_level' => 'full',
                'business_name' => 'Mwakyusa Properties Ltd',
                'address' => 'Mlimani City, Sam Nujoma Road',
                'region' => 'Dar es Salaam',
                'district' => 'Kinondoni',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'agent@patanyumba.com'],
            [
                'name' => 'Grace Massawe',
                'email' => 'agent@patanyumba.com',
                'phone' => '+255755987654',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'kyc_status' => 'approved',
                'verification_level' => 'full',
                'business_name' => 'Massawe Real Estate Agency',
                'address' => 'Mbezi Beach, Dar es Salaam',
                'region' => 'Dar es Salaam',
                'district' => 'Ubungo',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
    }
}
