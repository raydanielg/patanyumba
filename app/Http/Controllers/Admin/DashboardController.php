<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Payment;
use App\Models\KycDocument;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalLandlords' => User::where('role', 'landlord')->count(),
            'totalTenants' => User::where('role', 'tenant')->count(),
            'totalAgents' => User::where('role', 'agent')->count(),
            'totalProperties' => Property::count(),
            'activeListings' => Property::where('status', 'approved')->where('is_available', true)->count(),
            'pendingApprovals' => Property::where('status', 'pending')->count(),
            'totalRevenue' => Payment::where('status', 'success')->sum('amount'),
            'totalTransactions' => Payment::count(),
            'successRate' => Payment::count() > 0
                ? round(Payment::where('status', 'success')->count() / Payment::count() * 100, 1)
                : 100,
            'pendingKyc' => KycDocument::where('status', 'pending')->count(),
            'newUsersThisWeek' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'newPropertiesThisWeek' => Property::where('created_at', '>=', now()->subDays(7))->count(),
            'revenueThisWeek' => Payment::where('status', 'success')->where('created_at', '>=', now()->subDays(7))->sum('amount'),
            'transactionsThisWeek' => Payment::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $dailyRevenue = [];
        $dailyLabels = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('d M');
            $dailyRevenue[] = Payment::where('status', 'success')
                ->whereDate('created_at', $date->toDateString())
                ->sum('amount');
        }

        $recentTransactions = Payment::with('user', 'property')
            ->latest()
            ->take(8)
            ->get();

        $topLandlords = User::where('role', 'landlord')
            ->withCount('properties')
            ->orderByDesc('properties_count')
            ->take(5)
            ->get();

        $propertyTypes = Property::selectRaw('property_type, COUNT(*) as count')
            ->groupBy('property_type')
            ->pluck('count', 'property_type');

        return view('admin.dashboard', compact(
            'stats', 'dailyRevenue', 'dailyLabels',
            'recentTransactions', 'topLandlords', 'propertyTypes'
        ));
    }
}
