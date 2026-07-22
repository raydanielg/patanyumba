<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyUnit;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with('user', 'images', 'units', 'categories');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'approved');
        }

        if ($request->filled('type')) {
            $query->where('property_type', $request->type);
        }

        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        if ($request->filled('is_furnished')) {
            $query->where('is_furnished', filter_var($request->is_furnished, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('ward', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                case 'popular':
                    $query->orderBy('views_count', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $perPage = $request->filled('per_page') ? min($request->per_page, 50) : 15;
        $properties = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $properties,
        ]);
    }

    public function show(Property $property)
    {
        $property->load('user', 'images', 'units', 'reviews.user', 'categories');

        if (auth()->check()) {
            $property->increment('views_count');
        }

        return response()->json([
            'success' => true,
            'data' => $property,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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

        $validated['user_id'] = $request->user()->id;
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

        return response()->json([
            'success' => true,
            'message' => 'Property created successfully',
            'data' => $property->load('units'),
        ], 201);
    }

    public function update(Request $request, Property $property)
    {
        if ($request->user()->id !== $property->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'property_type' => 'sometimes|in:apartment,house,commercial,land,studio,maisonette,hotel,guest_house,multi_unit',
            'price' => 'sometimes|numeric|min:0',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'rental_period' => 'nullable|in:day,week,month,year',
            'contact_phone' => 'nullable|string|max:20',
            'region' => 'sometimes|string',
            'district' => 'sometimes|string',
            'ward' => 'nullable|string',
            'street' => 'nullable|string',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|integer|min:0',
            'is_furnished' => 'boolean',
            'is_available' => 'boolean',
        ]);

        $property->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Property updated successfully',
            'data' => $property->fresh()->load('units', 'images'),
        ]);
    }

    public function destroy(Request $request, Property $property)
    {
        if ($request->user()->id !== $property->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $property->delete();

        return response()->json([
            'success' => true,
            'message' => 'Property deleted successfully',
        ]);
    }

    public function myProperties(Request $request)
    {
        $properties = Property::where('user_id', $request->user()->id)
            ->with('images', 'units')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $properties,
        ]);
    }

    // Unit endpoints
    public function units(Property $property)
    {
        return response()->json([
            'success' => true,
            'data' => $property->units,
        ]);
    }

    public function showUnit(Property $property, $unitId)
    {
        $unit = $property->units()->findOrFail($unitId);
        return response()->json([
            'success' => true,
            'data' => $unit,
        ]);
    }

    public function storeUnit(Request $request, Property $property)
    {
        if ($request->user()->id !== $property->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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

        return response()->json([
            'success' => true,
            'message' => 'Unit added successfully',
            'data' => $unit,
        ], 201);
    }

    public function updateUnit(Request $request, Property $property, $unitId)
    {
        if ($request->user()->id !== $property->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $unit = $property->units()->findOrFail($unitId);
        $validated = $request->validate([
            'unit_name' => 'sometimes|string|max:255',
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

        return response()->json([
            'success' => true,
            'message' => 'Unit updated successfully',
            'data' => $unit->fresh(),
        ]);
    }

    public function destroyUnit(Request $request, Property $property, $unitId)
    {
        if ($request->user()->id !== $property->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $unit = $property->units()->findOrFail($unitId);
        $unit->delete();
        $property->decrement('total_units');

        return response()->json([
            'success' => true,
            'message' => 'Unit deleted successfully',
        ]);
    }
}
