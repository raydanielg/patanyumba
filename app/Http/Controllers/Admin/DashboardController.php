<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalProperties' => 0,
            'totalUsers' => 0,
            'totalListings' => 0,
            'activeListings' => 0,
            'newUsersThisWeek' => 0,
            'newListingsThisWeek' => 0,
            'totalViews' => 0,
            'viewsThisWeek' => 0,
            'successRate' => 98,
            'pendingApprovals' => 0,
        ];

        $dailyViews = [120, 145, 180, 165, 220, 280, 310, 290, 350, 420, 380, 410, 390, 450];
        $dailyLabels = [];
        for ($i = 13; $i >= 0; $i--) {
            $dailyLabels[] = now()->subDays($i)->format('d M');
        }

        $recentListings = collect([]);
        $topAgents = collect([]);

        return view('admin.dashboard', compact('stats', 'dailyViews', 'dailyLabels', 'recentListings', 'topAgents'));
    }
}
