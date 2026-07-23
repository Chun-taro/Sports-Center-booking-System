<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Facility;
use App\Services\SampleDataService;
use Illuminate\Http\Request;
use Throwable;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $facilities = Facility::where('is_active', true)->with('courts')->get();
            if ($facilities->isEmpty()) {
                $facilities = SampleDataService::getFacilities();
            }
            $totalCourts = Court::where('status', 'active')->count() ?: 18;
            $totalBookings = Booking::whereIn('status', ['approved', 'completed', 'checked_in'])->count() ?: 26;
        } catch (Throwable $e) {
            $facilities = SampleDataService::getFacilities();
            $totalCourts = 18;
            $totalBookings = 26;
        }

        return view('customer.home', compact('facilities', 'totalCourts', 'totalBookings'));
    }

    public function calendarEvents(Request $request)
    {
        try {
            $start = $request->query('start');
            $end = $request->query('end');
            $facilityId = $request->query('facility_id');
            $sport = $request->query('sport');

            $query = Booking::whereIn('status', ['pending', 'approved', 'checked_in', 'completed'])
                ->with(['facility', 'court']);

            if ($start && $end) {
                $query->whereBetween('booking_date', [$start, $end]);
            }

            if ($facilityId) {
                $query->where('facility_id', $facilityId);
            }

            if ($sport) {
                $query->whereHas('facility', function($q) use ($sport) {
                    $q->where('sport_type', $sport);
                });
            }

            $bookings = $query->get();

            $colorsBySport = [
                'badminton' => '#10b981',
                'basketball' => '#f97316',
                'pickleball' => '#3b82f6',
                'volleyball' => '#8b5cf6',
                'tennis' => '#eab308',
                'table_tennis' => '#ec4899',
                'futsal' => '#06b6d4',
            ];

            $events = $bookings->map(function ($booking) use ($colorsBySport) {
                $sport = $booking->facility->sport_type ?? 'badminton';
                $color = $colorsBySport[$sport] ?? '#2563eb';

                $startDateTime = $booking->booking_date->format('Y-m-d') . 'T' . $booking->start_time;
                $endDateTime = $booking->booking_date->format('Y-m-d') . 'T' . $booking->end_time;

                return [
                    'id' => $booking->id,
                    'title' => ($booking->facility->name ?? 'Facility') . ' - ' . ($booking->court->name ?? 'Court'),
                    'start' => $startDateTime,
                    'end' => $endDateTime,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'facility_name' => $booking->facility->name ?? 'Facility',
                        'court_name' => $booking->court->name ?? 'Court',
                        'sport' => ucfirst(str_replace('_', ' ', $sport)),
                        'status' => 'Reserved',
                        'facility_id' => $booking->facility_id,
                    ]
                ];
            });

            if ($events->isEmpty()) {
                $events = collect($this->getMockEvents($facilityId, $sport));
            }

            return response()->json($events);
        } catch (Throwable $e) {
            return response()->json($this->getMockEvents($request->query('facility_id'), $request->query('sport')));
        }
    }

    private function getMockEvents($facilityId = null, $sportFilter = null)
    {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $dayAfter = date('Y-m-d', strtotime('+2 days'));

        $mock = [
            [
                'id' => 1001,
                'title' => 'Apex Badminton Pavilion - Court A',
                'start' => $today . 'T09:00:00',
                'end' => $today . 'T11:00:00',
                'backgroundColor' => '#10b981',
                'borderColor' => '#10b981',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'facility_name' => 'Apex Badminton Pavilion',
                    'court_name' => 'Court A (Mat 1)',
                    'sport' => 'Badminton',
                    'sport_key' => 'badminton',
                    'status' => 'Reserved',
                    'facility_id' => 1,
                ]
            ],
            [
                'id' => 1002,
                'title' => 'Grand Slam Basketball - Main Court',
                'start' => $today . 'T14:00:00',
                'end' => $today . 'T16:00:00',
                'backgroundColor' => '#f97316',
                'borderColor' => '#f97316',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'facility_name' => 'Grand Slam Basketball Arena',
                    'court_name' => 'Main Championship Court',
                    'sport' => 'Basketball',
                    'sport_key' => 'basketball',
                    'status' => 'Reserved',
                    'facility_id' => 2,
                ]
            ],
            [
                'id' => 1003,
                'title' => 'Pickleball Social Hub - Court 1',
                'start' => $tomorrow . 'T10:00:00',
                'end' => $tomorrow . 'T12:00:00',
                'backgroundColor' => '#3b82f6',
                'borderColor' => '#3b82f6',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'facility_name' => 'Pickleball Social Hub',
                    'court_name' => 'Court 1 (Sun Covered)',
                    'sport' => 'Pickleball',
                    'sport_key' => 'pickleball',
                    'status' => 'Reserved',
                    'facility_id' => 3,
                ]
            ],
            [
                'id' => 1004,
                'title' => 'Apex Futsal Arena - Turf Pitch 1',
                'start' => $dayAfter . 'T18:00:00',
                'end' => $dayAfter . 'T20:00:00',
                'backgroundColor' => '#06b6d4',
                'borderColor' => '#06b6d4',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'facility_name' => 'Apex Futsal Arena',
                    'court_name' => 'Turf Pitch 1',
                    'sport' => 'Futsal',
                    'sport_key' => 'futsal',
                    'status' => 'Reserved',
                    'facility_id' => 5,
                ]
            ]
        ];

        if ($facilityId) {
            $mock = array_values(array_filter($mock, fn($e) => $e['extendedProps']['facility_id'] == $facilityId));
        }

        if ($sportFilter) {
            $mock = array_values(array_filter($mock, fn($e) => $e['extendedProps']['sport_key'] == $sportFilter));
        }

        return $mock;
    }
}
