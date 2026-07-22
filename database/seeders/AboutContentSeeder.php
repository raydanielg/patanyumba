<?php

namespace Database\Seeders;

use App\Models\AboutContent;
use Illuminate\Database\Seeder;

class AboutContentSeeder extends Seeder
{
    public function run(): void
    {
        // Main intro
        AboutContent::create([
            'section' => 'main',
            'title' => 'About PataNyumba',
            'content' => 'PataNyumba is Tanzania\'s leading property rental platform, designed to bridge the gap between tenants and landlords. Our mission is to make finding and listing properties simple, fast, and accessible to everyone across the country. Whether you\'re searching for a house, apartment, plot, or commercial space, PataNyumba connects you directly with property owners without intermediaries.',
            'icon' => 'home',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Mission
        AboutContent::create([
            'section' => 'mission',
            'title' => 'Our Mission',
            'content' => 'To empower Tanzanians with a transparent, efficient, and trustworthy platform for property rentals. We strive to eliminate the challenges of finding suitable housing by providing verified listings, direct communication with landlords, and a seamless rental experience from search to move-in.',
            'icon' => 'target',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // Vision
        AboutContent::create([
            'section' => 'vision',
            'title' => 'Our Vision',
            'content' => 'To become the most trusted and widely used property platform in East Africa, transforming how people find homes and how landlords connect with tenants. We envision a future where every Tanzanian can find their perfect home with just a few taps on their phone.',
            'icon' => 'eye',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // Values
        AboutContent::create([
            'section' => 'values',
            'title' => 'Transparency',
            'content' => 'We believe in honest, clear, and open communication. Every listing on PataNyumba is verified, and we provide all the information you need to make informed decisions.',
            'icon' => 'shield',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        AboutContent::create([
            'section' => 'values',
            'title' => 'Accessibility',
            'content' => 'We are committed to making property rentals accessible to all Tanzanians, regardless of location or background. Our app is designed to work smoothly even on low-end devices and slow internet connections.',
            'icon' => 'globe',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        AboutContent::create([
            'section' => 'values',
            'title' => 'Trust & Security',
            'content' => 'Through our KYC verification process, we ensure that both tenants and landlords are who they claim to be. Your safety and peace of mind are our top priorities.',
            'icon' => 'lock',
            'sort_order' => 6,
            'is_active' => true,
        ]);

        AboutContent::create([
            'section' => 'values',
            'title' => 'Innovation',
            'content' => 'We continuously innovate to improve the rental experience. From in-app calling to video tours and smart search filters, we leverage technology to make your journey effortless.',
            'icon' => 'lightbulb',
            'sort_order' => 7,
            'is_active' => true,
        ]);

        // Stats
        AboutContent::create([
            'section' => 'stats',
            'title' => 'Our Impact',
            'content' => 'Numbers that reflect our growing community and the trust placed in PataNyumba.',
            'icon' => 'chart',
            'stats' => [
                ['label' => 'Active Users', 'value' => '10,000+', 'icon' => 'users'],
                ['label' => 'Listed Properties', 'value' => '5,000+', 'icon' => 'home'],
                ['label' => 'Regions Covered', 'value' => '26', 'icon' => 'map'],
                ['label' => 'Successful Rentals', 'value' => '3,500+', 'icon' => 'check'],
            ],
            'sort_order' => 8,
            'is_active' => true,
        ]);

        // How it works
        AboutContent::create([
            'section' => 'how_it_works',
            'title' => 'How PataNyumba Works',
            'content' => 'PataNyumba makes property rental simple. For tenants: browse properties, filter by your preferences, contact landlords directly, and move in. For landlords: create an account, list your property with photos and videos, receive inquiries, and find the right tenant.',
            'icon' => 'info',
            'sort_order' => 9,
            'is_active' => true,
        ]);

        AboutContent::create([
            'section' => 'how_it_works',
            'title' => 'For Tenants',
            'content' => '1. Create a free account on PataNyumba.\n2. Search for properties by region, district, price, and type.\n3. Save your favorite properties for later.\n4. Unlock landlord contact details with a subscription.\n5. Call or message the landlord directly through the app.\n6. Visit the property and move in!',
            'icon' => 'search',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        AboutContent::create([
            'section' => 'how_it_works',
            'title' => 'For Landlords',
            'content' => '1. Register as a landlord on PataNyumba.\n2. Complete your KYC verification for trust.\n3. List your property with photos, videos, and details.\n4. Receive inquiries from interested tenants.\n5. Communicate directly through in-app calling.\n6. Find the perfect tenant for your property!',
            'icon' => 'building',
            'sort_order' => 11,
            'is_active' => true,
        ]);

        // Contact
        AboutContent::create([
            'section' => 'contact',
            'title' => 'Contact Us',
            'content' => 'We\'re here to help! Reach out to us through any of these channels and our team will respond promptly.',
            'icon' => 'mail',
            'sort_order' => 12,
            'is_active' => true,
        ]);

        AboutContent::create([
            'section' => 'contact',
            'title' => 'Email',
            'content' => 'support@patanyumba.co.tz\ninfo@patanyumba.co.tz',
            'icon' => 'email',
            'sort_order' => 13,
            'is_active' => true,
        ]);

        AboutContent::create([
            'section' => 'contact',
            'title' => 'Phone',
            'content' => '+255 712 345 678\n+255 754 987 654',
            'icon' => 'phone',
            'sort_order' => 14,
            'is_active' => true,
        ]);

        AboutContent::create([
            'section' => 'contact',
            'title' => 'Office',
            'content' => 'PataNyumba Headquarters\nPlot 45, Mlimani City Business Park\nSam Nujoma Road, Dar es Salaam\nTanzania',
            'icon' => 'location',
            'sort_order' => 15,
            'is_active' => true,
        ]);

        AboutContent::create([
            'section' => 'contact',
            'title' => 'Working Hours',
            'content' => 'Monday - Friday: 8:00 AM - 6:00 PM\nSaturday: 9:00 AM - 4:00 PM\nSunday: Closed\nSupport chat available 24/7',
            'icon' => 'clock',
            'sort_order' => 16,
            'is_active' => true,
        ]);
    }
}
