<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $method = $request->query('method');

        $query = Payment::with(['booking.facility', 'booking.court', 'user'])->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('payment_status', $status);
        }

        if ($method) {
            $query->where('payment_method', $method);
        }

        $payments = $query->paginate(15);

        return view('admin.payments.index', compact('payments', 'status', 'method'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment->payment_status = $validated['payment_status'];
        if ($validated['payment_status'] === 'paid' && !$payment->paid_at) {
            $payment->paid_at = now();
        }
        if (!empty($validated['reference_number'])) {
            $payment->reference_number = $validated['reference_number'];
        }
        if (!empty($validated['notes'])) {
            $payment->notes = $validated['notes'];
        }
        $payment->save();

        return back()->with('success', 'Payment status updated to ' . ucfirst($validated['payment_status']));
    }
}
