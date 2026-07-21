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
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:subscription_plans,slug',
            'price' => 'required|numeric',
            'billing_cycle' => 'required|in:one_time,weekly,monthly,quarterly,yearly',
            'unlock_limit' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        SubscriptionPlan::create($request->all());
        return back()->with('status', 'Plan created');
    }

    public function togglePlan(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);
        return back()->with('status', $plan->is_active ? 'Plan activated' : 'Plan deactivated');
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::with('user', 'plan')->latest()->paginate(15);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }
}
