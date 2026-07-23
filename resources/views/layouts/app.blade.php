<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ApexSports Booking Hub - Premium Sports Facilities')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --accent: #10b981;
            --dark-bg: #0f172a;
            --card-bg: #1e293b;
            --body-bg: #f8fafc;
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--body-bg);
            color: #334155;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: var(--font-heading);
            font-weight: 700;
        }

        /* Navbar */
        .navbar-apex {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 1.5rem;
            color: #ffffff !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand span {
            color: var(--accent);
        }

        .nav-link {
            color: #cbd5e1 !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: #ffffff !important;
        }

        .btn-accent {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
            transition: all 0.25s ease;
        }

        .btn-accent:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
            color: #ffffff;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
            color: #ffffff;
        }

        /* Footer */
        footer {
            background: #0f172a;
            color: #94a3b8;
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Glass Cards */
        .glass-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        /* Badges */
        .badge-sport {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            font-weight: 600;
            padding: 0.35em 0.8em;
            border-radius: 20px;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top navbar-apex">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fa-solid fa-bolt text-white fs-6"></i>
                </div>
                Apex<span>Sports</span>
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('facilities.*') ? 'active' : '' }}" href="{{ route('facilities.index') }}">Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.bookings.wizard') ? 'active' : '' }}" href="{{ route('customer.bookings.wizard') }}">Book Now</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    @auth
                        @if(auth()->user()->hasRole('admin', 'staff'))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                <i class="fa-solid fa-gauge-high me-1"></i> Admin Portal
                            </a>
                        @endif
                        <a href="{{ route('customer.bookings.index') }}" class="nav-link text-white position-relative me-2">
                            <i class="fa-solid fa-calendar-check me-1"></i> My Reservations
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-dark dropdown-toggle rounded-pill d-flex align-items-center gap-2 border-secondary" type="button" data-bs-toggle="dropdown">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:28px; height:28px; font-size:0.8rem;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="d-none d-sm-inline text-white small fw-semibold">{{ auth()->user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fa-solid fa-user me-2 text-muted"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.bookings.index') }}"><i class="fa-solid fa-receipt me-2 text-muted"></i> Booking History</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit"><i class="fa-solid fa-right-from-bracket me-2"></i> Log Out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-link text-white text-decoration-none">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-accent rounded-pill px-4">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-5 mt-auto">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <a class="navbar-brand d-flex align-items-center gap-2 mb-3" href="#">
                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-bolt text-white fs-6"></i>
                        </div>
                        Apex<span>Sports</span>
                    </a>
                    <p class="small text-secondary mb-0">Experience world-class sports facility bookings. Seamless online reservations for Badminton, Basketball, Pickleball, Tennis, Futsal, and more.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3 font-heading">Explore</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="{{ route('facilities.index') }}" class="text-secondary text-decoration-none">All Facilities</a></li>
                        <li><a href="{{ route('customer.bookings.wizard') }}" class="text-secondary text-decoration-none">Reserve Court</a></li>
                        <li><a href="{{ route('home') }}#sports" class="text-secondary text-decoration-none">Sports Offered</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3 font-heading">Account</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        @auth
                            <li><a href="{{ route('customer.bookings.index') }}" class="text-secondary text-decoration-none">My Bookings</a></li>
                            <li><a href="{{ route('customer.profile') }}" class="text-secondary text-decoration-none">Profile Settings</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-secondary text-decoration-none">Login</a></li>
                            <li><a href="{{ route('register') }}" class="text-secondary text-decoration-none">Register Account</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white mb-3 font-heading">Facility Operating Hours</h6>
                    <p class="small text-secondary mb-1"><i class="fa-regular fa-clock me-2 text-success"></i> Monday - Sunday: 7:00 AM – 11:00 PM</p>
                    <p class="small text-secondary mb-0"><i class="fa-solid fa-location-dot me-2 text-danger"></i> Apex Sports Complex, Grand Avenue, Sector 5</p>
                </div>
            </div>
            <hr class="border-secondary my-4 opacity-25">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-secondary">
                <p class="mb-0">&copy; {{ date('Y') }} ApexSports Hub. All rights reserved.</p>
                <div class="d-flex gap-3 mt-2 mt-md-0">
                    <a href="#" class="text-secondary"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="text-secondary"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-secondary"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @include('partials.alerts')
    @stack('scripts')
</body>
</html>
