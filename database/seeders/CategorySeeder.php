<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Houses', 'icon' => 'home_work', 'sort_order' => 1],
            ['name' => 'Apartments', 'icon' => 'apartment', 'sort_order' => 2],
            ['name' => 'Rooms', 'icon' => 'bed', 'sort_order' => 3],
            ['name' => 'Commercial', 'icon' => 'store', 'sort_order' => 4],
            ['name' => 'Office', 'icon' => 'business', 'sort_order' => 5],
            ['name' => 'Land', 'icon' => 'landscape', 'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}
