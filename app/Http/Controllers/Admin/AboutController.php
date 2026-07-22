<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $contents = AboutContent::orderBy('sort_order')->orderBy('id')->paginate(20);
        return view('admin.about.index', compact('contents'));
    }

    public function create()
    {
        return view('admin.about.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string|max:5000',
            'icon' => 'nullable|string|max:50',
            'image_url' => 'nullable|url|max:500',
            'stats' => 'nullable|json',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if (!empty($validated['stats'])) {
            $validated['stats'] = json_decode($validated['stats'], true);
        }

        AboutContent::create($validated);

        return redirect()->route('admin.about.index')->with('success', 'About content created successfully.');
    }

    public function edit(AboutContent $about)
    {
        return view('admin.about.edit', compact('about'));
    }

    public function update(Request $request, AboutContent $about)
    {
        $validated = $request->validate([
            'section' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string|max:5000',
            'icon' => 'nullable|string|max:50',
            'image_url' => 'nullable|url|max:500',
            'stats' => 'nullable|json',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if (!empty($validated['stats'])) {
            $validated['stats'] = json_decode($validated['stats'], true);
        } else {
            $validated['stats'] = null;
        }

        $about->update($validated);

        return redirect()->route('admin.about.index')->with('success', 'About content updated successfully.');
    }

    public function destroy(AboutContent $about)
    {
        $about->delete();
        return redirect()->route('admin.about.index')->with('success', 'About content deleted successfully.');
    }
}
