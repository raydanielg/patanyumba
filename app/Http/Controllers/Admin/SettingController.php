<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Settings updated successfully']);
        }
        return back()->with('status', 'Settings updated');
    }

    public function uploadHeroImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'slide' => 'required|integer|in:1,2,3,4,5',
        ]);

        $slide = $request->slide;
        $file = $request->file('image');
        $path = $file->store('hero', 'public');

        $url = Storage::url($path);

        Setting::updateOrCreate(
            ['key' => "hero_image_$slide"],
            ['value' => $url, 'group' => 'hero']
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Hero slide $slide image uploaded",
                'url' => $url,
            ]);
        }

        return back()->with('status', "Hero slide $slide image uploaded");
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $setting = Setting::where('key', $request->key)->first();
        if (!$setting) {
            $setting = Setting::create(['key' => $request->key, 'value' => 'false', 'group' => 'features']);
        }

        $currentValue = $setting->value === 'true';
        $setting->update(['value' => $currentValue ? 'false' : 'true']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst(str_replace('_', ' ', $setting->key)) . ' ' . ($currentValue ? 'disabled' : 'enabled'),
                'value' => $setting->value,
                'enabled' => !$currentValue,
            ]);
        }
        return back()->with('status', 'Setting toggled');
    }
}
