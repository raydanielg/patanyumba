<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

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
