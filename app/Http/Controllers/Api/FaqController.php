<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::where('is_active', true)->orderBy('sort_order')->orderBy('id');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $faqs = $query->get();

        $grouped = $faqs->groupBy('category')->map(function ($items, $category) {
            return [
                'category' => ucfirst($category),
                'items' => $items->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }

    public function categories()
    {
        $categories = Faq::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->map(fn ($c) => ucfirst($c))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
