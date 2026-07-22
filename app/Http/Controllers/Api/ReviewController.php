<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Property;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('user', 'property');

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'approved');
        }

        $reviews = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    public function propertyReviews(Property $property)
    {
        $reviews = $property->reviews()
            ->with('user')
            ->where('status', 'approved')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existing = Review::where('user_id', $request->user()->id)
            ->where('property_id', $validated['property_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this property',
            ], 422);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'property_id' => $validated['property_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review added successfully',
            'data' => $review->load('user'),
        ], 201);
    }

    public function update(Request $request, Review $review)
    {
        if ($request->user()->id !== $review->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review->fresh()->load('user'),
        ]);
    }

    public function destroy(Request $request, Review $review)
    {
        if ($request->user()->id !== $review->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }
}
