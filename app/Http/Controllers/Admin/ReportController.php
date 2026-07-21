<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with('reporter', 'reportable');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate(15);
        return view('admin.reports.index', compact('reports'));
    }

    public function resolve(Request $request, Report $report)
    {
        $request->validate(['resolution_note' => 'required|string']);
        $report->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'resolution_note' => $request->resolution_note,
        ]);
        return back()->with('status', 'Report resolved');
    }

    public function dismiss(Report $report)
    {
        $report->update([
            'status' => 'dismissed',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);
        return back()->with('status', 'Report dismissed');
    }
}
