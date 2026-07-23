<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Facility;
use App\Services\SampleDataService;
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
}
