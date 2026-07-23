@extends('layouts.admin')

@section('title', 'Payment Transactions - ApexSports Hub')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="font-heading fw-bold mb-1">Payments & Financial Ledger</h2>
        <p class="text-secondary small mb-0">Track payment status across Cash, GCash, Maya, and Credit Cards.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
        <div class="col-md-4">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">-- All Payment Statuses --</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="refunded" {{ $status === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>
        <div class="col-md-4">
            <select name="method" class="form-select" onchange="this.form.submit()">
                <option value="">-- All Payment Methods --</option>
                <option value="cash" {{ $method === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="gcash" {{ $method === 'gcash' ? 'selected' : '' }}>GCash</option>
                <option value="maya" {{ $method === 'maya' ? 'selected' : '' }}>Maya</option>
                <option value="credit_card" {{ $method === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
            </select>
        </div>
        @if($status || $method)
            <div class="col-md-2">
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Reset Filters</a>
            </div>
        @endif
    </form>
</div>

<!-- Payments Datatable -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4">Booking Ref</th>
                    <th class="py-3">Customer</th>
                    <th class="py-3">Amount</th>
                    <th class="py-3">Method</th>
                    <th class="py-3">Payment Status</th>
                    <th class="py-3">Reference No</th>
                    <th class="py-3 px-4 text-end">Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td class="py-3 px-4 fw-bold font-heading text-dark">
                            <a href="{{ route('admin.bookings.show', $p->booking_id) }}" class="text-decoration-none text-dark">{{ $p->booking->booking_code }}</a>
                        </td>
                        <td class="py-3">
                            <div class="fw-bold text-dark">{{ $p->user->name ?? 'Guest' }}</div>
                            <div class="small text-muted">{{ $p->user->email ?? 'N/A' }}</div>
                        </td>
                        <td class="py-3 fw-bold text-success fs-6">${{ number_format($p->amount, 2) }}</td>
                        <td class="py-3">
                            <span class="badge bg-light text-dark border px-3 py-1 text-uppercase fw-semibold">
                                {{ ucfirst($p->payment_method) }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if($p->payment_status === 'paid')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Paid</span>
                            @elseif($p->payment_status === 'unpaid')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">Unpaid</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">Refunded</span>
                            @endif
                        </td>
                        <td class="py-3 small text-muted font-monospace">{{ $p->reference_number ?? '-' }}</td>
                        <td class="py-3 px-4 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#editPaymentModal{{ $p->id }}">
                                Update Status
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Payment Modal -->
                    <div class="modal fade" id="editPaymentModal{{ $p->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.payments.status', $p->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title font-heading fw-bold">Update Payment for Booking #{{ $p->booking->booking_code }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-secondary">Payment Status</label>
                                            <select name="payment_status" class="form-select" required>
                                                <option value="paid" {{ $p->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="unpaid" {{ $p->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                                <option value="refunded" {{ $p->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-secondary">Transaction Reference Number</label>
                                            <input type="text" name="reference_number" class="form-control" value="{{ $p->reference_number }}" placeholder="e.g. PAY-9A8B7C6D">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-secondary">Ledger Note</label>
                                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary font-heading fw-bold">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No payment records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $payments->withQueryString()->links() }}
</div>
@endsection
