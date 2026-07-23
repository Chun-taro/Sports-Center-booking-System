<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Facility;
use App\Models\Holiday;
use App\Models\OperatingHour;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function wizard(Request $request)
    {
        $facilities = Facility::where('is_active', true)->with('courts')->get();
        $selectedFacilityId = $request->input('facility_id', $facilities->first()?->id);
        $selectedFacility = $facilities->firstWhere('id', $selectedFacilityId) ?? $facilities->first();

        return view('customer.bookings.wizard', compact('facilities', 'selectedFacility'));
    }

    public function getCourts(Request $request)
    {
        $facilityId = $request->input('facility_id');
        $courts = Court::where('facility_id', $facilityId)
            ->where('status', 'active')
            ->get(['id', 'name', 'capacity', 'hourly_rate_override']);

        return response()->json([
            'success' => true,
            'courts' => $courts,
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'court_id' => 'required|exists:courts,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $dateStr = $request->date;
        $date = Carbon::parse($dateStr);

        // Check if date is in past
        if ($date->isPast() && !$date->isToday()) {
            return response()->json(['success' => false, 'message' => 'Cannot book dates in the past.'], 422);
        }

        // Check max 30 days future limit
        if ($date->diffInDays(now()->startOfDay()) > 30) {
            return response()->json(['success' => false, 'message' => 'Bookings can only be made up to 30 days in advance.'], 422);
        }

        // Check Holiday
        $isHoliday = Holiday::where('holiday_date', $dateStr)
            ->orWhere(function($q) use ($date) {
                $q->where('is_recurring', true)
                  ->whereMonth('holiday_date', $date->month)
                  ->whereDay('holiday_date', $date->day);
            })->first();

        if ($isHoliday) {
            return response()->json([
                'success' => false,
                'is_closed' => true,
                'message' => 'Facility is closed on this date for Holiday: ' . $isHoliday->name,
                'slots' => []
            ]);
        }

        $facility = Facility::findOrFail($request->facility_id);
        $court = Court::findOrFail($request->court_id);

        // Check operating hours for day of week
        $dayOfWeek = $date->dayOfWeek; // 0 (Sunday) - 6 (Saturday)
        $operatingHour = OperatingHour::where('facility_id', $facility->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if ($operatingHour && $operatingHour->is_closed) {
            return response()->json([
                'success' => false,
                'is_closed' => true,
                'message' => 'Facility is closed on this day of the week.',
                'slots' => []
            ]);
        }

        $openTimeStr = $operatingHour ? $operatingHour->open_time : $facility->open_time;
        $closeTimeStr = $operatingHour ? $operatingHour->close_time : $facility->close_time;

        $openCarbon = Carbon::parse($dateStr . ' ' . $openTimeStr);
        $closeCarbon = Carbon::parse($dateStr . ' ' . $closeTimeStr);

        // Get existing bookings for court on this date
        $existingBookings = Booking::where('court_id', $court->id)
            ->where('booking_date', $dateStr)
            ->whereIn('status', ['pending', 'approved', 'checked_in', 'completed'])
            ->get(['start_time', 'end_time', 'status']);

        $slots = [];
        $currentSlot = $openCarbon->copy();

        while ($currentSlot->lt($closeCarbon)) {
            $slotStart = $currentSlot->format('H:i:s');
            $slotEnd = $currentSlot->copy()->addHour()->format('H:i:s');
            $slotLabel = $currentSlot->format('g:i A') . ' - ' . $currentSlot->copy()->addHour()->format('g:i A');

            // If date is today, disable past time slots
            $isPastSlot = false;
            if ($date->isToday() && $currentSlot->isBefore(now())) {
                $isPastSlot = true;
            }

            // Check overlap
            $isBooked = false;
            foreach ($existingBookings as $b) {
                if ($b->start_time < $slotEnd && $b->end_time > $slotStart) {
                    $isBooked = true;
                    break;
                }
            }

            $slots[] = [
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
                'label' => $slotLabel,
                'available' => !$isBooked && !$isPastSlot,
                'reason' => $isBooked ? 'Booked' : ($isPastSlot ? 'Past Time' : 'Available'),
            ];

            $currentSlot->addHour();
        }

        $hourlyRate = $court->hourly_rate_override ?? $facility->hourly_rate;

        return response()->json([
            'success' => true,
            'is_closed' => false,
            'hourly_rate' => (float)$hourlyRate,
            'slots' => $slots,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'court_id' => 'required|exists:courts,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required',
            'payment_method' => 'required|in:cash,gcash,maya,credit_card',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $facility = Facility::findOrFail($request->facility_id);
        $court = Court::findOrFail($request->court_id);

        $bookingDate = $request->booking_date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        // Double check availability
        if (!$court->isAvailableOnSlot($bookingDate, $startTime, $endTime)) {
            return back()->withInput()->with('error', 'The selected court and time slot is no longer available. Please select another slot.');
        }

        $startCarbon = Carbon::parse($bookingDate . ' ' . $startTime);
        $endCarbon = Carbon::parse($bookingDate . ' ' . $endTime);
        $durationHours = max(1, $startCarbon->diffInMinutes($endCarbon) / 60);

        $hourlyRate = $court->hourly_rate_override ?? $facility->hourly_rate;
        $subtotal = $hourlyRate * $durationHours;
        $taxAmount = 0.00; // Tax or service fee if applicable
        $totalAmount = $subtotal + $taxAmount;

        $bookingCode = 'SB-' . date('Ymd', strtotime($bookingDate)) . '-' . strtoupper(Str::random(4));

        DB::transaction(function () use ($user, $facility, $court, $bookingDate, $startTime, $endTime, $durationHours, $hourlyRate, $subtotal, $taxAmount, $totalAmount, $bookingCode, $request, &$booking) {
            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'user_id' => $user->id,
                'facility_id' => $facility->id,
                'court_id' => $court->id,
                'booking_date' => $bookingDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_hours' => $durationHours,
                'hourly_rate' => $hourlyRate,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash' ? 'unpaid' : 'paid',
                'reference_number' => $request->payment_method !== 'cash' ? 'PAY-' . strtoupper(Str::random(8)) : null,
                'paid_at' => $request->payment_method !== 'cash' ? now() : null,
            ]);
        });

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', 'Booking reservation submitted successfully! Booking Code: ' . $bookingCode);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = Booking::where('user_id', $user->id)
            ->with(['facility', 'court', 'payment'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(10);

        return view('customer.bookings.index', compact('bookings', 'status'));
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['facility', 'court', 'payment', 'user']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'approved'])) {
            return back()->with('error', 'This booking cannot be cancelled in its current state.');
        }

        // Cutoff check: 24 hours before booking date/time
        $bookingDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);
        if (now()->diffInHours($bookingDateTime, false) < 24 && !$booking->status === 'pending') {
            return back()->with('error', 'Bookings can only be cancelled at least 24 hours in advance.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->reason,
        ]);

        if ($booking->payment) {
            $booking->payment->update([
                'payment_status' => $booking->payment->payment_status === 'paid' ? 'refunded' : 'unpaid'
            ]);
        }

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
