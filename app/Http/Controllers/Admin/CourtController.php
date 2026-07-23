<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Facility;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    public function index(Request $request)
    {
        $facilityId = $request->query('facility_id');
        $facilities = Facility::orderBy('name')->get();

        $query = Court::with('facility')->orderBy('facility_id')->orderBy('name');
        if ($facilityId) {
            $query->where('facility_id', $facilityId);
        }

        $courts = $query->paginate(15);

        return view('admin.courts.index', compact('courts', 'facilities', 'facilityId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'hourly_rate_override' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        Court::create($validated);

        return back()->with('success', 'Court added successfully.');
    }

    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'hourly_rate_override' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $court->update($validated);

        return back()->with('success', 'Court updated successfully.');
    }

    public function destroy(Court $court)
    {
        $court->delete();
        return back()->with('success', 'Court removed successfully.');
    }
}
