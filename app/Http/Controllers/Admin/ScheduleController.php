<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Holiday;
use App\Models\OperatingHour;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $facilities = Facility::with('operatingHours')->orderBy('name')->get();
        $holidays = Holiday::orderBy('holiday_date', 'asc')->get();

        return view('admin.schedules.index', compact('facilities', 'holidays'));
    }

    public function updateOperatingHours(Request $request, Facility $facility)
    {
        $request->validate([
            'hours' => 'required|array',
            'hours.*.open_time' => 'required',
            'hours.*.close_time' => 'required',
        ]);

        foreach ($request->hours as $dayOfWeek => $data) {
            OperatingHour::updateOrCreate(
                [
                    'facility_id' => $facility->id,
                    'day_of_week' => $dayOfWeek,
                ],
                [
                    'open_time' => $data['open_time'],
                    'close_time' => $data['close_time'],
                    'is_closed' => isset($data['is_closed']),
                ]
            );
        }

        return back()->with('success', 'Operating hours updated for ' . $facility->name);
    }

    public function storeHoliday(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date',
            'is_recurring' => 'nullable|boolean',
            'note' => 'nullable|string|max:500',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');

        Holiday::create($validated);

        return back()->with('success', 'Holiday blackout date added successfully.');
    }

    public function destroyHoliday(Holiday $holiday)
    {
        $holiday->delete();
        return back()->with('success', 'Holiday deleted successfully.');
    }
}
