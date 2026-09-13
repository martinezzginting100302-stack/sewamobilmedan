<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sewa Mobil Medan')</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            display: flex;
            min-height: 100vh;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 240px;
            background: #0f172a;
            color: #cbd5e1;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 20;
        }

        .brand {
            padding: 22px 24px;
            border-bottom: 1px solid #1e293b;
        }

        .brand a {
            color: #fff;
            text-decoration: none;
            font-size: 19px;
            font-weight: 700;
            letter-spacing: .2px;
        }

        .brand small {
            display: block;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 400;
            margin-top: 2px;
        }

        .nav {
            flex: 1;
            padding: 14px 0;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14.5px;
            border-left: 3px solid transparent;
            transition: background .15s, color .15s;
        }

        .nav a:hover {
            background: #1e293b;
            color: #fff;
        }

        .nav a.active {
            background: #1e293b;
            color: #fff;
            border-left-color: #2563eb;
        }

        .nav .icons {
            width: 18px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid #1e293b;
            font-size: 12px;
            color: #64748b;
        }

        /* ===== Main ===== */
        .main {
            flex: 1;
            margin-left: 240px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar .page-title {
            font-size: 17px;
            font-weight: 600;
        }

        .topbar .userbox {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #2563eb;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
        }

        .userbox .name {
            font-size: 14px;
            font-weight: 600;
        }

        .userbox .email {
            font-size: 12px;
            color: #64748b;
        }

        .btn-logout {
            background: #fee2e2;
            color: #b91c1c;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-logout:hover { background: #fecaca; }

        .content {
            padding: 32px;
            flex: 1;
        }

        /* ===== Alerts ===== */
        .alert {
            padding: 13px 18px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert ul { margin: 0; padding-left: 20px; }
        .alert ul li { margin: 3px 0; }

        /* ===== Cards ===== */
        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
        }

        .card + .card { margin-top: 24px; }

        .card h2 {
            margin: 0 0 18px;
            font-size: 17px;
            font-weight: 600;
        }

        /* ===== Buttons ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s;
        }

        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }

        .btn-secondary { background: #64748b; color: #fff; }
        .btn-secondary:hover { background: #475569; }

        .btn-success { background: #059669; color: #fff; }
        .btn-success:hover { background: #047857; }

        .btn-warning { background: #d97706; color: #fff; }
        .btn-warning:hover { background: #b45309; }

        .btn-danger { background: #dc2626; color: #fff; }
        .btn-danger:hover { background: #b91c1c; }

        .btn-sm { padding: 6px 11px; font-size: 12.5px; }

        .btn-outline {
            background: #fff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-outline:hover { background: #f8fafc; }

        /* ===== Table ===== */
        .table-wrap { overflow-x: auto; }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table th {
            text-align: left;
            background: #f8fafc;
            color: #475569;
            padding: 11px 14px;
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table td {
            padding: 12px 14px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
        }

        .table tbody tr:hover { background: #f8fafc; }

        /* ===== Badge ===== */
        .badge {
            display: inline-block;
            padding: 4px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-tersedia    { background: #dcfce7; color: #166534; }
        .badge-disewa      { background: #dbeafe; color: #1d4ed8; }
        .badge-maintenance { background: #fef3c7; color: #92400e; }
        .badge-menunggu    { background: #fef3c7; color: #92400e; }
        .badge-dikonfirmasi{ background: #dbeafe; color: #1d4ed8; }
        .badge-selesai     { background: #dcfce7; color: #166534; }
        .badge-dibatalkan  { background: #fee2e2; color: #b91c1c; }

        /* ===== Form ===== */
        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
        }

        .form-group .hint { font-size: 12px; color: #94a3b8; margin-top: 4px; }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            background: #fff;
            color: #1e293b;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        textarea.form-control { resize: vertical; }

        .form-inline {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-inline .form-group { margin-bottom: 0; }
        .form-inline .form-control { width: 220px; }

        .mb-2 { margin-bottom: 16px; }
        .d-flex { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .mt-2 { margin-top: 16px; }
        .text-muted { color: #64748b; font-size: 12.5px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* ===== Stats ===== */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 22px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
        }

        .stat .label {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .stat .value {
            font-size: 30px;
            font-weight: 700;
            margin-top: 8px;
        }

        .stat .sub { font-size: 12.5px; color: #94a3b8; margin-top: 4px; }

        .stat-blue    { border-top: 3px solid #2563eb; }
        .stat-green   { border-top: 3px solid #059669; }
        .stat-amber   { border-top: 3px solid #d97706; }
        .stat-red     { border-top: 3px solid #dc2626; }
        .stat-indigo  { border-top: 3px solid #6366f1; }

        /* ===== Car photo ===== */
        .car-photo {
            width: 90px;
            height: 62px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
        }

        .car-photo-lg {
            width: 100%;
            max-width: 420px;
            height: 240px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
        }

        .placeholder-photo {
            width: 90px;
            height: 62px;
            border-radius: 6px;
            background: #e2e8f0;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .placeholder-photo-lg {
            width: 100%;
            max-width: 420px;
            height: 240px;
            border-radius: 10px;
            background: #e2e8f0;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== Detail table ===== */
        .detail-table { width: 100%; border-collapse: collapse; font-size: 14px; }

        .detail-table th {
            text-align: left;
            width: 230px;
            padding: 12px 14px;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid #eef2f7;
            background: #f8fafc;
        }

        .detail-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #eef2f7;
        }

        /* ===== Empty state ===== */
        .empty {
            text-align: center;
            padding: 46px 20px;
            color: #94a3b8;
        }
        .empty .big { font-size: 40px; margin-bottom: 8px; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .content { padding: 18px; }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="brand">
            <a href="{{ route('dashboard') }}">🚗 SewaMobilMedan</a>
            <small>Rental Mobil Medan</small>
        </div>

        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="icons">📊</span> Dashboard
            </a>
            <a href="{{ route('cars.index') }}" class="{{ request()->routeIs('cars.*') ? 'active' : '' }}">
                <span class="icons">🚙</span> Data Mobil
            </a>
            <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <span class="icons">📅</span> Booking
            </a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <span class="icons">📈</span> Laporan
            </a>
        </nav>

        <div class="sidebar-footer">
            © {{ date('Y') }} SewaMobilMedan
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="page-title">@yield('page_title', 'Dashboard')</div>

            <div class="userbox">
                <div>
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="email">{{ Auth::user()->email }}</div>
                </div>

                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-logout">Keluar</button>
                </form>
            </div>
        </header>

        <main class="content">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </main>
    </div>

</body>
</html>