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
        return view('admin.properties.index', compact('properties'));
    }

    public function show(Property $property)
    {
        $property->load('user', 'images', 'reviews', 'payments');
        return view('admin.properties.show', compact('property'));
    }

    public function approve(Property $property)
    {
        $property->update(['status' => 'approved']);
        return back()->with('status', 'Property approved');
    }

    public function reject(Request $request, Property $property)
    {
        $property->update(['status' => 'rejected']);
        return back()->with('status', 'Property rejected');
    }

    public function toggleFeatured(Property $property)
    {
        $property->update([
            'is_featured' => !$property->is_featured,
            'featured_until' => $property->is_featured ? null : now()->addDays(30),
        ]);
        return back()->with('status', $property->is_featured ? 'Property featured' : 'Feature removed');
    }
}
