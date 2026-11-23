<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kios Sembako Azwa') — Inventory System</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* ===========================
           CSS RESET & BASE
        =========================== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-50:  #f0fdf4;
            --green-100: #dcfce7;
            --green-200: #bbf7d0;
            --green-400: #4ade80;
            --green-500: #22c55e;
            --green-600: #16a34a;
            --green-700: #15803d;
            --green-800: #166534;

            --gray-50:   #f9fafb;
            --gray-100:  #f3f4f6;
            --gray-200:  #e5e7eb;
            --gray-300:  #d1d5db;
            --gray-400:  #9ca3af;
            --gray-500:  #6b7280;
            --gray-600:  #4b5563;
            --gray-700:  #374151;
            --gray-800:  #1f2937;
            --gray-900:  #111827;

            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0,0,0,.05);
            --shadow:    0 1px 3px 0 rgba(0,0,0,.10), 0 1px 2px -1px rgba(0,0,0,.10);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,.10), 0 2px 4px -2px rgba(0,0,0,.10);
            --radius:    0.75rem;
            --radius-sm: 0.375rem;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-100);
            color: var(--gray-800);
            min-height: 100vh;
        }

        /* ===========================
           NAVBAR
        =========================== */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 0 1.5rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            background: var(--green-600);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .brand-text .brand-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--gray-900);
            line-height: 1.1;
        }

        .brand-text .brand-sub {
            font-size: 0.65rem;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 500;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
        }

        .nav-link {
            padding: 0.4rem 0.875rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-600);
            transition: all 0.15s ease;
        }

        .nav-link:hover { color: var(--gray-900); background: var(--gray-100); }
        .nav-link.active { color: var(--green-600); font-weight: 600; }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--green-600);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .user-info .user-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray-800);
            line-height: 1.2;
        }

        .user-info .user-role {
            font-size: 0.7rem;
            color: var(--gray-500);
        }

        .btn-logout {
            padding: 0.35rem 0.875rem;
            border: 1.5px solid var(--gray-300);
            border-radius: var(--radius-sm);
            background: white;
            color: var(--gray-600);
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-logout:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }

        /* ===========================
           MAIN CONTENT
        =========================== */
        .main-content {
            padding: 1.75rem 2rem;
            max-width: 1280px;
            margin: 0 auto;
        }

        /* ===========================
           BUTTONS
        =========================== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background: var(--green-600);
            color: white;
        }

        .btn-primary:hover { background: var(--green-700); }

        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 1.5px solid var(--gray-300);
        }

        .btn-secondary:hover { background: var(--gray-50); border-color: var(--gray-400); }

        .btn-danger {
            background: white;
            color: #ef4444;
            border: 1.5px solid #fecaca;
        }

        .btn-danger:hover { background: #fef2f2; }

        .btn-sm { padding: 0.3rem 0.7rem; font-size: 0.8rem; }

        /* ===========================
           CARDS
        =========================== */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
        }

        .card-body { padding: 1.25rem; }
        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* ===========================
           STAT CARDS
        =========================== */
        .stat-card {
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .stat-card.yellow::before  { background: #f59e0b; }
        .stat-card.green::before   { background: var(--green-500); }
        .stat-card.blue::before    { background: #3b82f6; }
        .stat-card.purple::before  { background: #8b5cf6; }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-500);
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1;
        }

        .stat-value .unit {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-500);
        }

        .stat-sub {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.35rem;
        }

        .stat-sub .badge-in  { color: var(--green-600); font-weight: 600; }
        .stat-sub .badge-out { color: #ef4444; font-weight: 600; }

        /* ===========================
           FILTER BUTTONS
        =========================== */
        .period-filter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .period-btn {
            padding: 0.4rem 0.875rem;
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            border: 1.5px solid var(--gray-300);
            background: white;
            color: var(--gray-600);
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .period-btn:hover { border-color: var(--green-500); color: var(--green-600); }

        .period-btn.active {
            background: var(--green-600);
            border-color: var(--green-600);
            color: white;
        }

        .btn-apply {
            padding: 0.4rem 0.875rem;
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            background: var(--green-600);
            color: white;
            transition: all 0.15s ease;
        }

        .btn-apply:hover { background: var(--green-700); }

        /* ===========================
           WELCOME BANNER
        =========================== */
        .welcome-banner {
            background: linear-gradient(135deg, var(--green-600) 0%, var(--green-500) 60%, var(--green-400) 100%);
            border-radius: var(--radius);
            padding: 1.5rem 2rem;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            right: -2rem;
            top: -2rem;
            width: 12rem;
            height: 12rem;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            right: 4rem;
            bottom: -3rem;
            width: 9rem;
            height: 9rem;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .welcome-banner h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .welcome-banner p {
            font-size: 0.85rem;
            opacity: 0.85;
            margin-bottom: 0.625rem;
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: rgba(255,255,255,0.2);
            border-radius: 1rem;
            padding: 0.25rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 500;
            backdrop-filter: blur(4px);
        }

        /* ===========================
           TABLE
        =========================== */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        thead th {
            padding: 0.75rem 1rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-500);
            border-bottom: 1px solid var(--gray-200);
            text-align: left;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--gray-100);
            transition: background 0.1s;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--gray-50); }

        tbody td {
            padding: 0.875rem 1rem;
            color: var(--gray-700);
            vertical-align: middle;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--gray-400);
        }

        .empty-state .empty-icon {
            font-size: 3rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .empty-state p { font-size: 0.875rem; }

        /* ===========================
           BADGES
        =========================== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.625rem;
            border-radius: 1rem;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .badge-success { background: var(--green-100); color: var(--green-700); }
        .badge-danger  { background: #fee2e2; color: #b91c1c; }
        .badge-info    { background: #dbeafe; color: #1d4ed8; }
        .badge-warning { background: #fef3c7; color: #92400e; }

        /* ===========================
           FORMS & INPUTS
        =========================== */
        .form-group { margin-bottom: 1rem; }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.375rem;
        }

        .form-control {
            width: 100%;
            padding: 0.55rem 0.875rem;
            border: 1.5px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            color: var(--gray-800);
            background: white;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--green-500);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
        }

        .form-control.is-invalid { border-color: #ef4444; }

        .invalid-feedback {
            font-size: 0.78rem;
            color: #ef4444;
            margin-top: 0.3rem;
        }

        /* ===========================
           MODAL
        =========================== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
        }

        .modal-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            background: white;
            border-radius: var(--radius);
            width: 100%;
            max-width: 480px;
            margin: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            transform: scale(0.95) translateY(-10px);
            transition: transform 0.2s ease;
        }

        .modal-overlay.open .modal-box { transform: scale(1) translateY(0); }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: var(--gray-400);
            transition: color 0.15s;
            display: flex;
            align-items: center;
        }

        .modal-close:hover { color: var(--gray-700); }

        .modal-body { padding: 1.25rem 1.5rem; }
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--gray-100);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* ===========================
           PAGE HEADER
        =========================== */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .page-title { font-size: 1.375rem; font-weight: 700; color: var(--gray-900); }
        .page-sub   { font-size: 0.8rem; color: var(--gray-500); margin-top: 0.2rem; }

        .page-actions { display: flex; align-items: center; gap: 0.625rem; }

        /* ===========================
           ALERT
        =========================== */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .alert-success {
            background: var(--green-50);
            border: 1px solid var(--green-200);
            color: var(--green-700);
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        /* ===========================
           SEARCH INPUT
        =========================== */
        .search-box {
            position: relative;
        }

        .search-box .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 0.9rem;
        }

        .search-box .form-control {
            padding-left: 2.25rem;
        }

        /* ===========================
           GRID
        =========================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 900px) {
            .charts-grid { grid-template-columns: 1fr; }
        }

        /* ===========================
           MISC
        =========================== */
        .text-green { color: var(--green-600); }
        .text-red   { color: #ef4444; }
        .text-muted { color: var(--gray-500); }
        .fw-600     { font-weight: 600; }
        .fw-700     { font-weight: 700; }

        .flex-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .action-btns {
            display: flex;
            gap: 0.4rem;
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }

        .select-input {
            padding: 0.5rem 2rem 0.5rem 0.75rem;
            border: 1.5px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            color: var(--gray-700);
            background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%236b7280' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") no-repeat right 0.6rem center;
            appearance: none;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.15s;
        }

        .select-input:focus { border-color: var(--green-500); }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-left">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <div class="brand-logo">KSA</div>
                <div class="brand-text">
                    <div class="brand-name">Kios Sembako Azwa</div>
                    <div class="brand-sub">Inventory System</div>
                </div>
            </a>

            <ul class="navbar-nav">
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.index') }}"
                       class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        Produk
                    </a>
                </li>
                <li>
                    <a href="{{ route('stock.index') }}"
                       class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                        Histori Stok
                    </a>
                </li>
                @if(Auth::user()->isAdmin())
                <li>
                    <a href="{{ route('laporan.index') }}"
                       class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        Laporan Keuangan
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <div class="navbar-right">
            <div class="user-badge">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
