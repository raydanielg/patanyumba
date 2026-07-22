<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->only(['name', 'icon']);
        $data['slug'] = Str::slug($request->name);
        $data['sort_order'] = Category::max('sort_order') + 1;
        $data['is_active'] = true;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = Storage::url($path);
        }

        Category::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Category created']);
        }
        return back()->with('status', 'Category created');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->only(['name', 'icon']);
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($category->image) {
                $oldPath = str_replace('/storage/', '', $category->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = Storage::url($path);
        }

        $category->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Category updated']);
        }
        return back()->with('status', 'Category updated');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            $oldPath = str_replace('/storage/', '', $category->image);
            Storage::disk('public')->delete($oldPath);
        }
        $category->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Category deleted']);
        }
        return back()->with('status', 'Category deleted');
    }

    public function toggle(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $category->name . ' ' . ($category->is_active ? 'enabled' : 'disabled'),
                'is_active' => $category->is_active,
            ]);
        }
        return back()->with('status', 'Category toggled');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer',
        ]);

        foreach ($request->orders as $index => $id) {
            Category::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Order updated']);
        }
        return back()->with('status', 'Order updated');
    }
}
