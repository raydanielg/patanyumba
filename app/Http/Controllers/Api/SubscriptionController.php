<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    // Plans
    public function plans(Request $request)
    {
        $query = SubscriptionPlan::query();

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        if ($request->filled('target_audience')) {
            $query->where('target_audience', $request->target_audience);
        }

        $plans = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    public function showPlan(SubscriptionPlan $plan)
    {
        return response()->json([
            'success' => true,
            'data' => $plan,
        ]);
    }

    public function storePlan(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:subscription_plans,slug',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:one_time,weekly,monthly,quarterly,yearly',
            'target_audience' => 'required|in:house_hunter,agent,both',
            'unlock_limit' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable|array',
        ]);

        $validated['currency'] = 'TZS';
        $validated['is_active'] = true;

        $plan = SubscriptionPlan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'data' => $plan,
        ], 201);
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'billing_cycle' => 'sometimes|in:one_time,weekly,monthly,quarterly,yearly',
            'target_audience' => 'sometimes|in:house_hunter,agent,both',
            'unlock_limit' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'data' => $plan->fresh(),
        ]);
    }

    public function togglePlan(Request $request, SubscriptionPlan $plan)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $plan->update(['is_active' => !$plan->is_active]);

        return response()->json([
            'success' => true,
            'message' => $plan->is_active ? 'Plan activated' : 'Plan deactivated',
            'data' => $plan->fresh(),
        ]);
    }

    public function destroyPlan(Request $request, SubscriptionPlan $plan)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully',
        ]);
    }

    // User Subscriptions
    public function mySubscriptions(Request $request)
    {
        $subscriptions = Subscription::where('user_id', $request->user()->id)
            ->with('plan')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ]);
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

        if (!$plan->is_active) {
            return response()->json(['success' => false, 'message' => 'This plan is not available'], 422);
        }

        $startsAt = now();
        $endsAt = match ($plan->billing_cycle) {
            'weekly' => $startsAt->copy()->addWeek(),
            'monthly' => $startsAt->copy()->addMonth(),
            'quarterly' => $startsAt->copy()->addQuarter(),
            'yearly' => $startsAt->copy()->addYear(),
            default => $startsAt->copy()->addDays(365),
        };

        $subscription = Subscription::create([
            'user_id' => $request->user()->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'unlocks_used' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscribed successfully',
            'data' => $subscription->load('plan'),
        ], 201);
    }

    public function cancelSubscription(Request $request, Subscription $subscription)
    {
        if ($request->user()->id !== $subscription->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $subscription->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled',
            'data' => $subscription->fresh(),
        ]);
    }
}
