<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
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
}
