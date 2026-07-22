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
            'property_type' => 'required|in:apartment,house,commercial,land,studio,maisonette,hotel,guest_house,multi_unit',
            'listing_type' => 'required|in:single,multi_unit',
            'price' => 'required|numeric|min:0',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'rental_period' => 'nullable|in:day,week,month,year',
            'contact_phone' => 'nullable|string|max:20',
            'region' => 'required|string',
            'district' => 'required|string',
            'ward' => 'nullable|string',
            'street' => 'nullable|string',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|integer|min:0',
            'total_units' => 'nullable|integer|min:1',
            'is_furnished' => 'boolean',
        ]);

        $validated['status'] = 'pending';
        $validated['is_available'] = true;
        $validated['currency'] = 'TZS';
        $validated['rental_period'] = $validated['rental_period'] ?? 'month';

        if ($validated['listing_type'] === 'single') {
            $validated['total_units'] = 1;
        } else {
            $validated['total_units'] = $validated['total_units'] ?? 1;
        }

        $property = Property::create($validated);

        if ($validated['listing_type'] === 'single') {
            $property->units()->create([
                'unit_name' => 'Main Unit',
                'price' => $validated['price'],
                'bedrooms' => $validated['bedrooms'] ?? 0,
                'bathrooms' => $validated['bathrooms'] ?? 0,
                'area_sqm' => $validated['area_sqm'] ?? null,
                'is_furnished' => $validated['is_furnished'] ?? false,
                'is_available' => true,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Property created successfully', 'property' => $property->load('units')]);
        }
        return back()->with('status', 'Property created successfully');
    }

    public function storeUnit(Request $request, Property $property)
    {
        $validated = $request->validate([
            'unit_name' => 'required|string|max:255',
            'unit_number' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|integer|min:0',
            'floor_number' => 'nullable|integer|min:0',
            'max_occupants' => 'nullable|integer|min:1',
            'is_furnished' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['is_available'] = true;
        $unit = $property->units()->create($validated);
        $property->increment('total_units');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Unit added successfully', 'unit' => $unit]);
        }
        return back()->with('status', 'Unit added successfully');
    }

    public function updateUnit(Request $request, Property $property, $unitId)
    {
        $unit = $property->units()->findOrFail($unitId);
        $validated = $request->validate([
            'unit_name' => 'required|string|max:255',
            'unit_number' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|integer|min:0',
            'floor_number' => 'nullable|integer|min:0',
            'max_occupants' => 'nullable|integer|min:1',
            'is_furnished' => 'boolean',
            'is_available' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $unit->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Unit updated successfully', 'unit' => $unit->fresh()]);
        }
        return back()->with('status', 'Unit updated successfully');
    }

    public function destroyUnit(Property $property, $unitId)
    {
        $unit = $property->units()->findOrFail($unitId);
        $unit->delete();
        $property->decrement('total_units');

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Unit deleted successfully']);
        }
        return back()->with('status', 'Unit deleted');
    }

    public function show(Property $property)
    {
        $property->load('user', 'images', 'reviews', 'payments', 'units');
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
