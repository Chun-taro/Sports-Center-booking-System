@extends('layouts.admin')

@section('title', 'Administrator Dashboard - ApexSports Hub')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="font-heading fw-bold mb-1">Dashboard Overview</h2>
        <p class="text-secondary small mb-0">Live metrics, booking status distribution, revenue analytics, and facility usage.</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="{{ route('admin.calendar.index') }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-calendar-days me-1"></i> Interactive Calendar
        </a>
    </div>
</div>

<!-- Stat Counter Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold">Total Users</span>
                <div class="stat-icon bg-primary-subtle text-primary"><i class="fa-solid fa-users"></i></div>
            </div>
            <h3 class="font-heading fw-bold text-dark mb-0">{{ number_format($totalUsers) }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold">Total Bookings</span>
                <div class="stat-icon bg-success-subtle text-success"><i class="fa-solid fa-bookmark"></i></div>
            </div>
            <h3 class="font-heading fw-bold text-dark mb-0">{{ number_format($totalBookings) }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold">Today's Bookings</span>
                <div class="stat-icon bg-info-subtle text-info"><i class="fa-solid fa-calendar-day"></i></div>
            </div>
            <h3 class="font-heading fw-bold text-dark mb-0">{{ number_format($todaysBookings) }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold">Monthly Revenue</span>
                <div class="stat-icon bg-warning-subtle text-warning"><i class="fa-solid fa-dollar-sign"></i></div>
            </div>
            <h3 class="font-heading fw-bold text-dark mb-0">${{ number_format($monthlyRevenue, 2) }}</h3>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card border-start border-warning border-4">
            <span class="text-muted small fw-semibold">Pending</span>
            <h4 class="font-heading fw-bold text-warning mb-0">{{ $pendingBookings }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card border-start border-primary border-4">
            <span class="text-muted small fw-semibold">Approved</span>
            <h4 class="font-heading fw-bold text-primary mb-0">{{ $approvedBookings }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card border-start border-danger border-4">
            <span class="text-muted small fw-semibold">Cancelled</span>
            <h4 class="font-heading fw-bold text-danger mb-0">{{ $cancelledBookings }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card border-start border-success border-4">
            <span class="text-muted small fw-semibold">Courts Available / Occupied</span>
            <h4 class="font-heading fw-bold text-success mb-0">{{ $availableCourtsCount }} / <span class="text-dark">{{ $occupiedCourtsCount }}</span></h4>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Chart 1 & 2: Monthly Bookings & Revenue -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
            <h5 class="font-heading fw-bold mb-3">Monthly Bookings & Revenue Analytics</h5>
            <canvas id="monthlyChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Chart 3: Facility Utilization -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
            <h5 class="font-heading fw-bold mb-3">Facility Utilization</h5>
            <canvas id="utilizationChart" style="max-height: 280px;"></canvas>
        </div>
    </div>

    <!-- Chart 4: Popular Sports -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
            <h5 class="font-heading fw-bold mb-3">Popular Sports Distribution</h5>
            <canvas id="sportsChart" style="max-height: 260px;"></canvas>
        </div>
    </div>

    <!-- Chart 5: Booking Trends (7 Days) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
            <h5 class="font-heading fw-bold mb-3">7-Day Booking Trends</h5>
            <canvas id="trendsChart" style="max-height: 260px;"></canvas>
        </div>
    </div>
</div>

<!-- Recent Reservations -->
<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-heading fw-bold mb-0">Recent Reservations</h5>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-link btn-sm text-decoration-none">View All</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Booking Code</th>
                    <th>Customer</th>
                    <th>Facility & Court</th>
                    <th>Date & Slot</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Quick Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $b)
                    <tr>
                        <td class="fw-bold font-heading">{{ $b->booking_code }}</td>
                        <td>{{ $b->user->name ?? 'Guest' }}</td>
                        <td>{{ $b->facility->name }} - <span class="small text-muted">{{ $b->court->name }}</span></td>
                        <td>
                            <div class="small fw-semibold">{{ $b->booking_date->format('M d, Y') }}</div>
                            <div class="small text-muted">{{ date('g:i A', strtotime($b->start_time)) }} - {{ date('g:i A', strtotime($b->end_time)) }}</div>
                        </td>
                        <td class="fw-bold">${{ number_format($b->total_amount, 2) }}</td>
                        <td>
                            <span class="badge {{ $b->status_badge }} rounded-pill px-3 py-1 text-uppercase" style="font-size:0.65rem;">
                                {{ str_replace('_', ' ', $b->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($b->status === 'pending')
                                <form action="{{ route('admin.bookings.approve', $b->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success rounded-pill px-2"><i class="fa-solid fa-check me-1"></i> Approve</button>
                                </form>
                            @elseif($b->status === 'approved')
                                <form action="{{ route('admin.bookings.check-in', $b->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-info text-white rounded-pill px-2"><i class="fa-solid fa-user-check me-1"></i> Check In</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.bookings.show', $b->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart 1: Monthly Bookings & Revenue
        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    {
                        label: 'Bookings Count',
                        data: {!! json_encode($monthlyBookingsData) !!},
                        backgroundColor: '#2563eb',
                        borderRadius: 6
                    },
                    {
                        label: 'Revenue ($)',
                        data: {!! json_encode($monthlyRevenueData) !!},
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Chart 3: Facility Utilization
        new Chart(document.getElementById('utilizationChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($facilityLabels) !!},
                datasets: [{
                    data: {!! json_encode($facilityData) !!},
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Chart 4: Popular Sports
        new Chart(document.getElementById('sportsChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($sportLabels) !!},
                datasets: [{
                    label: 'Total Bookings',
                    data: {!! json_encode($sportData) !!},
                    backgroundColor: '#8b5cf6',
                    borderRadius: 6
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y' }
        });

        // Chart 5: Booking Trends
        new Chart(document.getElementById('trendsChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($last7Days) !!},
                datasets: [{
                    label: 'Daily Reservations',
                    data: {!! json_encode($dailyBookingsData) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    });
</script>
@endpush
