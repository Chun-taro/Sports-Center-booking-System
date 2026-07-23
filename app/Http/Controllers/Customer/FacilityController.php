<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Services\SampleDataService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        try {
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
            if ($facilities->isEmpty()) {
                $facilities = $this->getStaticPaginator($request);
            }
            $sports = Facility::where('is_active', true)->distinct()->pluck('sport_type');
            if ($sports->isEmpty()) {
                $sports = collect(['badminton', 'basketball', 'pickleball', 'tennis', 'futsal']);
            }
        } catch (Throwable $e) {
            $facilities = $this->getStaticPaginator($request);
            $sports = collect(['badminton', 'basketball', 'pickleball', 'tennis', 'futsal']);
        }

        return view('customer.facilities.index', compact('facilities', 'sports'));
    }

    public function show(string $slug)
    {
        try {
            $facility = Facility::where('slug', $slug)
                ->with(['courts' => function ($q) {
                    $q->where('status', 'active');
                }, 'operatingHours'])
                ->first();

            if (! $facility) {
                $facility = SampleDataService::getFacilities()->firstWhere('slug', $slug) ?? SampleDataService::getFacilities()->first();
            }
        } catch (Throwable $e) {
            $facility = SampleDataService::getFacilities()->firstWhere('slug', $slug) ?? SampleDataService::getFacilities()->first();
        }

        return view('customer.facilities.show', compact('facility'));
    }

    public function calendarEvents(Request $request, string $slug)
    {
        try {
            $facility = Facility::where('slug', $slug)->first();
            $facilityId = $facility ? $facility->id : 1;

            $start = $request->query('start');
            $end = $request->query('end');

            $query = Booking::where('facility_id', $facilityId)
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
                    'backgroundColor' => '#475569',
                    'borderColor' => '#334155',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'court_name' => $booking->court->name ?? 'Court',
                        'status' => 'Reserved',
                    ]
                ];
            });

            return response()->json($events);
        } catch (Throwable $e) {
            return response()->json([
                [
                    'id' => 101,
                    'title' => 'Booked: Court A',
                    'start' => date('Y-m-d') . 'T10:00:00',
                    'end' => date('Y-m-d') . 'T11:00:00',
                    'backgroundColor' => '#475569',
                    'borderColor' => '#334155',
                    'textColor' => '#ffffff',
                ],
                [
                    'id' => 102,
                    'title' => 'Booked: Court B',
                    'start' => date('Y-m-d') . 'T14:00:00',
                    'end' => date('Y-m-d') . 'T15:00:00',
                    'backgroundColor' => '#475569',
                    'borderColor' => '#334155',
                    'textColor' => '#ffffff',
                ]
            ]);
        }
    }

    private function getStaticPaginator(Request $request)
    {
        $all = SampleDataService::getFacilities();
        if ($request->filled('sport')) {
            $all = $all->where('sport_type', $request->sport);
        }
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $all = $all->filter(fn($f) => str_contains(strtolower($f->name), $search) || str_contains(strtolower($f->description), $search));
        }

        return new LengthAwarePaginator($all, $all->count(), 9, 1, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }
}
