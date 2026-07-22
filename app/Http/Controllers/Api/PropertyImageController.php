<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyImageController extends Controller
{
    public function index(Property $property)
    {
        return response()->json([
            'success' => true,
            'data' => $property->images()->orderBy('sort_order')->get(),
        ]);
    }

    public function upload(Request $request, Property $property)
    {
        if ($request->user()->id !== $property->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $uploaded = [];
        $sortOrder = $property->images()->max('sort_order') ?? 0;

        foreach ($request->file('images') as $index => $file) {
            $sortOrder++;
            $path = $file->store('properties', 'public');

            $image = PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => $path,
                'thumbnail_path' => $path,
                'is_primary' => $property->images()->count() === 0 && $index === 0,
                'sort_order' => $sortOrder,
            ]);

            $uploaded[] = $image;
        }

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' image(s) uploaded successfully',
            'data' => $uploaded,
        ], 201);
    }

    public function setPrimary(Request $request, Property $property, PropertyImage $image)
    {
        if ($request->user()->id !== $property->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $property->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image updated',
            'data' => $image->fresh(),
        ]);
    }

    public function reorder(Request $request, Property $property)
    {
        if ($request->user()->id !== $property->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:property_images,id',
            'images.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->images as $item) {
            PropertyImage::where('id', $item['id'])
                ->where('property_id', $property->id)
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Images reordered successfully',
        ]);
    }

    public function destroy(Request $request, Property $property, PropertyImage $image)
    {
        if ($request->user()->id !== $property->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($image->image_path) {
            Storage::disk('public')->delete($image->image_path);
        }
        if ($image->thumbnail_path && $image->thumbnail_path !== $image->image_path) {
            Storage::disk('public')->delete($image->thumbnail_path);
        }

        $image->delete();

        if ($image->is_primary) {
            $nextImage = $property->images()->orderBy('sort_order')->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
        ]);
    }
}
