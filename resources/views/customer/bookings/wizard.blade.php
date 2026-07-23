@extends('layouts.app')

@section('title', 'Court Reservation Wizard - ApexSports Hub')

@section('content')
<div class="bg-dark text-white py-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container text-center">
        <h1 class="font-heading fw-bold">Online Booking Wizard</h1>
        <p class="text-secondary small mb-0">Follow the steps below to reserve your court in real-time.</p>
    </div>
</div>

<div class="container py-5">
    <!-- Step Indicator Progress Bar -->
    <div class="glass-card p-4 mb-5">
        <div class="row text-center position-relative">
            <div class="col-2 wizard-step active" id="step-nav-1">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center fw-bold step-circle">1</div>
                <span class="small fw-semibold d-none d-md-inline">Facility</span>
            </div>
            <div class="col-2 wizard-step" id="step-nav-2">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center fw-bold step-circle">2</div>
                <span class="small fw-semibold d-none d-md-inline">Court</span>
            </div>
            <div class="col-2 wizard-step" id="step-nav-3">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center fw-bold step-circle">3</div>
                <span class="small fw-semibold d-none d-md-inline">Date</span>
            </div>
            <div class="col-2 wizard-step" id="step-nav-4">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center fw-bold step-circle">4</div>
                <span class="small fw-semibold d-none d-md-inline">Time Slot</span>
            </div>
            <div class="col-2 wizard-step" id="step-nav-5">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center fw-bold step-circle">5</div>
                <span class="small fw-semibold d-none d-md-inline">Summary</span>
            </div>
            <div class="col-2 wizard-step" id="step-nav-6">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center fw-bold step-circle">6</div>
                <span class="small fw-semibold d-none d-md-inline">Confirm</span>
            </div>
        </div>
    </div>

    <!-- Booking Form Container -->
    <form action="{{ route('customer.bookings.store') }}" method="POST" id="bookingForm">
        @csrf

        <!-- STEP 1: Select Facility -->
        <div class="wizard-panel active" id="panel-step-1">
            <h4 class="font-heading fw-bold mb-4">Step 1: Select Sports Facility</h4>
            <div class="row g-4 mb-4">
                @foreach($facilities as $facility)
                    <div class="col-md-6 col-lg-4">
                        <div class="glass-card h-100 p-3 facility-card cursor-pointer {{ $selectedFacility->id == $facility->id ? 'selected-border' : '' }}" onclick="selectFacility({{ $facility->id }}, '{{ addslashes($facility->name) }}', {{ $facility->hourly_rate }})">
                            <input type="radio" name="facility_id" value="{{ $facility->id }}" class="d-none" id="fac_{{ $facility->id }}" {{ $selectedFacility->id == $facility->id ? 'checked' : '' }}>
                            <div class="position-relative mb-3 rounded-3 overflow-hidden" style="height: 160px;">
                                <img src="{{ $facility->image_url ?? 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=500&q=80' }}" class="w-100 h-100 object-fit-cover">
                                <span class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-2 text-white px-2 py-1 small">
                                    ${{ number_format($facility->hourly_rate, 2) }}/hr
                                </span>
                            </div>
                            <h5 class="font-heading fw-bold mb-1 text-dark">{{ $facility->name }}</h5>
                            <p class="text-muted small mb-0">{{ Str::limit($facility->description, 60) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-primary-gradient px-4 py-2 font-heading fw-bold rounded-pill" onclick="goToStep(2)">
                    Next: Choose Court <i class="fa-solid fa-arrow-right me-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 2: Choose Court -->
        <div class="wizard-panel d-none" id="panel-step-2">
            <h4 class="font-heading fw-bold mb-4">Step 2: Choose Court</h4>
            <div class="row g-4 mb-4" id="courtsContainer">
                <div class="col-12 text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2">Loading available courts...</p>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 font-heading fw-bold rounded-pill" onclick="goToStep(1)">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-primary-gradient px-4 py-2 font-heading fw-bold rounded-pill" onclick="goToStep(3)">
                    Next: Choose Date <i class="fa-solid fa-arrow-right me-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 3: Choose Date -->
        <div class="wizard-panel d-none" id="panel-step-3">
            <h4 class="font-heading fw-bold mb-4">Step 3: Select Reservation Date</h4>
            <div class="row justify-content-center mb-4">
                <div class="col-md-6 text-center">
                    <div class="glass-card p-4">
                        <label class="form-label fw-bold text-secondary mb-3">Choose Date (Up to 30 days in advance)</label>
                        <input type="text" id="booking_date_picker" name="booking_date" class="form-control text-center fs-5 fw-bold bg-light" placeholder="Select Date" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 font-heading fw-bold rounded-pill" onclick="goToStep(2)">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-primary-gradient px-4 py-2 font-heading fw-bold rounded-pill" onclick="fetchTimeSlotsAndNext()">
                    Next: Choose Time Slot <i class="fa-solid fa-arrow-right me-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 4: Choose Time Slot -->
        <div class="wizard-panel d-none" id="panel-step-4">
            <h4 class="font-heading fw-bold mb-3">Step 4: Select Time Slot</h4>
            <p class="text-muted small mb-4">Click an available time slot below. Unavailable or past slots are automatically disabled.</p>
            
            <input type="hidden" name="start_time" id="input_start_time">
            <input type="hidden" name="end_time" id="input_end_time">

            <div id="slotsAlertContainer"></div>

            <div class="row g-3 mb-4" id="slotsGrid">
                <!-- Loaded dynamically -->
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 font-heading fw-bold rounded-pill" onclick="goToStep(3)">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-primary-gradient px-4 py-2 font-heading fw-bold rounded-pill" id="btnNextSummary" onclick="goToStep(5)" disabled>
                    Next: Summary <i class="fa-solid fa-arrow-right me-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 5: Booking Summary -->
        <div class="wizard-panel d-none" id="panel-step-5">
            <h4 class="font-heading fw-bold mb-4">Step 5: Review Reservation Details</h4>
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="glass-card p-4">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-secondary fw-semibold">Facility</td>
                                        <td class="fw-bold text-end text-dark fs-5" id="sumFacility">-</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-secondary fw-semibold">Court</td>
                                        <td class="fw-bold text-end text-dark" id="sumCourt">-</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-secondary fw-semibold">Reservation Date</td>
                                        <td class="fw-bold text-end text-dark" id="sumDate">-</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-secondary fw-semibold">Time Slot</td>
                                        <td class="fw-bold text-end text-primary" id="sumTimeSlot">-</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-secondary fw-semibold">Hourly Rate</td>
                                        <td class="fw-bold text-end text-dark" id="sumRate">-</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-dark fs-5">Total Price</td>
                                        <td class="fw-bold text-end text-success fs-4" id="sumTotal">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 font-heading fw-bold rounded-pill" onclick="goToStep(4)">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-primary-gradient px-4 py-2 font-heading fw-bold rounded-pill" onclick="goToStep(6)">
                    Next: Payment Method <i class="fa-solid fa-arrow-right me-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 6: Payment Method & Confirmation -->
        <div class="wizard-panel d-none" id="panel-step-6">
            <h4 class="font-heading fw-bold mb-4">Step 6: Payment Method & Confirmation</h4>
            <div class="row justify-content-center mb-4">
                <div class="col-lg-7">
                    <div class="glass-card p-4">
                        <h5 class="font-heading fw-bold mb-3">Choose Payment Method</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="p-3 border rounded-3 d-block cursor-pointer payment-option text-center">
                                    <input type="radio" name="payment_method" value="cash" checked class="me-2">
                                    <i class="fa-solid fa-money-bill-wave text-success fs-4 d-block mb-1"></i>
                                    <span class="fw-bold small d-block">Pay at Counter (Cash)</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="p-3 border rounded-3 d-block cursor-pointer payment-option text-center">
                                    <input type="radio" name="payment_method" value="gcash" class="me-2">
                                    <i class="fa-solid fa-mobile-screen-button text-primary fs-4 d-block mb-1"></i>
                                    <span class="fw-bold small d-block">GCash E-Wallet</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="p-3 border rounded-3 d-block cursor-pointer payment-option text-center">
                                    <input type="radio" name="payment_method" value="maya" class="me-2">
                                    <i class="fa-solid fa-wallet text-info fs-4 d-block mb-1"></i>
                                    <span class="fw-bold small d-block">Maya E-Wallet</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="p-3 border rounded-3 d-block cursor-pointer payment-option text-center">
                                    <input type="radio" name="payment_method" value="credit_card" class="me-2">
                                    <i class="fa-solid fa-credit-card text-warning fs-4 d-block mb-1"></i>
                                    <span class="fw-bold small d-block">Credit / Debit Card</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold small text-secondary">Special Requests / Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Equipment requests, arrival details..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 font-heading fw-bold rounded-pill" onclick="goToStep(5)">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </button>
                <button type="submit" class="btn btn-accent px-5 py-3 font-heading fw-bold rounded-pill fs-6 shadow">
                    Confirm & Complete Booking <i class="fa-solid fa-check-double ms-1"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .step-circle {
        width: 38px;
        height: 38px;
        background: #e2e8f0;
        color: #64748b;
        transition: all 0.3s ease;
    }
    .wizard-step.active .step-circle {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }
    .wizard-step.completed .step-circle {
        background: #2563eb;
        color: #fff;
    }
    .selected-border {
        border: 2px solid #10b981 !important;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25) !important;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .slot-btn {
        padding: 0.8rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .slot-btn.available {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #1e293b;
    }
    .slot-btn.available:hover {
        border-color: #2563eb;
        background: #eff6ff;
        color: #2563eb;
    }
    .slot-btn.selected {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff !important;
        border-color: #2563eb !important;
    }
    .slot-btn.unavailable {
        background: #f1f5f9;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
        cursor: not-allowed;
        opacity: 0.6;
    }
</style>
@endpush

@push('scripts')
<script>
    let state = {
        facilityId: "{{ $selectedFacility->id }}",
        facilityName: "{{ addslashes($selectedFacility->name) }}",
        courtId: null,
        courtName: '',
        date: "{{ date('Y-m-d') }}",
        startTime: '',
        endTime: '',
        slotLabel: '',
        hourlyRate: {{ $selectedFacility->hourly_rate }},
    };

    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#booking_date_picker", {
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: new Date().fp_incr(30),
            defaultDate: state.date,
            onChange: function(selectedDates, dateStr) {
                state.date = dateStr;
            }
        });

        loadCourts(state.facilityId);
    });

    function selectFacility(id, name, rate) {
        state.facilityId = id;
        state.facilityName = name;
        state.hourlyRate = rate;

        document.querySelectorAll('.facility-card').forEach(card => card.classList.remove('selected-border'));
        event.currentTarget.classList.add('selected-border');

        loadCourts(id);
    }

    function loadCourts(facilityId) {
        fetch("{{ route('customer.bookings.get-courts') }}?facility_id=" + facilityId)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('courtsContainer');
                container.innerHTML = '';

                if (data.courts.length === 0) {
                    container.innerHTML = '<div class="col-12 text-center py-4"><p class="text-danger fw-bold">No active courts available for this facility.</p></div>';
                    return;
                }

                data.courts.forEach((c, idx) => {
                    const isChecked = idx === 0;
                    if (isChecked) {
                        state.courtId = c.id;
                        state.courtName = c.name;
                        if (c.hourly_rate_override) state.hourlyRate = c.hourly_rate_override;
                    }

                    const html = `
                        <div class="col-md-6">
                            <div class="glass-card p-4 court-card cursor-pointer ${isChecked ? 'selected-border' : ''}" onclick="selectCourt(${c.id}, '${c.name.replace(/'/g, "\\'")}', ${c.hourly_rate_override || state.hourlyRate})">
                                <input type="radio" name="court_id" value="${c.id}" class="d-none" ${isChecked ? 'checked' : ''}>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="font-heading fw-bold mb-1 text-dark">${c.name}</h5>
                                        <span class="small text-muted"><i class="fa-solid fa-users me-1"></i> Capacity: ${c.capacity} Players</span>
                                    </div>
                                    <span class="badge bg-success-subtle text-success fs-6 fw-bold px-3 py-2">Active</span>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += html;
                });
            });
    }

    function selectCourt(id, name, rate) {
        state.courtId = id;
        state.courtName = name;
        if (rate) state.hourlyRate = rate;

        document.querySelectorAll('.court-card').forEach(card => card.classList.remove('selected-border'));
        event.currentTarget.classList.add('selected-border');
    }

    function goToStep(stepNum) {
        document.querySelectorAll('.wizard-panel').forEach(panel => panel.classList.add('d-none'));
        document.getElementById(`panel-step-${stepNum}`).classList.remove('d-none');

        document.querySelectorAll('.wizard-step').forEach((s, idx) => {
            if (idx + 1 === stepNum) {
                s.classList.add('active');
            } else if (idx + 1 < stepNum) {
                s.classList.remove('active');
                s.classList.add('completed');
            } else {
                s.classList.remove('active', 'completed');
            }
        });

        if (stepNum === 5) {
            updateSummary();
        }
    }

    function fetchTimeSlotsAndNext() {
        const grid = document.getElementById('slotsGrid');
        const alertBox = document.getElementById('slotsAlertContainer');
        grid.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="text-muted small mt-2">Checking real-time slot availability...</p></div>';
        alertBox.innerHTML = '';

        fetch("{{ route('customer.bookings.check-availability') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                facility_id: state.facilityId,
                court_id: state.courtId,
                date: state.date
            })
        })
        .then(res => res.json())
        .then(data => {
            grid.innerHTML = '';
            if (!data.success) {
                alertBox.innerHTML = `<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> ${data.message}</div>`;
                return;
            }

            if (data.hourly_rate) {
                state.hourlyRate = data.hourly_rate;
            }

            if (data.slots.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center py-4"><p class="text-muted">No operating time slots available for this date.</p></div>';
                return;
            }

            data.slots.forEach(slot => {
                const btnClass = slot.available ? 'available' : 'unavailable';
                const html = `
                    <div class="col-6 col-md-4 col-lg-3">
                        <button type="button" class="btn w-100 slot-btn ${btnClass}" ${slot.available ? `onclick="selectSlot('${slot.start_time}', '${slot.end_time}', '${slot.label}', this)"` : 'disabled'}>
                            <i class="fa-regular fa-clock me-1"></i> ${slot.label}
                            <span class="d-block small text-muted" style="font-size:0.7rem;">${slot.reason}</span>
                        </button>
                    </div>
                `;
                grid.innerHTML += html;
            });

            goToStep(4);
        });
    }

    function selectSlot(start, end, label, btnElem) {
        state.startTime = start;
        state.endTime = end;
        state.slotLabel = label;

        document.getElementById('input_start_time').value = start;
        document.getElementById('input_end_time').value = end;

        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
        btnElem.classList.add('selected');

        document.getElementById('btnNextSummary').removeAttribute('disabled');
    }

    function updateSummary() {
        document.getElementById('sumFacility').innerText = state.facilityName;
        document.getElementById('sumCourt').innerText = state.courtName;
        document.getElementById('sumDate').innerText = state.date;
        document.getElementById('sumTimeSlot').innerText = state.slotLabel;
        document.getElementById('sumRate').innerText = '$' + parseFloat(state.hourlyRate).toFixed(2);
        document.getElementById('sumTotal').innerText = '$' + parseFloat(state.hourlyRate).toFixed(2);
    }
</script>
@endpush
