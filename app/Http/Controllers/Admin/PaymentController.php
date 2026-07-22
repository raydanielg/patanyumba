<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
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
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tx_id', 'like', "%{$search}%")
                  ->orWhere('provider_tx_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest()->paginate(15);
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $totalTransactions = Payment::count();
        $users = User::where('is_active', true)->get();
        $properties = Property::where('status', 'approved')->limit(50)->get();

        return view('admin.payments.index', compact('payments', 'totalRevenue', 'totalTransactions', 'users', 'properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'payment_type' => 'required|in:unlock,subscription,featured,sponsored',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,mpesa,airtel_money,mixx_yas,halopesa,tpesa,visa,mastercard',
            'provider_tx_id' => 'nullable|string|max:255',
            'status' => 'required|in:pending,success,failed,refunded',
        ]);

        $validated['tx_id'] = 'TXN' . strtoupper(uniqid());
        $validated['currency'] = 'TZS';
        if ($validated['status'] === 'success') {
            $validated['paid_at'] = now();
        }

        $payment = Payment::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment recorded successfully', 'payment' => $payment]);
        }
        return back()->with('status', 'Payment recorded');
    }

    public function show(Payment $payment)
    {
        $payment->load('user', 'property', 'subscription');
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'payment' => $payment]);
        }
        return view('admin.payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,success,failed,refunded',
        ]);

        $payment->update([
            'status' => $validated['status'],
            'paid_at' => $validated['status'] === 'success' ? now() : $payment->paid_at,
        ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment status updated', 'status' => $validated['status']]);
        }
        return back()->with('status', 'Payment status updated');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment deleted successfully']);
        }
        return back()->with('status', 'Payment deleted');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:payments,id']);

        $count = Payment::whereIn('id', $request->ids)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $count . ' payments deleted successfully']);
        }
        return back()->with('status', $count . ' payments deleted');
    }
}
