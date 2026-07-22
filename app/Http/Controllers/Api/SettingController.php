<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Property;
use App\Models\Region;

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

    public function regions()
    {
        $regions = Property::where('status', 'approved')
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        return response()->json([
            'success' => true,
            'data' => $regions,
        ]);
    }

    public function allRegions()
    {
        $regions = Region::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'data' => $regions,
        ]);
    }

    public function districts($regionId)
    {
        $region = Region::where('is_active', true)->find($regionId);
        if (!$region) {
            return response()->json([
                'success' => false,
                'message' => 'Region not found',
            ], 404);
        }

        $districts = $region->districts()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'region_id']);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    public function regionsWithDistricts()
    {
        $regions = Region::where('is_active', true)
            ->with(['districts' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $regions->map(function ($region) {
                return [
                    'id' => $region->id,
                    'name' => $region->name,
                    'code' => $region->code,
                    'districts' => $region->districts->map(function ($d) {
                        return ['id' => $d->id, 'name' => $d->name];
                    }),
                ];
            }),
        ]);
    }
}
