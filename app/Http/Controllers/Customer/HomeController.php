<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Court;
use App\Models\Booking;

class HomeController extends Controller
{
    public function index()
    {
        $facilities = Facility::where('is_active', true)->with('courts')->get();
        $totalCourts = Court::where('status', 'active')->count();
        $totalBookings = Booking::whereIn('status', ['approved', 'completed', 'checked_in'])->count();

        return view('customer.home', compact('facilities', 'totalCourts', 'totalBookings'));
    }
}
