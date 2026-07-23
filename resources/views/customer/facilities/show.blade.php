@extends('layouts.app')

@section('title', $facility->name . ' - ApexSports Hub')

@section('content')
<div class="bg-dark text-white py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container py-3">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('facilities.index') }}" class="text-secondary text-decoration-none small"><i class="fa-solid fa-arrow-left me-1"></i> Back to Facilities</a>
            <span class="text-secondary">/</span>
            <span class="badge-sport bg-success text-white">{{ ucfirst(str_replace('_', ' ', $facility->sport_type)) }}</span>
        </div>
        <h1 class="display-5 font-heading fw-bold mb-2">{{ $facility->name }}</h1>
        <p class="text-secondary mb-0"><i class="fa-regular fa-clock me-1 text-success"></i> Open Daily: {{ date('g:i A', strtotime($facility->open_time)) }} – {{ date('g:i A', strtotime($facility->close_time)) }}</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">
            <!-- Main Facility Card -->
            <div class="glass-card overflow-hidden mb-4">
                <img src="{{ $facility->image_url ?? 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=1200&q=80' }}" class="w-100 object-fit-cover" style="max-height: 420px;" alt="{{ $facility->name }}">
                <div class="p-4">
                    <h3 class="font-heading fw-bold mb-3">Facility Overview</h3>
                    <p class="text-secondary fs-6 lh-lg mb-4">{{ $facility->description }}</p>

                    <h4 class="font-heading fw-bold mb-3">Courts Included</h4>
                    <div class="row g-3">
                        @foreach($facility->courts as $court)
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="font-heading fw-bold mb-1 text-dark">{{ $court->name }}</h6>
                                        <span class="small text-muted"><i class="fa-solid fa-users me-1"></i> Capacity: {{ $court->capacity }} Players</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-success d-block">
                                            ${{ number_format($court->hourly_rate_override ?? $facility->hourly_rate, 2) }}/hr
                                        </span>
                                        <span class="badge bg-success-subtle text-success small">Active</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar CTA & Schedule -->
        <div class="col-lg-4">
            <div class="glass-card p-4 sticky-top" style="top: 90px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small text-uppercase fw-semibold">Base Rate</span>
                    <h3 class="font-heading fw-bold text-primary mb-0">${{ number_format($facility->hourly_rate, 2) }} <span class="fs-6 text-muted font-body fw-normal">/ hour</span></h3>
                </div>

                <a href="{{ route('customer.bookings.wizard', ['facility_id' => $facility->id]) }}" class="btn btn-accent btn-lg w-100 rounded-pill py-3 font-heading fw-bold mb-4">
                    Proceed to Booking <i class="fa-solid fa-calendar-check ms-2"></i>
                </a>

                <h5 class="font-heading fw-bold mb-3 border-top pt-3">Operating Hours</h5>
                <ul class="list-unstyled mb-0">
                    @php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
                    @foreach($facility->operatingHours as $oh)
                        <li class="d-flex justify-content-between py-2 border-bottom small">
                            <span class="fw-semibold text-secondary">{{ $days[$oh->day_of_week] }}</span>
                            @if($oh->is_closed)
                                <span class="badge bg-danger">Closed</span>
                            @else
                                <span class="text-dark fw-bold">{{ date('g:i A', strtotime($oh->open_time)) }} - {{ date('g:i A', strtotime($oh->close_time)) }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
