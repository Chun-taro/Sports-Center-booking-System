<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - ApexSports Hub')</title>

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

    <!-- FullCalendar 6 -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <!-- Chart.js 4 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --admin-sidebar-w: 260px;
            --admin-bg: #f1f5f9;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --accent: #10b981;
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--admin-bg);
            color: #334155;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
        }

        /* Sidebar */
        .sidebar {
            width: var(--admin-sidebar-w);
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.5rem;
            font-family: var(--font-heading);
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand span {
            color: var(--accent);
        }

        .sidebar-heading {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
            padding: 1.2rem 1.5rem 0.4rem 1.5rem;
        }

        .nav-item-admin {
            margin: 0.2rem 0.8rem;
        }

        .nav-link-admin {
            color: #94a3b8;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link-admin:hover {
            color: #ffffff;
            background: var(--sidebar-hover);
        }

        .nav-link-admin.active {
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .nav-link-admin i {
            width: 20px;
            text-align: center;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: var(--admin-sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-content {
            padding: 2rem;
            flex: 1;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: calc(-1 * var(--admin-sidebar-w));
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="fa-solid fa-bolt text-white fs-6"></i>
            </div>
            Apex<span>Sports</span>
        </div>

        <div class="py-2">
            <div class="sidebar-heading">Core</div>
            <div class="nav-item-admin">
                <a href="{{ route('admin.dashboard') }}" class="nav-link-admin {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
            </div>
            <div class="nav-item-admin">
                <a href="{{ route('admin.calendar.index') }}" class="nav-link-admin {{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-days"></i> Booking Calendar
                </a>
            </div>

            <div class="sidebar-heading">Management</div>
            <div class="nav-item-admin">
                <a href="{{ route('admin.bookings.index') }}" class="nav-link-admin {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bookmark"></i> Reservations
                </a>
            </div>
            <div class="nav-item-admin">
                <a href="{{ route('admin.facilities.index') }}" class="nav-link-admin {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-building"></i> Sports Facilities
                </a>
            </div>
            <div class="nav-item-admin">
                <a href="{{ route('admin.courts.index') }}" class="nav-link-admin {{ request()->routeIs('admin.courts.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-vector-square"></i> Court Setup
                </a>
            </div>
            <div class="nav-item-admin">
                <a href="{{ route('admin.schedules.index') }}" class="nav-link-admin {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock"></i> Hours & Holidays
                </a>
            </div>

            <div class="sidebar-heading">Finance & Analytics</div>
            <div class="nav-item-admin">
                <a href="{{ route('admin.payments.index') }}" class="nav-link-admin {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-credit-card"></i> Payments & Ledger
                </a>
            </div>
            <div class="nav-item-admin">
                <a href="{{ route('admin.reports.index') }}" class="nav-link-admin {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-column"></i> Analytical Reports
                </a>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="sidebar-heading">System Access</div>
                <div class="nav-item-admin">
                    <a href="{{ route('admin.users.index') }}" class="nav-link-admin {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear"></i> User Management
                    </a>
                </div>
            @endif
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" type="button" id="sidebarToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="d-none d-md-block">
                    <span class="badge bg-primary-subtle text-primary me-2 font-heading px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-shield-halved me-1"></i> {{ strtoupper(auth()->user()->role) }} PORTAL
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm rounded-pill" target="_blank">
                    <i class="fa-solid fa-globe me-1"></i> Customer Site
                </a>

                <div class="dropdown">
                    <button class="btn btn-white border rounded-pill d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width:32px; height:32px; font-size:0.85rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="text-start d-none d-sm-block me-1">
                            <div class="small fw-bold text-dark lh-1">{{ auth()->user()->name }}</div>
                            <div class="small text-secondary lh-1 mt-1" style="font-size:0.75rem;">{{ auth()->user()->email }}</div>
                        </div>
                        <i class="fa-solid fa-chevron-down small text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fa-solid fa-user me-2 text-muted"></i> Profile Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>

    @include('partials.alerts')
    @stack('scripts')
</body>
</html>
