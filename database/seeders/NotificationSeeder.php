<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $notifications = [
                [
                    'user_id' => $user->id,
                    'title' => 'Welcome to PataNyumba',
                    'body' => 'Thank you for joining PataNyumba! Start exploring properties today.',
                    'type' => 'info',
                    'is_read' => false,
                    'created_at' => now()->subDays(5),
                ],
                [
                    'user_id' => $user->id,
                    'title' => 'New Property Available',
                    'body' => 'A new property matching your interests has been listed in your area.',
                    'type' => 'success',
                    'link' => '/properties',
                    'is_read' => false,
                    'created_at' => now()->subDays(3),
                ],
                [
                    'user_id' => $user->id,
                    'title' => 'Profile Update Reminder',
                    'body' => 'Please complete your profile to get the best experience on PataNyumba.',
                    'type' => 'warning',
                    'is_read' => true,
                    'read_at' => now()->subDays(2),
                    'created_at' => now()->subDays(4),
                ],
                [
                    'user_id' => $user->id,
                    'title' => 'KYC Verification Required',
                    'body' => 'Submit your KYC documents to verify your identity and unlock all features.',
                    'type' => 'danger',
                    'link' => '/kyc',
                    'is_read' => false,
                    'created_at' => now()->subDays(1),
                ],
                [
                    'user_id' => $user->id,
                    'title' => 'Price Drop Alert',
                    'body' => 'A property you viewed recently has had a price reduction. Check it out!',
                    'type' => 'info',
                    'is_read' => true,
                    'read_at' => now()->subHours(12),
                    'created_at' => now()->subDays(2),
                ],
                [
                    'user_id' => $user->id,
                    'title' => 'Subscription Expiring Soon',
                    'body' => 'Your subscription plan will expire in 3 days. Renew to keep your benefits.',
                    'type' => 'warning',
                    'is_read' => false,
                    'created_at' => now()->subHours(6),
                ],
            ];

            foreach ($notifications as $notif) {
                Notification::create($notif);
            }
        }
    }
}
