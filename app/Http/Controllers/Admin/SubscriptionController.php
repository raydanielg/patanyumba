<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::latest()->paginate(15);
        return view('admin.subscriptions.plans', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:subscription_plans,slug',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:one_time,weekly,monthly,quarterly,yearly',
            'target_audience' => 'required|in:house_hunter,agent,both',
            'unlock_limit' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable|string',
        ]);

        if (!empty($validated['features'])) {
            $validated['features'] = array_filter(array_map('trim', explode("\n", $validated['features'])));
        } else {
            $validated['features'] = null;
        }
        $validated['currency'] = 'TZS';
        $validated['is_active'] = true;

        $plan = SubscriptionPlan::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Plan created successfully', 'plan' => $plan]);
        }
        return back()->with('status', 'Plan created');
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:one_time,weekly,monthly,quarterly,yearly',
            'target_audience' => 'required|in:house_hunter,agent,both',
            'unlock_limit' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable|string',
        ]);

        if (!empty($validated['features'])) {
            $validated['features'] = array_filter(array_map('trim', explode("\n", $validated['features'])));
        } else {
            $validated['features'] = null;
        }

        $plan->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Plan updated successfully', 'plan' => $plan]);
        }
        return back()->with('status', 'Plan updated');
    }

    public function togglePlan(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => $plan->is_active ? 'Plan activated' : 'Plan deactivated', 'is_active' => $plan->is_active]);
        }
        return back()->with('status', $plan->is_active ? 'Plan activated' : 'Plan deactivated');
    }

    public function destroyPlan(SubscriptionPlan $plan)
    {
        $plan->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Plan deleted successfully']);
        }
        return back()->with('status', 'Plan deleted');
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::with('user', 'plan')->latest()->paginate(15);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }
}
