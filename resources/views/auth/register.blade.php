@extends('layouts.app')

@section('title', 'Register Account - ApexSports Hub')

@section('content')
<div class="py-5 bg-dark text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 85vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card border-0 rounded-4 shadow-lg overflow-hidden bg-white text-dark">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-accent text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; font-size: 1.5rem; background:#10b981;">
                                <i class="fa-solid fa-user-plus"></i>
                            </div>
                            <h3 class="font-heading fw-bold">Create an Account</h3>
                            <p class="text-muted small">Join ApexSports to easily reserve courts and manage your bookings</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold small text-uppercase text-secondary">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                                </div>
                                @error('name')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold small text-uppercase text-secondary">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                        <input type="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold small text-uppercase text-secondary">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                                        <input type="text" class="form-control bg-light border-start-0 @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="+1 555 0192">
                                    </div>
                                    @error('phone')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-semibold small text-uppercase text-secondary">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                        <input type="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="password_confirmation" class="form-label fw-semibold small text-uppercase text-secondary">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-shield-halved"></i></span>
                                        <input type="password" class="form-control bg-light border-start-0" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-accent w-100 py-3 rounded-3 font-heading fw-bold fs-6">
                                Create Account <i class="fa-solid fa-user-check ms-1"></i>
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-2 border-top">
                            <span class="small text-muted">Already have an account?</span>
                            <a href="{{ route('login') }}" class="small fw-bold text-primary ms-1 text-decoration-none">Sign In</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
