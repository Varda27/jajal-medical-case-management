<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Case Management') - Jajal Medical</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --medical-primary: #2563eb;
            --medical-primary-dark: #1d4ed8;
            --medical-dark: #0b1736;
            --medical-accent: #38bdf8;
            --medical-border: #e2e8f0;
        }
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
        }
        .navbar-medical {
            background: linear-gradient(135deg, #070d1e 0%, #0b1736 50%, #1e3a8a 100%);
            border-bottom: 3px solid #3b82f6;
        }
        .sidebar {
            background: #ffffff;
            min-height: calc(100vh - 70px);
            border-right: 1px solid var(--medical-border);
        }
        .sidebar .nav-link {
            color: #475569;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.15s ease-in-out;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #eff6ff;
            color: #1d4ed8;
        }
        .badge-status-draft { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-status-planning { background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
        .badge-status-in-review { background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-status-approved { background-color: #ecfdf5; color: #047857; border: 1px solid #6ee7b7; }
        .badge-status-completed { background-color: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .badge-status-cancelled { background-color: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .badge-priority-low { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .badge-priority-medium { background-color: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        .badge-priority-high { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .card-custom {
            border: 1px solid var(--medical-border);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-medical sticky-top py-2">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white" href="{{ route('dashboard') }}">
                <span class="p-1 rounded bg-info bg-opacity-25 text-info">
                    <i class="bi bi-hospital fs-4"></i>
                </span>
                <span>JAJAL <span class="text-info fw-normal">MEDICAL</span></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active text-white' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cases.*') ? 'active text-white' : '' }}" href="{{ route('cases.index') }}">
                            <i class="bi bi-folder2-open me-1"></i> Case Management
                        </a>
                    </li>
                </ul>

                @auth
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-md-block">
                        <div class="text-white fw-semibold small">{{ Auth::user()->name }}</div>
                        <div>
                            @if(Auth::user()->isAdmin())
                                <span class="badge bg-primary text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-shield-check me-1"></i>Admin</span>
                            @else
                                <span class="badge bg-secondary text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-eye me-1"></i>Viewer</span>
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="container-fluid px-4 py-4">
        <!-- Flash Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show card-custom border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2 text-success"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show card-custom border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show card-custom border-0 shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2 text-info"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
