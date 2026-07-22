<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    public function heroSlides()
    {
        $settings = Setting::where('group', 'hero')->get()->keyBy('key');

        $slides = [];
        for ($i = 1; $i <= 5; $i++) {
            $image = $settings->get("hero_image_$i")?->value;
            if ($image) {
                $slides[] = [
                    'id' => $i,
                    'image' => url($image),
                    'title' => $settings->get("hero_title_$i")?->value ?? 'Find Your Perfect Home',
                    'subtitle' => $settings->get("hero_subtitle_$i")?->value ?? 'Browse thousands of verified listings',
                ];
            }
        }

        return response()->json([
            'success' => true,
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
}
