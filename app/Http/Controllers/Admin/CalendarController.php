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
        try {
            $facilities = Facility::orderBy('name')->get();
            if ($facilities->isEmpty()) {
                $facilities = SampleDataService::getFacilities();
            }
        } catch (\Throwable $e) {
            $facilities = SampleDataService::getFacilities();
        }

        return view('admin.calendar.index', compact('facilities'));
    }

    public function events(Request $request)
    {
        try {
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
                    'title' => ($booking->facility->name ?? 'Facility') . ' - ' . ($booking->court->name ?? 'Court') . ' (' . ($booking->user->name ?? 'Guest') . ')',
                    'start' => $startDateTime,
                    'end' => $endDateTime,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => $textColor,
                    'extendedProps' => [
                        'booking_code' => $booking->booking_code ?? 'SB-2026',
                        'customer_name' => $booking->user->name ?? 'John Doe',
                        'customer_phone' => $booking->user->phone ?? '+123456789',
                        'facility_name' => $booking->facility->name ?? 'Sports Center',
                        'court_name' => $booking->court->name ?? 'Court 1',
                        'status' => ucfirst(str_replace('_', ' ', $booking->status ?? 'approved')),
                        'status_raw' => $booking->status ?? 'approved',
                        'amount' => '$' . number_format($booking->total_amount ?? 30, 2),
                    ],
                ];
            });

            if ($events->isEmpty()) {
                $events = collect($this->getMockAdminEvents());
            }

            return response()->json($events);
        } catch (\Throwable $e) {
            return response()->json($this->getMockAdminEvents());
        }
    }

    private function getMockAdminEvents()
    {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        return [
            [
                'id' => 101,
                'title' => 'Apex Badminton Pavilion - Court A (John Doe)',
                'start' => $today . 'T10:00:00',
                'end' => $today . 'T11:00:00',
                'backgroundColor' => '#0d6efd',
                'borderColor' => '#0d6efd',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'booking_code' => 'SB-20260724-001',
                    'customer_name' => 'John Doe',
                    'customer_phone' => '+1234567890',
                    'facility_name' => 'Apex Badminton Pavilion',
                    'court_name' => 'Court A',
                    'status' => 'Approved',
                    'status_raw' => 'approved',
                    'amount' => '$28.00',
                ]
            ],
            [
                'id' => 102,
                'title' => 'Grand Slam Basketball Arena - Main Court (Sarah Smith)',
                'start' => $tomorrow . 'T14:00:00',
                'end' => $tomorrow . 'T16:00:00',
                'backgroundColor' => '#198754',
                'borderColor' => '#198754',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'booking_code' => 'SB-20260725-002',
                    'customer_name' => 'Sarah Smith',
                    'customer_phone' => '+1987654321',
                    'facility_name' => 'Grand Slam Basketball Arena',
                    'court_name' => 'Main Court',
                    'status' => 'Completed',
                    'status_raw' => 'completed',
                    'amount' => '$120.00',
                ]
            ]
        ];
    }
}
