<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with('user', 'images');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('property_type', $request->type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $properties = $query->latest()->paginate(15);
        $landlords = \App\Models\User::where('role', 'landlord')->where('is_active', true)->get();
        return view('admin.properties.index', compact('properties', 'landlords'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'property_type' => 'required|in:apartment,house,commercial,land,studio,maisonette',
            'price' => 'required|numeric|min:0',
            'region' => 'required|string',
            'district' => 'required|string',
            'ward' => 'nullable|string',
            'street' => 'nullable|string',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|integer|min:0',
            'is_furnished' => 'boolean',
        ]);

        $validated['status'] = 'pending';
        $validated['is_available'] = true;
        $validated['currency'] = 'TZS';

        $property = Property::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Property created successfully', 'property' => $property]);
        }
        return back()->with('status', 'Property created successfully');
    }

    public function show(Property $property)
    {
        $property->load('user', 'images', 'reviews', 'payments');
        return view('admin.properties.show', compact('property'));
    }

    public function destroy(Property $property)
    {
        $property->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Property deleted successfully']);
        }
        return back()->with('status', 'Property deleted');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:properties,id']);

        $count = Property::whereIn('id', $request->ids)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $count . ' properties deleted successfully']);
        }
        return back()->with('status', $count . ' properties deleted');
    }

    public function approve(Property $property)
    {
        $property->update(['status' => 'approved']);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Property approved', 'status' => 'approved']);
        }
        return back()->with('status', 'Property approved');
    }

    public function reject(Request $request, Property $property)
    {
        $property->update(['status' => 'rejected']);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Property rejected', 'status' => 'rejected']);
        }
        return back()->with('status', 'Property rejected');
    }

    public function toggleFeatured(Property $property)
    {
        $property->update([
            'is_featured' => !$property->is_featured,
            'featured_until' => $property->is_featured ? null : now()->addDays(30),
        ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => $property->is_featured ? 'Property featured' : 'Feature removed', 'is_featured' => $property->is_featured]);
        }
        return back()->with('status', $property->is_featured ? 'Property featured' : 'Feature removed');
    }
}
