<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $facilities = Facility::orderBy('name')->get();
        return view('admin.calendar.index', compact('facilities'));
    }

    public function events(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $facilityId = $request->query('facility_id');
        $status = $request->query('status');

        $query = Booking::with(['user', 'facility', 'court']);

        if ($start && $end) {
            $query->whereBetween('booking_date', [$start, $end]);
        }

        if ($facilityId) {
            $query->where('facility_id', $facilityId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            $color = match ($booking->status) {
                'pending' => '#ffc107',
                'approved' => '#0d6efd',
                'checked_in' => '#0dcaf0',
                'completed' => '#198754',
                'cancelled' => '#6c757d',
                'rejected', 'no_show' => '#dc3545',
                default => '#212529',
            };

            $textColor = in_array($booking->status, ['pending', 'checked_in']) ? '#000000' : '#ffffff';

            $startDateTime = $booking->booking_date->format('Y-m-d') . 'T' . $booking->start_time;
            $endDateTime = $booking->booking_date->format('Y-m-d') . 'T' . $booking->end_time;

            return [
                'id' => $booking->id,
                'title' => $booking->facility->name . ' - ' . $booking->court->name . ' (' . ($booking->user->name ?? 'Guest') . ')',
                'start' => $startDateTime,
                'end' => $endDateTime,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => $textColor,
                'extendedProps' => [
                    'booking_code' => $booking->booking_code,
                    'customer_name' => $booking->user->name ?? 'N/A',
                    'customer_phone' => $booking->user->phone ?? 'N/A',
                    'facility_name' => $booking->facility->name,
                    'court_name' => $booking->court->name,
                    'status' => ucfirst(str_replace('_', ' ', $booking->status)),
                    'status_raw' => $booking->status,
                    'amount' => '$' . number_format($booking->total_amount, 2),
                ],
            ];
        });

        return response()->json($events);
    }
}
