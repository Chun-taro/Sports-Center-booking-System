@extends('layouts.admin')

@section('title', 'Analytical Reports - ApexSports Hub')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="font-heading fw-bold mb-1">Analytical Reports</h2>
        <p class="text-secondary small mb-0">Generate daily, weekly, and monthly statistics, revenue metrics, and export data.</p>
    </div>
    <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success rounded-pill px-4 font-heading fw-bold mt-2 mt-md-0">
        <i class="fa-solid fa-file-excel me-1"></i> Export to CSV / Excel
    </a>
</div>

<!-- Date Range Picker Bar -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-bold small text-secondary">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold small text-secondary">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100 font-heading fw-bold">Generate Report</button>
        </div>
    </form>
</div>

<!-- Key Stat Cards in Date Range -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <span class="text-muted small fw-semibold">Reservations in Range</span>
            <h3 class="font-heading fw-bold text-primary mb-0">{{ number_format($totalBookingsInRange) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <span class="text-muted small fw-semibold">Total Revenue Collected</span>
            <h3 class="font-heading fw-bold text-success mb-0">${{ number_format($totalRevenueInRange, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <span class="text-muted small fw-semibold">Most Booked Venue</span>
            <h5 class="font-heading fw-bold text-dark mb-0 text-truncate">{{ $mostBookedFacility->name ?? 'N/A' }}</h5>
            <span class="small text-muted">{{ $mostBookedFacility->bookings_count ?? 0 }} bookings</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <span class="text-muted small fw-semibold">Active Customers</span>
            <h3 class="font-heading fw-bold text-info mb-0">{{ number_format($activeCustomers) }} / <span class="fs-6 text-muted font-body">{{ $totalCustomers }}</span></h3>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Facility Usage Matrix -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="font-heading fw-bold mb-3">Facility Usage Matrix</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Facility Name</th>
                            <th>Sport</th>
                            <th class="text-end">Bookings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facilityUsage as $fu)
                            <tr>
                                <td class="fw-bold text-dark">{{ $fu->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ ucfirst($fu->sport_type) }}</span></td>
                                <td class="text-end fw-bold text-primary">{{ $fu->bookings_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Peak Hours Matrix -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="font-heading fw-bold mb-3">Peak Booking Hours</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Time Slot Start</th>
                            <th class="text-end">Reservations Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($peakHours as $ph)
                            <tr>
                                <td class="fw-bold text-dark"><i class="fa-regular fa-clock me-1 text-success"></i> {{ date('g:i A', strtotime($ph->start_time)) }}</td>
                                <td class="text-end fw-bold text-success">{{ $ph->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Bookings Log -->
<div class="card border-0 shadow-sm rounded-4 p-4">
    <h5 class="font-heading fw-bold mb-3">Filtered Reservations Log</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Customer</th>
                    <th>Facility & Court</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookingsList as $b)
                    <tr>
                        <td class="fw-bold font-heading">{{ $b->booking_code }}</td>
                        <td>{{ $b->user->name ?? 'N/A' }}</td>
                        <td>{{ $b->facility->name }} ({{ $b->court->name }})</td>
                        <td>{{ $b->booking_date->format('Y-m-d') }}</td>
                        <td class="fw-bold text-success">${{ number_format($b->total_amount, 2) }}</td>
                        <td><span class="badge {{ $b->status_badge }} rounded-pill px-3 py-1 text-uppercase">{{ $b->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $bookingsList->withQueryString()->links() }}
</div>
@endsection
