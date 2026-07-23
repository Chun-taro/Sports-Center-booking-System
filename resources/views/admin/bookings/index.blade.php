@extends('layouts.admin')

@section('title', 'Reservations Management - ApexSports Hub')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="font-heading fw-bold mb-1">Reservation Management</h2>
        <p class="text-secondary small mb-0">Approve, reject, check-in, or update customer court bookings.</p>
    </div>
</div>

<!-- Filter Header -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search Code / Name / Email..." value="{{ $search }}">
        </div>

        <div class="col-md-3">
            <select name="facility_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- All Facilities --</option>
                @foreach($facilities as $f)
                    <option value="{{ $f->id }}" {{ $facilityId == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">-- All Statuses --</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="checked_in" {{ $status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 font-heading fw-bold">Filter</button>
            @if($status || $facilityId || $date || $search)
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Bookings Datatable -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4">Booking Code</th>
                    <th class="py-3">Customer</th>
                    <th class="py-3">Facility / Court</th>
                    <th class="py-3">Date & Slot</th>
                    <th class="py-3">Amount</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 px-4 text-end">Quick Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                    <tr>
                        <td class="py-3 px-4 font-heading fw-bold text-dark">
                            <a href="{{ route('admin.bookings.show', $b->id) }}" class="text-decoration-none text-dark">{{ $b->booking_code }}</a>
                        </td>
                        <td class="py-3">
                            <div class="fw-bold text-dark">{{ $b->user->name ?? 'Guest' }}</div>
                            <div class="small text-muted">{{ $b->user->email ?? 'N/A' }}</div>
                        </td>
                        <td class="py-3">
                            <div class="fw-semibold text-dark">{{ $b->facility->name }}</div>
                            <div class="small text-muted">{{ $b->court->name }}</div>
                        </td>
                        <td class="py-3">
                            <div class="fw-semibold text-dark"><i class="fa-regular fa-calendar text-primary me-1"></i> {{ $b->booking_date->format('M d, Y') }}</div>
                            <div class="small text-muted"><i class="fa-regular fa-clock text-success me-1"></i> {{ date('g:i A', strtotime($b->start_time)) }} - {{ date('g:i A', strtotime($b->end_time)) }}</div>
                        </td>
                        <td class="py-3 fw-bold text-dark">${{ number_format($b->total_amount, 2) }}</td>
                        <td class="py-3">
                            <span class="badge {{ $b->status_badge }} rounded-pill px-3 py-1 text-uppercase" style="font-size:0.68rem;">
                                {{ str_replace('_', ' ', $b->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-end">
                            <div class="d-inline-flex gap-1">
                                @if($b->status === 'pending')
                                    <form action="{{ route('admin.bookings.approve', $b->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" title="Approve Booking">
                                            <i class="fa-solid fa-check me-1"></i> Approve
                                        </button>
                                    </form>
                                @elseif($b->status === 'approved')
                                    <form action="{{ route('admin.bookings.check-in', $b->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold" title="Check In Customer">
                                            <i class="fa-solid fa-user-check me-1"></i> Check In
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.bookings.show', $b->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No reservations matching criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $bookings->withQueryString()->links() }}
</div>
@endsection
