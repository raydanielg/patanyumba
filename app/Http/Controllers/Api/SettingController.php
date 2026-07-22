<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Category;

class SettingController extends Controller
{
    public function heroSlides()
    {
        $settings = Setting::where('group', 'hero')->get()->keyBy('key');

        $enabled = ($settings->get('hero_enabled')?->value ?? 'true') === 'true';

        $slides = [];
        for ($i = 1; $i <= 10; $i++) {
            $image = $settings->get("hero_image_$i")?->value;
            $title = $settings->get("hero_title_$i")?->value;
            $subtitle = $settings->get("hero_subtitle_$i")?->value;
            $buttonText = $settings->get("hero_button_text_$i")?->value;
            $buttonLink = $settings->get("hero_button_link_$i")?->value;

            if ($image || $title || $subtitle) {
                $slides[] = [
                    'id' => $i,
                    'image' => $image ? url($image) : null,
                    'title' => $title ?? 'Find Your Perfect Home',
                    'subtitle' => $subtitle ?? '',
                    'button_text' => $buttonText ?? '',
                    'button_link' => $buttonLink ?? '',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'enabled' => $enabled,
            'data' => $slides,
        ]);
    }

    public function appSettings()
    {
        $settings = Setting::whereIn('group', ['general', 'features', 'social'])->get()->keyBy('key');

        $data = [];
        foreach ($settings as $key => $setting) {
            $data[$key] = $setting->value;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function categories()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'icon' => $cat->icon,
                    'image' => $cat->image ? url($cat->image) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
