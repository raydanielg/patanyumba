<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = Favorite::where('user_id', $request->user()->id)
            ->with('property.user', 'property.images', 'property.units')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $favorites,
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $request->property_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'message' => 'Removed from favorites',
                'is_favorited' => false,
            ]);
        }

        Favorite::create([
            'user_id' => $request->user()->id,
            'property_id' => $request->property_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to favorites',
            'is_favorited' => true,
        ], 201);
    }

    public function check(Request $request, Property $property)
    {
        $isFavorited = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $property->id)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
        ]);
    }

    public function destroy(Request $request, Favorite $favorite)
    {
        if ($request->user()->id !== $favorite->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from favorites',
        ]);
    }
}
