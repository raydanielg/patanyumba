<?php

namespace App\Http\Controllers\Admin;

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

        $payments = $query->latest()->paginate(15);
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $totalTransactions = Payment::count();

        return view('admin.payments.index', compact('payments', 'totalRevenue', 'totalTransactions'));
    }

    public function show(Payment $payment)
    {
        $payment->load('user', 'property', 'subscription');
        return view('admin.payments.show', compact('payment'));
    }
}
