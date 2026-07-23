@extends('layouts.app')

@section('title', 'My Reservations - ApexSports Hub')

@section('content')
<div class="bg-dark text-white py-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container">
        <h1 class="font-heading fw-bold">My Booking History</h1>
        <p class="text-secondary small mb-0">View upcoming reservations, check status, or cancel bookings.</p>
    </div>
</div>

<div class="container py-5">
    <!-- Status Filter Pills -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('customer.bookings.index') }}" class="btn btn-sm rounded-pill px-3 fw-semibold {{ !$status ? 'btn-primary' : 'btn-outline-secondary' }}">All Bookings</a>
        <a href="{{ route('customer.bookings.index', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill px-3 fw-semibold {{ $status === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}">Pending</a>
        <a href="{{ route('customer.bookings.index', ['status' => 'approved']) }}" class="btn btn-sm rounded-pill px-3 fw-semibold {{ $status === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}">Approved</a>
        <a href="{{ route('customer.bookings.index', ['status' => 'completed']) }}" class="btn btn-sm rounded-pill px-3 fw-semibold {{ $status === 'completed' ? 'btn-success' : 'btn-outline-success' }}">Completed</a>
        <a href="{{ route('customer.bookings.index', ['status' => 'cancelled']) }}" class="btn btn-sm rounded-pill px-3 fw-semibold {{ $status === 'cancelled' ? 'btn-secondary' : 'btn-outline-secondary' }}">Cancelled</a>
    </div>

    <!-- Reservations Table -->
    <div class="glass-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4">Booking Code</th>
                        <th class="py-3">Facility / Court</th>
                        <th class="py-3">Date & Time</th>
                        <th class="py-3">Total Amount</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 px-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="py-3 px-4 fw-bold font-heading text-dark">
                                {{ $booking->booking_code }}
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $booking->facility->name }}</div>
                                <div class="small text-muted">{{ $booking->court->name }}</div>
                            </td>
                            <td class="py-3">
                                <div class="fw-semibold text-dark"><i class="fa-regular fa-calendar me-1 text-primary"></i> {{ $booking->booking_date->format('M d, Y') }}</div>
                                <div class="small text-muted"><i class="fa-regular fa-clock me-1 text-success"></i> {{ date('g:i A', strtotime($booking->start_time)) }} - {{ date('g:i A', strtotime($booking->end_time)) }}</div>
                            </td>
                            <td class="py-3 fw-bold text-dark">
                                ${{ number_format($booking->total_amount, 2) }}
                            </td>
                            <td class="py-3">
                                <span class="badge {{ $booking->status_badge }} rounded-pill px-3 py-2 text-uppercase font-heading" style="font-size:0.7rem;">
                                    {{ str_replace('_', ' ', $booking->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-end">
                                <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    View Receipt <i class="fa-solid fa-chevron-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fa-solid fa-calendar-xmark text-muted display-4 mb-3"></i>
                                <h5 class="font-heading fw-bold">No Reservations Found</h5>
                                <p class="text-secondary small">You haven't made any court reservations yet.</p>
                                <a href="{{ route('customer.bookings.wizard') }}" class="btn btn-accent rounded-pill px-4">Book Your First Court</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $bookings->withQueryString()->links() }}
    </div>
</div>
@endsection
