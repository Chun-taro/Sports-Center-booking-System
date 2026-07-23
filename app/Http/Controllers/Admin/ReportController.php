<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Total Metrics in range
        $totalBookingsInRange = Booking::whereBetween('booking_date', [$startDate, $endDate])->count();
        $totalRevenueInRange = Payment::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');
        
        $mostBookedFacility = Facility::withCount(['bookings' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('booking_date', [$startDate, $endDate]);
        }])->orderBy('bookings_count', 'desc')->first();

        // Facility Usage matrix
        $facilityUsage = Facility::withCount(['bookings' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('booking_date', [$startDate, $endDate]);
        }])->get();

        // Peak Hours Matrix
        $peakHours = Booking::select('start_time', DB::raw('count(*) as count'))
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->groupBy('start_time')
            ->orderBy('count', 'desc')
            ->take(8)
            ->get();

        // Customer Stats
        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')
            ->whereHas('bookings', function($q) use ($startDate, $endDate) {
                $q->whereBetween('booking_date', [$startDate, $endDate]);
            })->count();

        // Detailed Bookings list in range
        $bookingsList = Booking::with(['user', 'facility', 'court', 'payment'])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->orderBy('booking_date', 'desc')
            ->paginate(15);

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'totalBookingsInRange',
            'totalRevenueInRange',
            'mostBookedFacility',
            'facilityUsage',
            'peakHours',
            'totalCustomers',
            'activeCustomers',
            'bookingsList'
        ));
    }

    public function exportCsv(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $bookings = Booking::with(['user', 'facility', 'court', 'payment'])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->orderBy('booking_date', 'asc')
            ->get();

        $fileName = 'bookings_report_' . $startDate . '_to_' . $endDate . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Booking Code', 'Customer Name', 'Email', 'Facility', 'Court', 'Date', 'Start Time', 'End Time', 'Amount', 'Status', 'Payment Method', 'Payment Status'];

        $callback = function() use($bookings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_code,
                    $b->user->name ?? 'N/A',
                    $b->user->email ?? 'N/A',
                    $b->facility->name ?? 'N/A',
                    $b->court->name ?? 'N/A',
                    $b->booking_date->format('Y-m-d'),
                    $b->start_time,
                    $b->end_time,
                    number_format($b->total_amount, 2),
                    ucfirst(str_replace('_', ' ', $b->status)),
                    ucfirst($b->payment->payment_method ?? 'N/A'),
                    ucfirst($b->payment->payment_status ?? 'N/A'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
