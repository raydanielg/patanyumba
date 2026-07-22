<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = Report::with('reporter', 'reportable');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        $reports = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    public function show(Request $request, Report $report)
    {
        if (!$request->user()->isAdmin() && $request->user()->id !== $report->reporter_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $report->load('reporter', 'reportable', 'resolver');

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_type' => 'required|string|in:App\\Models\\Property,App\\Models\\User,App\\Models\\Review',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string|in:spam,fraud,misleading,offensive,duplicate,other',
            'description' => 'nullable|string|max:2000',
        ]);

        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'reportable_type' => $validated['reportable_type'],
            'reportable_id' => $validated['reportable_id'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report submitted successfully',
            'data' => $report,
        ], 201);
    }

    public function resolve(Request $request, Report $report)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:resolved,dismissed',
            'resolution_note' => 'nullable|string|max:2000',
        ]);

        $report->update([
            'status' => $validated['status'],
            'resolution_note' => $validated['resolution_note'] ?? null,
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report resolved',
            'data' => $report->fresh()->load('reporter', 'reportable', 'resolver'),
        ]);
    }

    public function myReports(Request $request)
    {
        $reports = Report::where('reporter_id', $request->user()->id)
            ->with('reportable')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }
}
