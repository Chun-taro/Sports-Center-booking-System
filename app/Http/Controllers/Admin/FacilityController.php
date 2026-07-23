<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\OperatingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::withCount('courts')->orderBy('name')->paginate(10);
        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('admin.facilities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:500',
            'hourly_rate' => 'required|numeric|min:0',
            'open_time' => 'required',
            'close_time' => 'required',
            'max_players' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['is_active'] = $request->has('is_active');

        $facility = Facility::create($validated);

        // Create default operating hours for 7 days
        for ($day = 0; $day < 7; $day++) {
            OperatingHour::create([
                'facility_id' => $facility->id,
                'day_of_week' => $day,
                'open_time' => $validated['open_time'],
                'close_time' => $validated['close_time'],
                'is_closed' => false,
            ]);
        }

        return redirect()->route('admin.facilities.index')->with('success', 'Facility created successfully.');
    }

    public function edit(Facility $facility)
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:500',
            'hourly_rate' => 'required|numeric|min:0',
            'open_time' => 'required',
            'close_time' => 'required',
            'max_players' => 'required|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $facility->update($validated);

        return redirect()->route('admin.facilities.index')->with('success', 'Facility updated successfully.');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();
        return redirect()->route('admin.facilities.index')->with('success', 'Facility deleted successfully.');
    }
}
