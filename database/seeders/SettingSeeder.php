<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'app_name', 'value' => 'PataNyumba', 'group' => 'general'],
            ['key' => 'app_tagline', 'value' => 'Find Your Dream Home in Tanzania', 'group' => 'general'],
            ['key' => 'app_description', 'value' => 'PataNyumba is the leading property platform in Tanzania, connecting house hunters with agents and property owners.', 'group' => 'general'],
            ['key' => 'support_email', 'value' => 'support@patanyumba.co.tz', 'group' => 'general'],
            ['key' => 'support_phone', 'value' => '+255 700 000 000', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'Africa/Dar_es_Salaam', 'group' => 'general'],
            ['key' => 'default_currency', 'value' => 'TZS', 'group' => 'general'],

            // Maintenance
            ['key' => 'maintenance_mode', 'value' => 'false', 'group' => 'maintenance'],
            ['key' => 'maintenance_message', 'value' => 'We are performing scheduled maintenance. We will be back shortly.', 'group' => 'maintenance'],
            ['key' => 'maintenance_start', 'value' => '', 'group' => 'maintenance'],
            ['key' => 'maintenance_end', 'value' => '', 'group' => 'maintenance'],

            // KYC
            ['key' => 'kyc_verification_enabled', 'value' => 'true', 'group' => 'kyc'],
            ['key' => 'kyc_required_for_listing', 'value' => 'true', 'group' => 'kyc'],
            ['key' => 'kyc_required_for_contact', 'value' => 'false', 'group' => 'kyc'],
            ['key' => 'kyc_auto_approve', 'value' => 'false', 'group' => 'kyc'],
            ['key' => 'kyc_expiry_days', 'value' => '365', 'group' => 'kyc'],

            // Features
            ['key' => 'property_listing_enabled', 'value' => 'true', 'group' => 'features'],
            ['key' => 'featured_listings_enabled', 'value' => 'true', 'group' => 'features'],
            ['key' => 'sponsored_listings_enabled', 'value' => 'true', 'group' => 'features'],
            ['key' => 'property_unlock_enabled', 'value' => 'true', 'group' => 'features'],
            ['key' => 'subscriptions_enabled', 'value' => 'true', 'group' => 'features'],
            ['key' => 'user_registration_enabled', 'value' => 'true', 'group' => 'features'],
            ['key' => 'agent_registration_enabled', 'value' => 'true', 'group' => 'features'],
            ['key' => 'property_approval_required', 'value' => 'true', 'group' => 'features'],
            ['key' => 'max_properties_per_agent', 'value' => '50', 'group' => 'features'],
            ['key' => 'max_images_per_property', 'value' => '20', 'group' => 'features'],

            // Hero Images
            ['key' => 'hero_enabled', 'value' => 'true', 'group' => 'hero'],
            ['key' => 'hero_image_1', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_title_1', 'value' => 'Find Your Dream Home', 'group' => 'hero'],
            ['key' => 'hero_subtitle_1', 'value' => 'Browse thousands of properties across Tanzania', 'group' => 'hero'],
            ['key' => 'hero_button_text_1', 'value' => 'Search Now', 'group' => 'hero'],
            ['key' => 'hero_button_link_1', 'value' => '/search', 'group' => 'hero'],
            ['key' => 'hero_image_2', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_title_2', 'value' => 'List Your Property', 'group' => 'hero'],
            ['key' => 'hero_subtitle_2', 'value' => 'Reach thousands of potential buyers and tenants', 'group' => 'hero'],
            ['key' => 'hero_button_text_2', 'value' => 'List Now', 'group' => 'hero'],
            ['key' => 'hero_button_link_2', 'value' => '/list', 'group' => 'hero'],
            ['key' => 'hero_image_3', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_title_3', 'value' => 'Verified Agents', 'group' => 'hero'],
            ['key' => 'hero_subtitle_3', 'value' => 'Connect with trusted and verified real estate agents', 'group' => 'hero'],
            ['key' => 'hero_button_text_3', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_button_link_3', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_image_4', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_title_4', 'value' => 'Move In Faster', 'group' => 'hero'],
            ['key' => 'hero_subtitle_4', 'value' => 'Connect directly with landlords', 'group' => 'hero'],
            ['key' => 'hero_button_text_4', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_button_link_4', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_image_5', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_title_5', 'value' => 'Your Dream Home Awaits', 'group' => 'hero'],
            ['key' => 'hero_subtitle_5', 'value' => 'Start your search today', 'group' => 'hero'],
            ['key' => 'hero_button_text_5', 'value' => '', 'group' => 'hero'],
            ['key' => 'hero_button_link_5', 'value' => '', 'group' => 'hero'],

            // Announcements
            ['key' => 'announcement_enabled', 'value' => 'false', 'group' => 'announcements'],
            ['key' => 'announcement_text', 'value' => 'Welcome to PataNyumba! New features available now.', 'group' => 'announcements'],
            ['key' => 'announcement_type', 'value' => 'info', 'group' => 'announcements'],
            ['key' => 'announcement_link', 'value' => '', 'group' => 'announcements'],
            ['key' => 'announcement_link_text', 'value' => 'Learn More', 'group' => 'announcements'],

            // Notifications
            ['key' => 'notify_new_registration', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notify_new_property', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notify_kyc_submission', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notify_payment_received', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notify_property_report', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notify_subscription_expiry', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'email_notifications_enabled', 'value' => 'true', 'group' => 'notifications'],

            // Payment
            ['key' => 'mpesa_enabled', 'value' => 'true', 'group' => 'payment'],
            ['key' => 'airtel_money_enabled', 'value' => 'true', 'group' => 'payment'],
            ['key' => 'halopesa_enabled', 'value' => 'false', 'group' => 'payment'],
            ['key' => 'tpesa_enabled', 'value' => 'false', 'group' => 'payment'],
            ['key' => 'mixx_yas_enabled', 'value' => 'false', 'group' => 'payment'],
            ['key' => 'card_payment_enabled', 'value' => 'false', 'group' => 'payment'],
            ['key' => 'cash_payment_enabled', 'value' => 'true', 'group' => 'payment'],
            ['key' => 'unlock_fee', 'value' => '1000', 'group' => 'payment'],
            ['key' => 'featured_fee', 'value' => '15000', 'group' => 'payment'],
            ['key' => 'sponsored_fee', 'value' => '25000', 'group' => 'payment'],

            // SEO
            ['key' => 'meta_keywords', 'value' => 'property, real estate, Tanzania, houses, apartments, land, rent, buy', 'group' => 'seo'],
            ['key' => 'meta_description', 'value' => 'Find properties for sale and rent across Tanzania. Connect with verified agents.', 'group' => 'seo'],
            ['key' => 'google_analytics_id', 'value' => '', 'group' => 'seo'],
            ['key' => 'facebook_pixel_id', 'value' => '', 'group' => 'seo'],

            // Social
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/patanyumba', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/patanyumba', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/patanyumba', 'group' => 'social'],
            ['key' => 'social_whatsapp', 'value' => '+255 700 000 000', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => '', 'group' => 'social'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
