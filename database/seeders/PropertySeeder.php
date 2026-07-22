<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $landlord = User::where('email', 'landlord@patanyumba.com')->first();
        $agent = User::where('email', 'agent@patanyumba.com')->first();

        if (!$landlord) {
            $this->call(LandlordUserSeeder::class);
            $landlord = User::where('email', 'landlord@patanyumba.com')->first();
            $agent = User::where('email', 'agent@patanyumba.com')->first();
        }

        $housesCat = Category::where('name', 'Houses')->first();
        $apartmentsCat = Category::where('name', 'Apartments')->first();
        $roomsCat = Category::where('name', 'Rooms')->first();
        $commercialCat = Category::where('name', 'Commercial')->first();
        $officeCat = Category::where('name', 'Office')->first();
        $landCat = Category::where('name', 'Land')->first();

        $properties = [
            [
                'user_id' => $landlord->id,
                'title' => 'Modern 3 Bedroom House in Mbezi',
                'description' => 'Beautiful modern house located in the heart of Mbezi Beach. This property features 3 spacious bedrooms, 2 bathrooms, a large living room, modern kitchen, and a garden. The house is fully fenced with a gate and has ample parking space. Located in a quiet, secure neighborhood with easy access to main roads, schools, and shopping centers.',
                'property_type' => 'house',
                'listing_type' => 'single',
                'price' => 850000,
                'currency' => 'TZS',
                'rental_period' => 'month',
                'contact_phone' => '+255712345678',
                'region' => 'Dar es Salaam',
                'district' => 'Kinondoni',
                'ward' => 'Mbezi Beach',
                'street' => 'Plot 45, Mbezi Beach Road',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area_sqm' => 250,
                'is_furnished' => false,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => true,
                'amenities' => ['Parking', 'Garden', 'Security Guard', 'Water Tank', 'Electricity', 'Fence', 'Gate'],
                'categories' => [$housesCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1568605114967-8130f81a6abd?w=800',
                    'https://images.unsplash.com/photo-1564013799919-ab6000fcbc6c?w=800',
                    'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800',
                ],
            ],
            [
                'user_id' => $landlord->id,
                'title' => 'Luxury Apartment with Ocean View - Masaki',
                'description' => 'Stunning luxury apartment in Masaki with breathtaking ocean views. Features 2 bedrooms with en-suite bathrooms, modern fitted kitchen, spacious living area with balcony, swimming pool, gym, and 24/7 security. Walking distance to Oyster Bay shopping center and restaurants.',
                'property_type' => 'apartment',
                'listing_type' => 'single',
                'price' => 1200000,
                'currency' => 'TZS',
                'rental_period' => 'month',
                'contact_phone' => '+255712345678',
                'region' => 'Dar es Salaam',
                'district' => 'Kinondoni',
                'ward' => 'Masaki',
                'street' => 'Msasani Road, Plot 12',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area_sqm' => 120,
                'is_furnished' => true,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => true,
                'amenities' => ['WiFi', 'Swimming Pool', 'Gym', 'Parking', 'Elevator', 'Security Guard', 'AC', 'Balcony', 'Furnished'],
                'categories' => [$apartmentsCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800',
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800',
                ],
            ],
            [
                'user_id' => $landlord->id,
                'title' => 'Spacious Family Home - Kigamboni',
                'description' => 'Large family home in Kigamboni with 4 bedrooms, 3 bathrooms, double garage, large garden, and outdoor kitchen. Perfect for families looking for space and tranquility. Close to Kigamboni ferry and beach.',
                'property_type' => 'house',
                'listing_type' => 'single',
                'price' => 650000,
                'currency' => 'TZS',
                'rental_period' => 'month',
                'contact_phone' => '+255712345678',
                'region' => 'Dar es Salaam',
                'district' => 'Temeke',
                'ward' => 'Kigamboni',
                'street' => 'Beach Road, Plot 78',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area_sqm' => 400,
                'is_furnished' => false,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => true,
                'amenities' => ['Parking', 'Garden', 'Outdoor Kitchen', 'Water Tank', 'Electricity', 'Fence', 'Gate'],
                'categories' => [$housesCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800',
                    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=800',
                ],
            ],
            [
                'user_id' => $agent->id,
                'title' => 'Studio Apartment for Rent - Ilala',
                'description' => 'Compact and modern studio apartment in Ilala CBD. Perfect for young professionals or students. Comes with fitted kitchen, bathroom, and living area. Walking distance to public transport and markets.',
                'property_type' => 'studio',
                'listing_type' => 'single',
                'price' => 250000,
                'currency' => 'TZS',
                'rental_period' => 'month',
                'contact_phone' => '+255755987654',
                'region' => 'Dar es Salaam',
                'district' => 'Ilala',
                'ward' => 'Ilala',
                'street' => 'Nyerere Road, Plot 23',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'area_sqm' => 45,
                'is_furnished' => true,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => true,
                'amenities' => ['WiFi', 'Furnished', 'Electricity', 'Water Tank', 'Security Guard'],
                'categories' => [$apartmentsCat?->id, $roomsCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800',
                    'https://images.unsplash.com/photo-1554995207-c18c203602cb?w=800',
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d029?w=800',
                ],
            ],
            [
                'user_id' => $agent->id,
                'title' => 'Commercial Space for Rent - Ubungo',
                'description' => 'Prime commercial space on ground floor in Ubungo. 200 sqm open plan space suitable for retail, office, or restaurant. High foot traffic area with main road frontage. Includes parking and loading area.',
                'property_type' => 'commercial',
                'listing_type' => 'single',
                'price' => 1500000,
                'currency' => 'TZS',
                'rental_period' => 'month',
                'contact_phone' => '+255755987654',
                'region' => 'Dar es Salaam',
                'district' => 'Ubungo',
                'ward' => 'Ubungo',
                'street' => 'Morogoro Road, Plot 110',
                'bedrooms' => 0,
                'bathrooms' => 2,
                'area_sqm' => 200,
                'is_furnished' => false,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => true,
                'amenities' => ['Parking', 'Electricity', 'Water Tank', 'Security Guard', 'Store'],
                'categories' => [$commercialCat?->id, $officeCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800',
                    'https://images.unsplash.com/photo-1497366811353-67807737e536?w=800',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800',
                ],
            ],
            [
                'user_id' => $landlord->id,
                'title' => '2 Bedroom Apartment - Mikocheni',
                'description' => 'Well maintained 2 bedroom apartment in Mikocheni. Spacious rooms, fitted kitchen, balcony, and shared garden. Close to Mikocheni hospital and Agha Khan school. Secure compound with guard.',
                'property_type' => 'apartment',
                'listing_type' => 'single',
                'price' => 550000,
                'currency' => 'TZS',
                'rental_period' => 'month',
                'contact_phone' => '+255712345678',
                'region' => 'Dar es Salaam',
                'district' => 'Kinondoni',
                'ward' => 'Mikocheni',
                'street' => 'Old Bagamoyo Road, Plot 67',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area_sqm' => 85,
                'is_furnished' => false,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => true,
                'amenities' => ['Parking', 'Garden', 'Security Guard', 'Water Tank', 'Electricity', 'Balcony'],
                'categories' => [$apartmentsCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1470182404256-ce3c8a5c5a79?w=800',
                    'https://images.unsplash.com/photo-1486304871900-6255d0f00f94?w=800',
                    'https://images.unsplash.com/photo-1502672023488-70e25813eb80?w=800',
                ],
            ],
            [
                'user_id' => $landlord->id,
                'title' => 'Multi-Unit Residential Building - Sinza',
                'description' => 'Multi-unit residential building in Sinza with 6 self-contained units. Each unit has 1 bedroom, 1 bathroom, kitchen, and living area. Great investment property with steady rental income. All units currently occupied except 2.',
                'property_type' => 'apartment',
                'listing_type' => 'multi_unit',
                'price' => 180000,
                'currency' => 'TZS',
                'rental_period' => 'month',
                'contact_phone' => '+255712345678',
                'region' => 'Dar es Salaam',
                'district' => 'Kinondoni',
                'ward' => 'Sinza',
                'street' => 'Sinza Mori, Plot 34',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'area_sqm' => 600,
                'total_units' => 6,
                'is_furnished' => false,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => false,
                'amenities' => ['Parking', 'Security Guard', 'Water Tank', 'Electricity', 'Fence', 'Gate'],
                'categories' => [$apartmentsCat?->id, $roomsCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1516156008625-321cf4d2ce8d?w=800',
                    'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=800',
                    'https://images.unsplash.com/photo-1493801163647-7f2d4fe1f5b7?w=800',
                ],
            ],
            [
                'user_id' => $agent->id,
                'title' => 'Single Room for Rent - Gongo la Mboto',
                'description' => 'Clean single room for rent in Gongo la Mboto. Shared bathroom and kitchen. Good for students or single professionals. Close to bus stop and local market.',
                'property_type' => 'apartment',
                'listing_type' => 'single',
                'price' => 80000,
                'currency' => 'TZS',
                'rental_period' => 'month',
                'contact_phone' => '+255755987654',
                'region' => 'Dar es Salaam',
                'district' => 'Ilala',
                'ward' => 'Gongo la Mboto',
                'street' => 'Gongo Road, Plot 5',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'area_sqm' => 20,
                'is_furnished' => false,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => false,
                'amenities' => ['Electricity', 'Water Tank'],
                'categories' => [$roomsCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1505693416388-ac5ce068eb8f?w=800',
                    'https://images.unsplash.com/photo-1522444195799-478938b359a4?w=800',
                ],
            ],
            [
                'user_id' => $landlord->id,
                'title' => 'Office Space for Rent - Upanga',
                'description' => 'Professional office space in Upanga area. 150 sqm with 4 rooms, reception area, kitchen, and 2 bathrooms. Suitable for company offices, clinics, or consultancy. Ample parking and 24/7 security.',
                'property_type' => 'commercial',
                'listing_type' => 'single',
                'price' => 900000,
                'currency' => 'TZS',
                'rental_period' => 'month',
                'contact_phone' => '+255712345678',
                'region' => 'Dar es Salaam',
                'district' => 'Ilala',
                'ward' => 'Upanga West',
                'street' => 'Upanga Street, Plot 89',
                'bedrooms' => 0,
                'bathrooms' => 2,
                'area_sqm' => 150,
                'is_furnished' => false,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => false,
                'amenities' => ['Parking', 'Electricity', 'Water Tank', 'Security Guard', 'Internet', 'AC'],
                'categories' => [$officeCat?->id, $commercialCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=800',
                    'https://images.unsplash.com/photo-1497215842964-222b430dc094?w=800',
                    'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=800',
                ],
            ],
            [
                'user_id' => $landlord->id,
                'title' => 'Land for Sale - Bagamoyo',
                'description' => 'Prime land for sale in Bagamoyo. 2000 sqm plot with title deed. Suitable for residential development, farming, or commercial use. Located near the main road with easy access to Bagamoyo town.',
                'property_type' => 'land',
                'listing_type' => 'single',
                'price' => 15000000,
                'currency' => 'TZS',
                'rental_period' => 'year',
                'contact_phone' => '+255712345678',
                'region' => 'Pwani',
                'district' => 'Bagamoyo',
                'ward' => 'Bagamoyo',
                'street' => 'Main Road, Plot 200',
                'bedrooms' => 0,
                'bathrooms' => 0,
                'area_sqm' => 2000,
                'is_furnished' => false,
                'is_available' => true,
                'status' => 'approved',
                'is_featured' => false,
                'amenities' => ['Electricity', 'Water Tank', 'Fence'],
                'categories' => [$landCat?->id],
                'images' => [
                    'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800',
                    'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800',
                ],
            ],
        ];

        foreach ($properties as $data) {
            $categoryIds = $data['categories'] ?? [];
            $imageUrls = $data['images'] ?? [];
            unset($data['categories'], $data['images']);

            $property = Property::updateOrCreate(
                ['title' => $data['title']],
                $data
            );

            // Attach categories
            if ($categoryIds) {
                $property->categories()->sync(array_filter($categoryIds));
            }

            // Create images
            foreach ($imageUrls as $index => $url) {
                PropertyImage::updateOrCreate(
                    ['property_id' => $property->id, 'image_path' => $url],
                    [
                        'property_id' => $property->id,
                        'image_path' => $url,
                        'thumbnail_path' => $url,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]
                );
            }
        }
    }
}
