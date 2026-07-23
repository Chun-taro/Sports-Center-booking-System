@extends('layouts.admin')

@section('title', 'Interactive Calendar - ApexSports Hub')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="font-heading fw-bold mb-1">Booking Calendar</h2>
        <p class="text-secondary small mb-0">Visual calendar representation of court reservations, color-coded by booking status.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">
        <select id="facilityFilter" class="form-select form-select-sm" style="width: auto;">
            <option value="">-- All Facilities --</option>
            @foreach($facilities as $fac)
                <option value="{{ $fac->id }}">{{ $fac->name }}</option>
            @endforeach
        </select>

        <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
            <option value="">-- All Statuses --</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="checked_in">Checked In</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>

<!-- Status Legend Banner -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
    <div class="d-flex flex-wrap align-items-center gap-3 small fw-semibold">
        <span class="text-secondary me-2">Status Legend:</span>
        <span class="badge" style="background:#ffc107; color:#000;">Pending</span>
        <span class="badge" style="background:#0d6efd;">Approved</span>
        <span class="badge" style="background:#0dcaf0; color:#000;">Checked In</span>
        <span class="badge" style="background:#198754;">Completed</span>
        <span class="badge" style="background:#6c757d;">Cancelled</span>
        <span class="badge" style="background:#dc3545;">Rejected / No Show</span>
    </div>
</div>

<!-- Calendar Container -->
<div class="card border-0 shadow-sm rounded-4 p-4">
    <div id="fullCalendar"></div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-heading fw-bold" id="modalBookingCode">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <span class="text-secondary small d-block">Facility & Court</span>
                    <h5 class="fw-bold text-dark mb-0" id="modalFacilityCourt">-</h5>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <span class="text-secondary small d-block">Customer</span>
                        <strong class="text-dark" id="modalCustomer">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-secondary small d-block">Phone</span>
                        <strong class="text-dark" id="modalPhone">-</strong>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <span class="text-secondary small d-block">Status</span>
                        <span class="badge rounded-pill px-3 py-2 text-uppercase" id="modalStatusBadge">-</span>
                    </div>
                    <div class="col-6">
                        <span class="text-secondary small d-block">Total Amount</span>
                        <strong class="text-success fs-5" id="modalAmount">-</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <a href="#" id="modalDetailLink" class="btn btn-primary rounded-pill px-4">View Full Reservation</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('fullCalendar');
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function(info, successCallback, failureCallback) {
                const facId = document.getElementById('facilityFilter').value;
                const status = document.getElementById('statusFilter').value;
                
                let url = `{{ route('admin.calendar.events') }}?start=${info.startStr.split('T')[0]}&end=${info.endStr.split('T')[0]}`;
                if (facId) url += `&facility_id=${facId}`;
                if (status) url += `&status=${status}`;

                fetch(url)
                    .then(res => res.json())
                    .then(data => successCallback(data))
                    .catch(err => failureCallback(err));
            },
            eventClick: function(info) {
                const props = info.event.extendedProps;
                document.getElementById('modalBookingCode').innerText = 'Booking #' + props.booking_code;
                document.getElementById('modalFacilityCourt').innerText = `${props.facility_name} (${props.court_name})`;
                document.getElementById('modalCustomer').innerText = props.customer_name;
                document.getElementById('modalPhone').innerText = props.customer_phone;
                document.getElementById('modalAmount').innerText = props.amount;
                
                const badge = document.getElementById('modalStatusBadge');
                badge.innerText = props.status;
                badge.className = 'badge rounded-pill px-3 py-2 text-uppercase bg-dark';

                document.getElementById('modalDetailLink').href = `{{ url('admin/bookings') }}/${info.event.id}`;

                eventModal.show();
            }
        });

        calendar.render();

        document.getElementById('facilityFilter').addEventListener('change', () => calendar.refetchEvents());
        document.getElementById('statusFilter').addEventListener('change', () => calendar.refetchEvents());
    });
</script>
@endpush
