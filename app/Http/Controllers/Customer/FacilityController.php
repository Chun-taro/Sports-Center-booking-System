<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::where('is_active', true)->with('courts');

        if ($request->filled('sport')) {
            $query->where('sport_type', $request->sport);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $facilities = $query->paginate(9);
        $sports = Facility::where('is_active', true)->distinct()->pluck('sport_type');

        return view('customer.facilities.index', compact('facilities', 'sports'));
    }

    public function show(Facility $facility)
    {
        $facility->load(['courts' => function ($q) {
            $q->where('status', 'active');
        }, 'operatingHours']);

        return view('customer.facilities.show', compact('facility'));
    }

    public function calendarEvents(Request $request, Facility $facility)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $query = Booking::where('facility_id', $facility->id)
            ->whereIn('status', ['pending', 'approved', 'checked_in', 'completed'])
            ->with('court');

        if ($start && $end) {
            $query->whereBetween('booking_date', [$start, $end]);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            $startDateTime = $booking->booking_date->format('Y-m-d') . 'T' . $booking->start_time;
            $endDateTime = $booking->booking_date->format('Y-m-d') . 'T' . $booking->end_time;

            return [
                'id' => $booking->id,
                'title' => 'Booked: ' . ($booking->court->name ?? 'Court'),
                'start' => $startDateTime,
                'end' => $endDateTime,
                'backgroundColor' => '#475569', // Privacy-safe sleek dark slate
                'borderColor' => '#334155',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'court_name' => $booking->court->name ?? 'Court',
                    'status' => 'Reserved',
                ]
            ];
        });

        return response()->json($events);
    }
}
