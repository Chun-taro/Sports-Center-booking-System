@extends('layouts.admin')

@section('title', 'Manage Booking #' . $booking->booking_code)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings.index') }}" class="text-secondary text-decoration-none small"><i class="fa-solid fa-arrow-left me-1"></i> Back to Reservations</a>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <h2 class="font-heading fw-bold mb-0">Booking #{{ $booking->booking_code }}</h2>
        <span class="badge {{ $booking->status_badge }} rounded-pill px-3 py-2 text-uppercase fs-6">
            {{ str_replace('_', ' ', $booking->status) }}
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Details -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
            <h4 class="font-heading fw-bold mb-4 border-bottom pb-3">Reservation Overview</h4>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <span class="text-secondary small d-block">Facility</span>
                    <h5 class="fw-bold text-dark mb-0">{{ $booking->facility->name }}</h5>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary small d-block">Court</span>
                    <h5 class="fw-bold text-primary mb-0">{{ $booking->court->name }}</h5>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary small d-block">Date</span>
                    <strong class="text-dark"><i class="fa-regular fa-calendar text-primary me-1"></i> {{ $booking->booking_date->format('l, F j, Y') }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary small d-block">Time Slot</span>
                    <strong class="text-success"><i class="fa-regular fa-clock text-success me-1"></i> {{ date('g:i A', strtotime($booking->start_time)) }} - {{ date('g:i A', strtotime($booking->end_time)) }}</strong>
                </div>
            </div>

            <h5 class="font-heading fw-bold mb-3 border-top pt-3">Customer Information</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <span class="text-secondary small d-block">Name</span>
                    <strong class="text-dark">{{ $booking->user->name ?? 'N/A' }}</strong>
                </div>
                <div class="col-md-4">
                    <span class="text-secondary small d-block">Email</span>
                    <span class="text-dark small">{{ $booking->user->email ?? 'N/A' }}</span>
                </div>
                <div class="col-md-4">
                    <span class="text-secondary small d-block">Phone</span>
                    <span class="text-dark small">{{ $booking->user->phone ?? 'N/A' }}</span>
                </div>
            </div>

            @if($booking->cancellation_reason)
                <div class="alert alert-danger mb-4">
                    <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Cancellation / Rejection Reason:</h6>
                    <p class="mb-0 small">{{ $booking->cancellation_reason }}</p>
                </div>
            @endif

            <h5 class="font-heading fw-bold mb-3 border-top pt-3">Financial Ledger</h5>
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rate Type</th>
                        <th class="text-center">Hours</th>
                        <th class="text-end">Hourly Rate</th>
                        <th class="text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Court Reservation Rate</td>
                        <td class="text-center fw-bold">{{ $booking->duration_hours }} hr</td>
                        <td class="text-end">${{ number_format($booking->hourly_rate, 2) }}</td>
                        <td class="text-end fw-bold text-success">${{ number_format($booking->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: Status Control Panel -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 90px;">
            <h5 class="font-heading fw-bold mb-3">Update Booking Status</h5>
            
            <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST" class="mb-4">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Select New Status</label>
                    <select name="status" class="form-select" required>
                        <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $booking->status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="checked_in" {{ $booking->status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                        <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="rejected" {{ $booking->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="no_show" {{ $booking->status === 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Notes / Reason (Optional)</label>
                    <textarea name="reason" class="form-control" rows="2" placeholder="Note or reason..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 font-heading fw-bold rounded-pill">
                    Update Status <i class="fa-solid fa-rotate ms-1"></i>
                </button>
            </form>

            @if($booking->payment)
                <div class="border-top pt-3">
                    <h6 class="font-heading fw-bold mb-2">Payment Details</h6>
                    <p class="small text-muted mb-1">Status: <strong class="text-uppercase text-dark">{{ $booking->payment->payment_status }}</strong></p>
                    <p class="small text-muted mb-1">Method: {{ ucfirst($booking->payment->payment_method) }}</p>
                    @if($booking->payment->reference_number)
                        <p class="small text-muted mb-0">Ref: {{ $booking->payment->reference_number }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
