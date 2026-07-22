<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user', 'property');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('payment_type', $request->type);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $payments = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    public function myPayments(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->with('property')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    public function show(Payment $payment)
    {
        $payment->load('user', 'property', 'subscription');

        return response()->json([
            'success' => true,
            'data' => $payment,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'payment_type' => 'required|in:unlock,subscription,featured,sponsored',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,mpesa,airtel_money,mixx_yas,halopesa,tpesa,visa,mastercard',
            'provider_tx_id' => 'nullable|string|max:255',
            'status' => 'required|in:pending,success,failed,refunded',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['tx_id'] = 'TXN' . strtoupper(uniqid());
        $validated['currency'] = 'TZS';
        if ($validated['status'] === 'success') {
            $validated['paid_at'] = now();
        }

        $payment = Payment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'data' => $payment,
        ], 201);
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,success,failed,refunded',
        ]);

        $payment->update([
            'status' => $validated['status'],
            'paid_at' => $validated['status'] === 'success' ? now() : $payment->paid_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated',
            'data' => $payment->fresh(),
        ]);
    }

    public function destroy(Request $request, Payment $payment)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully',
        ]);
    }
}
