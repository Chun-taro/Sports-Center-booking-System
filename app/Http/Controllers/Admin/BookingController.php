<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $facilities = Facility::orderBy('name')->get();
        $status = $request->query('status');
        $facilityId = $request->query('facility_id');
        $date = $request->query('date');
        $search = $request->query('search');

        $query = Booking::with(['user', 'facility', 'court', 'payment'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($facilityId) {
            $query->where('facility_id', $facilityId);
        }

        if ($date) {
            $query->whereDate('booking_date', $date);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        $bookings = $query->paginate(15);

        return view('admin.bookings.index', compact('bookings', 'facilities', 'status', 'facilityId', 'date', 'search'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'facility', 'court', 'payment']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function approve(Booking $booking)
    {
        $booking->update(['status' => 'approved']);
        return back()->with('success', 'Booking #' . $booking->booking_code . ' approved successfully.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $booking->update([
            'status' => 'rejected',
            'cancellation_reason' => $request->reason,
        ]);
        return back()->with('success', 'Booking rejected.');
    }

    public function checkIn(Booking $booking)
    {
        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);
        return back()->with('success', 'Customer checked in successfully for booking #' . $booking->booking_code);
    }

    public function complete(Booking $booking)
    {
        $booking->update(['status' => 'completed']);
        return back()->with('success', 'Booking marked as completed.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,cancelled,completed,checked_in,no_show',
            'reason' => 'nullable|string|max:255',
        ]);

        $booking->status = $validated['status'];
        if (!empty($validated['reason'])) {
            $booking->cancellation_reason = $validated['reason'];
        }
        if ($validated['status'] === 'checked_in' && !$booking->checked_in_at) {
            $booking->checked_in_at = now();
        }
        $booking->save();

        return back()->with('success', 'Booking status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])));
    }
}
