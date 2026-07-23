@extends('layouts.app')

@section('title', 'Browse Facilities - ApexSports Hub')

@section('content')
<div class="bg-dark text-white py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container py-3">
        <h1 class="display-5 font-heading fw-bold">Sports Facilities</h1>
        <p class="text-secondary mb-0">Browse our world-class venues and check real-time court availability.</p>
    </div>
</div>

<div class="container py-5">
    <!-- Filter Header -->
    <div class="glass-card p-4 mb-5">
        <form method="GET" action="{{ route('facilities.index') }}" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search facilities by name or description..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-4">
                <select name="sport" class="form-select" onchange="this.form.submit()">
                    <option value="">-- All Sports --</option>
                    @foreach($sports as $sp)
                        <option value="{{ $sp }}" {{ request('sport') == $sp ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $sp)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary-gradient w-100 rounded-3 font-heading fw-bold">Filter</button>
                @if(request('search') || request('sport'))
                    <a href="{{ route('facilities.index') }}" class="btn btn-outline-secondary rounded-3"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Facilities Grid -->
    <div class="row g-4">
        @forelse($facilities as $facility)
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
                        <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($facility->description, 100) }}</p>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top text-secondary small mb-3">
                            <span><i class="fa-solid fa-layer-group me-1 text-primary"></i> {{ $facility->courts->count() }} Courts</span>
                            <span><i class="fa-regular fa-clock me-1 text-success"></i> {{ date('g:i A', strtotime($facility->open_time)) }} - {{ date('g:i A', strtotime($facility->close_time)) }}</span>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('facilities.show', $facility->slug) }}" class="btn btn-outline-secondary w-50 rounded-pill py-2 font-heading small fw-bold">
                                Details
                            </a>
                            <a href="{{ route('customer.bookings.wizard', ['facility_id' => $facility->id]) }}" class="btn btn-primary-gradient w-50 rounded-pill py-2 font-heading small fw-bold">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 glass-card">
                    <i class="fa-solid fa-building-circle-xmark text-muted display-1 mb-3"></i>
                    <h4 class="font-heading fw-bold">No Facilities Found</h4>
                    <p class="text-secondary">Try clearing search filters or checking back later.</p>
                    <a href="{{ route('facilities.index') }}" class="btn btn-outline-primary rounded-pill px-4">Reset Search</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
        {{ $facilities->withQueryString()->links() }}
    </div>
</div>
@endsection
