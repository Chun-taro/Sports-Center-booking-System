<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Facility;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Counter Cards
        $totalUsers = User::where('role', 'customer')->count();
        $totalBookings = Booking::count();
        $todaysBookings = Booking::whereDate('booking_date', $today)->count();
        
        $monthlyRevenue = Payment::where('payment_status', 'paid')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        $pendingBookings = Booking::where('status', 'pending')->count();
        $approvedBookings = Booking::where('status', 'approved')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        $totalCourtsCount = Court::where('status', 'active')->count();
        $occupiedCourtsCount = Booking::whereDate('booking_date', $today)
            ->whereIn('status', ['approved', 'checked_in'])
            ->where('start_time', '<=', now()->toTimeString())
            ->where('end_time', '>', now()->toTimeString())
            ->distinct('court_id')
            ->count('court_id');
        $availableCourtsCount = max(0, $totalCourtsCount - $occupiedCourtsCount);

        // Chart 1: Monthly Bookings (Last 6 Months)
        $months = [];
        $monthlyBookingsData = [];
        $monthlyRevenueData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $count = Booking::whereMonth('booking_date', $date->month)
                ->whereYear('booking_date', $date->year)
                ->count();
            $monthlyBookingsData[] = $count;

            $rev = Payment::where('payment_status', 'paid')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount');
            $monthlyRevenueData[] = (float)$rev;
        }

        // Chart 3: Facility Utilization (Bookings per Facility)
        $facilityUtilization = Facility::withCount(['bookings' => function($q) {
            $q->whereIn('status', ['approved', 'completed', 'checked_in']);
        }])->get();

        $facilityLabels = $facilityUtilization->pluck('name')->toArray();
        $facilityData = $facilityUtilization->pluck('bookings_count')->toArray();

        // Chart 4: Popular Sports
        $popularSports = Facility::select('sport_type', DB::raw('count(bookings.id) as total'))
            ->leftJoin('bookings', 'facilities.id', '=', 'bookings.facility_id')
            ->groupBy('sport_type')
            ->get();

        $sportLabels = $popularSports->pluck('sport_type')->map(fn($s) => ucfirst($s))->toArray();
        $sportData = $popularSports->pluck('total')->toArray();

        // Chart 5: Booking Trends (Last 7 Days)
        $last7Days = [];
        $dailyBookingsData = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $last7Days[] = $day->format('D, M j');
            $c = Booking::whereDate('booking_date', $day->toDateString())->count();
            $dailyBookingsData[] = $c;
        }

        // Recent Bookings List
        $recentBookings = Booking::with(['user', 'facility', 'court', 'payment'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalBookings',
            'todaysBookings',
            'monthlyRevenue',
            'pendingBookings',
            'approvedBookings',
            'cancelledBookings',
            'availableCourtsCount',
            'occupiedCourtsCount',
            'months',
            'monthlyBookingsData',
            'monthlyRevenueData',
            'facilityLabels',
            'facilityData',
            'sportLabels',
            'sportData',
            'last7Days',
            'dailyBookingsData',
            'recentBookings'
        ));
    }
}
