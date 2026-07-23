@extends('layouts.app')

@section('title', 'Sign In - ApexSports Booking Hub')

@section('content')
<div class="py-5 bg-dark text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 rounded-4 shadow-lg overflow-hidden bg-white text-dark">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <h3 class="font-heading fw-bold">Welcome Back</h3>
                            <p class="text-muted small">Sign in to manage your bookings and court reservations</p>
                        </div>

                        <!-- Demo Credentials Helper Alert -->
                        <div class="alert alert-light border small mb-4">
                            <div class="fw-bold mb-1"><i class="fa-solid fa-key text-warning me-1"></i> Demo Login Accounts:</div>
                            <ul class="list-unstyled mb-0 text-secondary" style="font-size: 0.82rem;">
                                <li><strong>Admin:</strong> admin@apexsports.com / password</li>
                                <li><strong>Staff:</strong> staff@apexsports.com / password</li>
                                <li><strong>Customer:</strong> customer@apexsports.com / password</li>
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold small text-uppercase text-secondary">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold small text-uppercase text-secondary">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label small text-secondary" for="remember">Remember me</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-gradient w-100 py-3 rounded-3 font-heading fw-bold fs-6">
                                Sign In <i class="fa-solid fa-arrow-right me-1"></i>
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-2 border-top">
                            <span class="small text-muted">Don't have an account yet?</span>
                            <a href="{{ route('register') }}" class="small fw-bold text-primary ms-1 text-decoration-none">Register Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
