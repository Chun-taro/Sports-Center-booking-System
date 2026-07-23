@extends('layouts.app')

@section('title', 'Reservation Details - ' . $booking->booking_code)

@section('content')
<div class="bg-dark text-white py-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('customer.bookings.index') }}" class="text-secondary text-decoration-none small"><i class="fa-solid fa-arrow-left me-1"></i> Back to Reservations</a>
            <h2 class="font-heading fw-bold mb-0">Booking #{{ $booking->booking_code }}</h2>
        </div>
        <button onclick="window.print()" class="btn btn-outline-light rounded-pill px-3">
            <i class="fa-solid fa-print me-1"></i> Print Receipt
        </button>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div>
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-bolt fs-5"></i>
                        </div>
                        <h4 class="font-heading fw-bold text-dark mb-1">ApexSports Booking Hub</h4>
                        <p class="text-muted small mb-0">Apex Sports Complex, Grand Avenue, Sector 5</p>
                    </div>
                    <div class="text-end">
                        <span class="badge {{ $booking->status_badge }} rounded-pill px-3 py-2 text-uppercase font-heading fs-6">
                            {{ str_replace('_', ' ', $booking->status) }}
                        </span>
                        <p class="small text-muted mt-2 mb-0">Booked on: {{ $booking->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <h6 class="text-secondary text-uppercase small font-heading fw-bold">Customer Details</h6>
                        <p class="mb-1 text-dark fw-bold">{{ $booking->user->name }}</p>
                        <p class="mb-1 text-muted small"><i class="fa-solid fa-envelope me-1"></i> {{ $booking->user->email }}</p>
                        <p class="mb-0 text-muted small"><i class="fa-solid fa-phone me-1"></i> {{ $booking->user->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-secondary text-uppercase small font-heading fw-bold">Reservation Details</h6>
                        <p class="mb-1 text-dark fw-bold">{{ $booking->facility->name }}</p>
                        <p class="mb-1 text-primary fw-semibold small">{{ $booking->court->name }}</p>
                        <p class="mb-0 text-muted small"><i class="fa-regular fa-calendar me-1"></i> {{ $booking->booking_date->format('l, F j, Y') }}</p>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item Description</th>
                                <th class="text-center">Duration</th>
                                <th class="text-end">Hourly Rate</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $booking->facility->name }} - {{ $booking->court->name }}</div>
                                    <div class="small text-muted">Time: {{ date('g:i A', strtotime($booking->start_time)) }} – {{ date('g:i A', strtotime($booking->end_time)) }}</div>
                                </td>
                                <td class="text-center fw-bold">{{ $booking->duration_hours }} hr</td>
                                <td class="text-end">${{ number_format($booking->hourly_rate, 2) }}</td>
                                <td class="text-end fw-bold text-dark">${{ number_format($booking->subtotal, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end font-heading fs-5">Total Amount:</th>
                                <th class="text-end font-heading fs-5 text-success">${{ number_format($booking->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($booking->payment)
                    <div class="p-3 border rounded-3 bg-light mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="font-heading fw-bold mb-1">Payment Status: 
                                    <span class="text-uppercase {{ $booking->payment->payment_status === 'paid' ? 'text-success' : 'text-warning' }}">
                                        {{ $booking->payment->payment_status }}
                                    </span>
                                </h6>
                                <span class="small text-muted">Method: {{ ucfirst($booking->payment->payment_method) }}</span>
                                @if($booking->payment->reference_number)
                                    <span class="small text-muted ms-2">Ref #: {{ $booking->payment->reference_number }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(in_array($booking->status, ['pending', 'approved']))
                    <div class="text-end pt-3 border-top">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fa-solid fa-ban me-1"></i> Cancel Booking
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cancellation Modal -->
@if(in_array($booking->status, ['pending', 'approved']))
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customer.bookings.cancel', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-header-title font-heading fw-bold">Cancel Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary small">Are you sure you want to cancel booking <strong>#{{ $booking->booking_code }}</strong>?</p>
                    <div class="mb-3">
                        <label for="reason" class="form-label fw-bold small text-secondary">Reason for Cancellation</label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" required placeholder="Please state your reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger font-heading fw-bold">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
