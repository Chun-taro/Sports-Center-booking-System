@extends('layouts.app')

@section('title', 'ApexSports Hub - World-Class Sports Facility Booking')

@section('content')
<!-- Hero Banner -->
<section class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f172a 100%); color: #fff;">
    <div class="position-absolute" style="top: -100px; right: -100px; width: 400px; height: 400px; background: rgba(16, 185, 129, 0.15); filter: blur(100px); border-radius: 50%;"></div>
    <div class="position-absolute" style="bottom: -100px; left: -100px; width: 400px; height: 400px; background: rgba(37, 99, 235, 0.15); filter: blur(100px); border-radius: 50%;"></div>

    <div class="container py-lg-5 position-relative" style="z-index: 2;">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-30 rounded-pill px-3 py-2 mb-3 fw-semibold">
                    <i class="fa-solid fa-fire-flame-curved me-1"></i> #1 Premier Sports Reservation Platform
                </span>
                <h1 class="display-3 fw-extrabold font-heading text-white lh-sm mb-3">
                    Reserve Premium <br><span style="background: linear-gradient(90deg, #10b981, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sports Facilities</span> Effortlessly
                </h1>
                <p class="lead text-secondary mb-4 opacity-90 fs-5" style="max-width: 580px;">
                    Instant real-time court availability, transparent pricing, and instant booking confirmation for Badminton, Basketball, Pickleball, Tennis, Futsal & more.
                </p>
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <a href="{{ route('customer.bookings.wizard') }}" class="btn btn-accent btn-lg rounded-pill px-4 py-3 font-heading fs-6">
                        Book a Court Now <i class="fa-solid fa-arrow-right-long ms-2"></i>
                    </a>
                    <a href="{{ route('facilities.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3 font-heading fs-6">
                        Explore Facilities
                    </a>
                </div>

                <!-- Stats Strip -->
                <div class="row g-4 mt-4 pt-4 border-top border-secondary border-opacity-25">
                    <div class="col-4">
                        <h3 class="fw-bold font-heading text-white mb-0">{{ $facilities->count() }}+</h3>
                        <p class="small text-secondary mb-0">Sports Venues</p>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold font-heading text-white mb-0">{{ $totalCourts }}+</h3>
                        <p class="small text-secondary mb-0">Courts Available</p>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold font-heading text-white mb-0">{{ $totalBookings }}+</h3>
                        <p class="small text-secondary mb-0">Bookings Made</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="position-relative">
                    <div class="rounded-4 overflow-hidden shadow-lg border border-secondary border-opacity-25" style="transform: rotate(2deg);">
                        <img src="https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=800&q=80" alt="Sports Center" class="img-fluid w-100" style="height: 380px; object-fit: cover;">
                    </div>
                    <div class="glass-card position-absolute bg-dark text-white border-0 shadow-lg p-3 rounded-4" style="bottom: -20px; left: -20px; max-width: 260px; background: rgba(15, 23, 42, 0.9) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-success text-white p-3 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-circle-check fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 font-heading text-white">Instant Availability</h6>
                                <p class="small text-secondary mb-0" style="font-size: 0.75rem;">Zero double booking guarantee</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sports Categories Section -->
<section class="py-5 bg-light" id="sports">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-semibold mb-2">Sports Offerings</span>
            <h2 class="font-heading fw-bold">Select Your Sport</h2>
            <p class="text-secondary">Discover venues tailored for your favorite competitive & casual games</p>
        </div>

        <div class="row g-4">
            @php
                $sportsGrid = [
                    ['name' => 'Badminton', 'type' => 'badminton', 'icon' => 'fa-feather-pointed', 'color' => '#10b981'],
                    ['name' => 'Basketball', 'type' => 'basketball', 'icon' => 'fa-basketball', 'color' => '#f97316'],
                    ['name' => 'Pickleball', 'type' => 'pickleball', 'icon' => 'fa-table-tennis-paddle-ball', 'color' => '#3b82f6'],
                    ['name' => 'Volleyball', 'type' => 'volleyball', 'icon' => 'fa-volleyball', 'color' => '#8b5cf6'],
                    ['name' => 'Tennis', 'type' => 'tennis', 'icon' => 'fa-baseball', 'color' => '#eab308'],
                    ['name' => 'Table Tennis', 'type' => 'table_tennis', 'icon' => 'fa-ping-pong-paddle', 'color' => '#ec4899'],
                    ['name' => 'Futsal', 'type' => 'futsal', 'icon' => 'fa-futbol', 'color' => '#06b6d4'],
                ];
            @endphp

            @foreach($sportsGrid as $sport)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('facilities.index', ['sport' => $sport['type']]) }}" class="text-decoration-none">
                        <div class="glass-card p-4 text-center h-100">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px; background: {{ $sport['color'] }}15; color: {{ $sport['color'] }};">
                                <i class="fa-solid {{ $sport['icon'] }} fs-3"></i>
                            </div>
                            <h5 class="font-heading fw-bold text-dark mb-1">{{ $sport['name'] }}</h5>
                            <span class="small text-muted">Browse Courts <i class="fa-solid fa-chevron-right ms-1" style="font-size:0.7rem;"></i></span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Facilities -->
<section class="py-5">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
            <div>
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-semibold mb-2">Featured Venues</span>
                <h2 class="font-heading fw-bold mb-0">Featured Facilities</h2>
            </div>
            <a href="{{ route('facilities.index') }}" class="btn btn-outline-primary rounded-pill px-4 mt-3 mt-md-0 fw-semibold">View All Facilities</a>
        </div>

        <div class="row g-4">
            @foreach($facilities as $facility)
                <div class="col-md-6 col-lg-4">
                    <div class="glass-card h-100 overflow-hidden d-flex flex-column">
                        <div class="position-relative" style="height: 220px;">
                            <img src="{{ $facility->image_url ?? 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=600&q=80' }}" class="w-100 h-100 object-fit-cover" alt="{{ $facility->name }}">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-dark bg-opacity-75 backdrop-blur text-white px-3 py-2 rounded-pill fw-semibold" style="backdrop-filter: blur(8px);">
                                    ${{ number_format($facility->hourly_rate, 2) }} / hr
                                </span>
                            </div>
                            <div class="position-absolute bottom-0 start-0 m-3">
                                <span class="badge-sport bg-white text-dark shadow-sm">
                                    {{ ucfirst(str_replace('_', ' ', $facility->sport_type)) }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <h5 class="font-heading fw-bold text-dark mb-2">{{ $facility->name }}</h5>
                            <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($facility->description, 90) }}</p>

                            <div class="d-flex align-items-center justify-content-between pt-3 border-top text-secondary small mb-3">
                                <span><i class="fa-solid fa-layer-group me-1 text-primary"></i> {{ $facility->courts->count() }} Courts</span>
                                <span><i class="fa-regular fa-clock me-1 text-success"></i> {{ date('g:i A', strtotime($facility->open_time)) }} - {{ date('g:i A', strtotime($facility->close_time)) }}</span>
                            </div>

                            <a href="{{ route('customer.bookings.wizard', ['facility_id' => $facility->id]) }}" class="btn btn-primary-gradient w-100 rounded-pill py-2 font-heading fw-bold">
                                Reserve Facility <i class="fa-solid fa-calendar-plus ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 bg-dark text-white position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container text-center py-4">
        <h2 class="display-5 font-heading fw-bold mb-3">Ready for your next game?</h2>
        <p class="lead text-secondary mb-4 mx-auto" style="max-width: 600px;">Check court availability in seconds and lock in your reservation with ApexSports Hub.</p>
        <a href="{{ route('customer.bookings.wizard') }}" class="btn btn-accent btn-lg rounded-pill px-5 py-3 font-heading fw-bold">
            Start Booking Wizard <i class="fa-solid fa-arrow-right ms-2"></i>
        </a>
    </div>
</section>
@endsection
