@extends('layouts.admin')

@section('title', 'Operating Hours & Holidays - ApexSports Hub')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="font-heading fw-bold mb-1">Operating Hours & Holiday Schedule</h2>
        <p class="text-secondary small mb-0">Configure daily opening/closing hours per venue and manage blackout holiday dates.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4 font-heading fw-bold mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
        <i class="fa-solid fa-plus me-1"></i> Add Holiday Blackout Date
    </button>
</div>

<div class="row g-4">
    <!-- Facility Operating Hours Form Grid -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
            <h4 class="font-heading fw-bold mb-4">Facility Operating Hours</h4>

            @foreach($facilities as $facility)
                <div class="border rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-heading fw-bold text-dark mb-0">{{ $facility->name }}</h5>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">{{ ucfirst($facility->sport_type) }}</span>
                    </div>

                    <form action="{{ route('admin.schedules.operating-hours', $facility->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-3">
                                <thead>
                                    <tr class="text-secondary small text-uppercase">
                                        <th>Day of Week</th>
                                        <th>Open Time</th>
                                        <th>Close Time</th>
                                        <th class="text-center">Is Closed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
                                    @for($day = 0; $day < 7; $day++)
                                        @php
                                            $oh = $facility->operatingHours->firstWhere('day_of_week', $day);
                                        @endphp
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $days[$day] }}</td>
                                            <td>
                                                <input type="time" name="hours[{{ $day }}][open_time]" class="form-control form-control-sm" value="{{ $oh->open_time ?? $facility->open_time }}">
                                            </td>
                                            <td>
                                                <input type="time" name="hours[{{ $day }}][close_time]" class="form-control form-control-sm" value="{{ $oh->close_time ?? $facility->close_time }}">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="hours[{{ $day }}][is_closed]" class="form-check-input" value="1" {{ ($oh->is_closed ?? false) ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">Update Hours</button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Holidays Blackout Dates Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 90px;">
            <h5 class="font-heading fw-bold mb-3">Holidays & Blackout Dates</h5>
            <p class="text-muted small mb-3">The booking wizard automatically blocks user reservations on configured holiday dates.</p>

            <ul class="list-group list-group-flush mb-3">
                @forelse($holidays as $h)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <div>
                            <div class="fw-bold text-dark">{{ $h->name }}</div>
                            <div class="small text-muted"><i class="fa-regular fa-calendar me-1 text-danger"></i> {{ $h->holiday_date->format('M d, Y') }}</div>
                            @if($h->note) <div class="small text-secondary italic">{{ $h->note }}</div> @endif
                        </div>
                        <form action="{{ route('admin.schedules.holidays.destroy', $h->id) }}" method="POST" onsubmit="return confirm('Remove holiday?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-muted text-center py-4 px-0">No holiday blackout dates defined.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Add Holiday Modal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.schedules.holidays.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-heading fw-bold">Add Holiday Blackout Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Holiday Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. National Sports Holiday" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Holiday Date</label>
                        <input type="date" name="holiday_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Note / Description</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Facility maintenance or holiday note..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary font-heading fw-bold">Save Holiday</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
