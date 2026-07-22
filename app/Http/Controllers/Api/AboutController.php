<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $items = AboutContent::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $grouped = $items->groupBy('section')->map(function ($group, $section) {
            return [
                'section' => $section,
                'items' => $group->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }

    public function show($section)
    {
        $items = AboutContent::where('is_active', true)
            ->where('section', $section)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}
