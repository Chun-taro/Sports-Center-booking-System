@extends('layouts.app')

@section('title', 'Select Portal - ApexSports Booking Hub')

@section('content')
<div class="py-5 bg-dark text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 85vh; display: flex; align-items: center;">
    <div class="container py-4">
        <div class="text-center mb-5">
            <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3 shadow-lg" style="width: 60px; height: 60px; font-size: 1.75rem;">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <h1 class="display-5 font-heading fw-bold text-white mb-2">Select Your Portal</h1>
            <p class="lead text-secondary opacity-90 mx-auto" style="max-width: 580px;">Zero sign-up required. Choose your role below for instant 1-click access into the platform.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" id="portalLoginForm">
            @csrf
            <div class="row g-4 justify-content-center">
                <!-- Customer Role Card -->
                <div class="col-md-6 col-lg-5">
                    <div class="glass-card p-4 p-md-5 h-100 text-center text-dark bg-white border-0 shadow-lg position-relative overflow-hidden d-flex flex-column" style="border-radius: 24px;">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 80px; height: 80px; font-size: 2.2rem;">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-20 text-success rounded-pill px-3 py-2 fw-bold text-uppercase mx-auto mb-2" style="font-size: 0.75rem;">Customer Portal</span>
                        <h3 class="font-heading fw-bold text-dark mb-2">Customer Experience</h3>
                        <p class="text-secondary small flex-grow-1 mb-4">Browse available courts, view the interactive landing page schedule, reserve time slots, and manage booking receipts.</p>
                        
                        <button type="submit" name="role" value="customer" class="btn btn-accent btn-lg w-100 py-3 rounded-pill font-heading fw-bold shadow-sm">
                            Proceed as Customer <i class="fa-solid fa-arrow-right-long ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Admin & Staff Role Card -->
                <div class="col-md-6 col-lg-5">
                    <div class="glass-card p-4 p-md-5 h-100 text-center text-dark bg-white border-0 shadow-lg position-relative overflow-hidden d-flex flex-column" style="border-radius: 24px;">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 80px; height: 80px; font-size: 2.2rem;">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-20 text-primary rounded-pill px-3 py-2 fw-bold text-uppercase mx-auto mb-2" style="font-size: 0.75rem;">Admin & Staff Portal</span>
                        <h3 class="font-heading fw-bold text-dark mb-2">Management Dashboard</h3>
                        <p class="text-secondary small flex-grow-1 mb-4">Access analytics metrics, reservation calendars, facility & court controls, operating schedules, and financial reports.</p>
                        
                        <button type="submit" name="role" value="admin" class="btn btn-primary-gradient btn-lg w-100 py-3 rounded-pill font-heading fw-bold shadow-sm">
                            Proceed as Admin / Staff <i class="fa-solid fa-gauge-high ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
