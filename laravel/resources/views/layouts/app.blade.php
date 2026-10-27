<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Pencatatan Iuran Warga - Transparansi iuran lingkungan untuk masa depan yang lebih hijau">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Iuran Warga - Warga Bijak, Lingkungan Nyaman')</title>
    <link rel="icon" type="image/png" href="{{ asset('logogcr.png') }}?v=2">
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    {{-- Lucide Icons --}}
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.292.0/dist/umd/lucide.min.js"></script>
    
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #166534;
            --primary-light: #22c55e;
            --primary-lighter: #dcfce7;
            --primary-lightest: #fff7ed;
            --primary-dark: #14532d;
            --bg: #f8faf9;
            --white: #ffffff;
            --text: #1a1a2e;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border: #e5e7eb;
            --border-light: #f3f4f6;
            --orange: #f97316;
            --orange-light: #fff7ed;
            --red: #ef4444;
            --red-light: #fef2f2;
            --yellow: #eab308;
            --yellow-light: #fefce8;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --radius: 16px;
            --radius-sm: 10px;
            --radius-xs: 6px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .navbar-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .navbar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--primary-lighter);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
        }

        .navbar-nav a {
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: var(--radius-xs);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .navbar-nav a:hover,
        .navbar-nav a.active {
            color: var(--primary);
            background: var(--primary-lightest);
        }

        /* Navbar Dropdowns */
        .nav-item-dropdown {
            position: relative;
        }

        .nav-dropdown-trigger {
            cursor: pointer;
            padding: 8px 16px;
            border-radius: var(--radius-xs);
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            background: transparent;
            border: none;
            font-family: inherit;
        }

        .nav-item-dropdown:hover .nav-dropdown-trigger,
        .nav-item-dropdown.active .nav-dropdown-trigger {
            color: var(--primary);
            background: var(--primary-lightest);
        }

        .nav-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--white);
            min-width: 200px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 8px;
            z-index: 1000;
            margin-top: 10px;
            animation: navFadeIn 0.2s ease;
        }

        /* Bridge the gap to prevent hover loss */
        .nav-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -12px;
            left: 0;
            right: 0;
            height: 12px;
            background: transparent;
        }

        @keyframes navFadeIn {
            from { opacity: 0; transform: translateX(-50%) translateY(10px); }
            to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        .nav-item-dropdown:hover .nav-dropdown-menu {
            display: block;
        }

        .nav-dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-dropdown-item:hover {
            background: var(--primary-lightest);
            color: var(--primary);
        }

        .nav-dropdown-item .item-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-dropdown-item i {
            width: 16px;
            height: 16px;
        }

        .nav-dropdown-item.active {
            color: var(--primary);
            background: var(--primary-lightest);
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-badge {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--primary);
            background: var(--primary-lightest);
            border: 1px solid rgba(34,197,94,0.25);
            padding: 5px 12px;
            border-radius: 20px;
        }

        .btn-login-nav {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: var(--radius-xs);
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            background: var(--primary);
            color: var(--white);
        }
        .btn-login-nav:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(22,101,52,0.25);
        }

        .btn-logout-nav {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--radius-xs);
            font-size: 0.84rem;
            font-weight: 600;
            border: 1.5px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            background: transparent;
            color: var(--text-secondary);
        }
        .btn-logout-nav:hover {
            background: var(--red-light);
            border-color: var(--red);
            color: var(--red);
        }

        @media (max-width: 768px) {
            .btn-logout-nav {
                display: none;
            }
        }

        /* Main Content */
        .main-content {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 3rem 1rem 2rem;
        }

        .hero-icon {
            width: 56px;
            height: 56px;
            background: var(--primary-lighter);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .hero h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .hero p {
            color: var(--text-secondary);
            font-size: 1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        /* Saldo Card */
        .saldo-card {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem 2.5rem;
            text-align: center;
            max-width: 400px;
            margin: 1.5rem auto 2rem;
            box-shadow: 0 8px 32px rgba(22, 101, 52, 0.3);
            position: relative;
            overflow: hidden;
        }

        .saldo-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .saldo-label {
            font-size: 0.85rem;
            opacity: 0.85;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .saldo-amount {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -1px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-light);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon.green {
            background: var(--primary-lighter);
            color: var(--primary);
        }

        .stat-icon.orange {
            background: var(--orange-light);
            color: var(--orange);
        }

        .stat-icon.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .stat-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .stat-badge.positive {
            background: var(--primary-lighter);
            color: var(--primary);
        }

        .stat-badge.negative {
            background: var(--red-light);
            color: var(--red);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.35rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }

        .stat-sub {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        /* Card */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-light);
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
        }

        /* Table */
        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        thead th {
            text-align: left;
            padding: 0.75rem 1rem;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.8rem;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }

        tbody tr {
            transition: background 0.15s;
        }

        tbody tr:hover {
            background: #f6b983ff;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Badge */
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .badge-success {
            background: var(--primary-lighter);
            color: var(--primary);
        }

        .badge-danger {
            background: var(--red-light);
            color: var(--red);
        }

        .badge-warning {
            background: var(--yellow-light);
            color: #a16207;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: var(--radius-xs);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.3);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }

        .btn-outline:hover {
            background: var(--primary-lightest);
            border-color: var(--primary-light);
            color: var(--primary);
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: 0.78rem;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            padding: 6px 10px;
        }

        .btn-ghost:hover {
            background: var(--border-light);
            color: var(--text);
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 280px;
            width: 100%;
        }

        .chart-legend {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 0.75rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        /* Expense list */
        .expense-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 0;
            border-bottom: 1px solid var(--border-light);
        }

        .expense-row:last-child {
            border-bottom: none;
        }

        .expense-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .expense-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary);
        }

        .expense-name {
            font-weight: 500;
            color: var(--text);
            font-size: 0.9rem;
        }

        .expense-amount {
            font-weight: 600;
            color: var(--text);
            font-size: 0.9rem;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
            animation: modalIn 0.25s ease;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text);
        }

        /* Forms */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.35rem;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-xs);
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--text);
            background: var(--white);
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
        }

        select.form-input {
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideDown 0.3s;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: var(--primary-lighter);
            color: var(--primary);
            border: 1px solid rgba(22, 101, 52, 0.2);
        }

        .alert-error {
            background: var(--red-light);
            color: var(--red);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Pagination */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-light);
        }

        .pagination nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .pagination nav > div:first-child {
            display: none; /* Hide 'Showing...' text on small screens if desired, or keep generic */
        }

        @media (min-width: 640px) {
            .pagination nav > div:first-child {
                display: block;
                color: var(--text-secondary);
                font-size: 0.85rem;
            }
        }

        .pagination nav svg {
            width: 16px;
            height: 16px;
        }

        .pagination .flex.justify-between, 
        .pagination .hidden.sm\:flex-1 {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: var(--radius-xs);
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            color: var(--text-secondary);
            background: var(--white);
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .pagination a:hover {
            background: var(--primary-lightest);
            color: var(--primary);
            border-color: var(--primary-light);
        }

        .pagination span[aria-current="page"] {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        .pagination span[aria-disabled="true"] {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--bg);
        }

        /* Fixing the SVG container in default laravel pagination */
        .pagination nav div div span span,
        .pagination nav div div a {
            margin: 0 2px;
        }

        /* ===== Hamburger Menu Button ===== */
        .hamburger-btn {
            display: none;
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 8px;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .hamburger-btn:hover {
            background: var(--primary-lightest);
            border-color: var(--primary-light);
            color: var(--primary);
        }

        /* ===== Mobile Sidebar Overlay ===== */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .mobile-overlay.active {
            display: block;
            opacity: 1;
        }

        .mobile-sidebar {
            position: fixed;
            top: 0;
            right: -300px;
            width: 280px;
            max-width: 85vw;
            height: 100%;
            background: var(--white);
            z-index: 1000;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
            transition: right 0.3s ease;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .mobile-sidebar.active {
            right: 0;
        }
        .mobile-sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }
        .mobile-sidebar-close {
            background: none;
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s;
        }
        .mobile-sidebar-close:hover {
            background: var(--red-light);
            border-color: var(--red);
            color: var(--red);
        }
        .mobile-sidebar-nav {
            padding: 0.75rem;
            flex: 1;
        }
        .mobile-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s;
            margin-bottom: 2px;
        }
        .mobile-nav-item:hover,
        .mobile-nav-item.active {
            background: var(--primary-lightest);
            color: var(--primary);
        }
        .mobile-nav-group-title {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px 6px;
            margin-top: 4px;
        }
        .mobile-nav-divider {
            height: 1px;
            background: var(--border-light);
            margin: 6px 0;
        }
        .mobile-sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
        }

        /* ===== Page Header Responsive ===== */
        .page-header-responsive {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        /* ===== TABLET (769px - 1024px) ===== */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .stat-card:last-child {
                grid-column: span 2;
            }
            .main-content {
                padding: 1.5rem;
            }
            .navbar {
                padding: 0 1rem;
            }
            .chart-container {
                height: 240px;
            }
        }

        /* ===== PHONE (≤768px) ===== */
        @media (max-width: 768px) {
            /* Show hamburger, hide desktop nav */
            .hamburger-btn {
                display: flex;
            }
            .navbar-nav {
                display: none;
            }
            .navbar-actions .admin-badge span,
            .navbar-actions .btn-logout-nav span,
            .navbar-actions .btn-login-nav span {
                display: none;
            }
            .navbar-actions .admin-badge {
                padding: 5px 8px;
            }
            .navbar-actions .btn-logout-nav,
            .navbar-actions .btn-login-nav {
                padding: 7px 8px;
            }
            .navbar-actions {
                gap: 6px;
            }

            /* Layout */
            .main-content {
                padding: 1rem;
            }
            .card {
                padding: 1rem;
                border-radius: 12px;
            }
            .card-header {
                flex-direction: column;
                gap: 0.75rem;
                align-items: flex-start;
            }

            /* Stats */
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            .stat-card:last-child {
                grid-column: span 1;
            }
            .stat-card {
                padding: 1rem;
            }
            .stat-value {
                font-size: 1.25rem;
            }

            /* Hero */
            .hero {
                padding: 2rem 0.5rem 1.5rem;
            }
            .hero h1 {
                font-size: 1.5rem;
            }
            .hero p {
                font-size: 0.88rem;
            }

            /* Saldo */
            .saldo-card {
                padding: 1.25rem 1.5rem;
            }
            .saldo-amount {
                font-size: 1.7rem;
            }

            /* Chart */
            .chart-container {
                height: 200px;
            }

            /* Tables */
            .table-wrapper {
                margin: 0 -1rem;
                padding: 0 0;
                border-radius: 0;
            }
            table {
                font-size: 0.8rem;
            }
            thead th {
                padding: 0.6rem 0.75rem;
                font-size: 0.72rem;
            }
            tbody td {
                padding: 0.65rem 0.75rem;
            }

            /* Modal */
            .modal {
                width: 95%;
                padding: 1.5rem;
                border-radius: 16px 16px 0 0;
                max-height: 90vh;
                margin-bottom: 0;
                align-self: flex-end;
            }
            .modal-overlay.active {
                align-items: flex-end;
            }

            /* Forms touch friendly */
            .form-input {
                min-height: 44px;
                font-size: 16px; /* prevent iOS zoom */
            }
            .btn {
                min-height: 44px;
                padding: 10px 16px;
            }

            /* Page headers stack on phone */
            .page-header-responsive {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            .page-header-responsive .btn,
            .page-header-responsive > div:last-child {
                width: 100%;
            }
            .page-header-responsive .btn {
                justify-content: center;
            }

            /* Footer */
            footer {
                padding: 1.5rem 1rem !important;
            }
        }

        /* ===== SMALL PHONE (≤480px) ===== */
        @media (max-width: 480px) {
            .navbar-inner {
                height: 56px;
            }
            .navbar-brand span {
                font-size: 0.95rem;
            }
            .hero h1 {
                font-size: 1.3rem;
            }
            .hero-icon {
                width: 48px;
                height: 48px;
                border-radius: 12px;
            }
            .saldo-amount {
                font-size: 1.5rem;
            }
            .stat-label {
                font-size: 0.72rem;
            }
            .stat-value {
                font-size: 1.1rem;
            }
            .card-title {
                font-size: 0.95rem;
            }
            .main-content {
                padding: 0.75rem;
            }
        }

        /* Utility */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mb-4 { margin-bottom: 1rem; }
    </style>

    @yield('styles')
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <img src="{{ asset('logogcr.png') }}" alt="Logo" style="height:45px; width:auto; object-fit:contain;">
            </a>
            <ul class="navbar-nav">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @auth
                {{-- Dropdown Data Master --}}
                <li class="nav-item-dropdown {{ request()->routeIs('warga.*', 'users.*') ? 'active' : '' }}">
                    <button class="nav-dropdown-trigger">
                        <i data-lucide="database" style="width:16px;height:16px"></i>
                        <span>Data Master</span>
                        <i data-lucide="chevron-down" style="width:12px;height:12px"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="{{ route('warga.index') }}" class="nav-dropdown-item {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                            <div class="item-content">
                                <i data-lucide="users"></i>
                                <span>Warga Terdaftar</span>
                            </div>
                        </a>
                        <a href="{{ route('users.index') }}" class="nav-dropdown-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <div class="item-content">
                                <i data-lucide="user-cog"></i>
                                <span>Manajemen Admin</span>
                            </div>
                        </a>
                    </div>
                </li>

                {{-- Dropdown Keuangan --}}
                <li class="nav-item-dropdown {{ request()->routeIs('pembayaran.*', 'pemasukan_lain.*', 'pengeluaran.*') ? 'active' : '' }}">
                    <button class="nav-dropdown-trigger">
                        <i data-lucide="calculator" style="width:16px;height:16px"></i>
                        <span>Keuangan</span>
                        <i data-lucide="chevron-down" style="width:12px;height:12px"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="{{ route('pembayaran.index') }}" class="nav-dropdown-item {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}">
                            <div class="item-content">
                                <i data-lucide="wallet"></i>
                                <span>Pembayaran Iuran</span>
                            </div>
                        </a>
                        <a href="{{ route('pemasukan_lain.index') }}" class="nav-dropdown-item {{ request()->routeIs('pemasukan_lain.*') ? 'active' : '' }}">
                            <div class="item-content">
                                <i data-lucide="trending-up"></i>
                                <span>Pemasukan Lain</span>
                            </div>
                        </a>
                        <div style="height: 1px; background: var(--border-light); margin: 4px 8px;"></div>
                        <a href="{{ route('pengeluaran.index') }}" class="nav-dropdown-item {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">
                            <div class="item-content">
                                <i data-lucide="receipt"></i>
                                <span>Catat Pengeluaran</span>
                            </div>
                        </a>
                    </div>
                </li>

                <li>
                    <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i data-lucide="settings" style="width:16px;height:16px"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
                @endauth
            </ul>
            {{-- Auth Actions --}}
            <div class="navbar-actions">
                @auth
                    <div class="admin-badge">
                        <i data-lucide="shield-check" style="width:14px;height:14px"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0">
                        @csrf
                        <button type="submit" class="btn-logout-nav">
                            <i data-lucide="log-out" style="width:15px;height:15px"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-login-nav">
                        <i data-lucide="log-in" style="width:15px;height:15px"></i>
                        <span>Login Admin</span>
                    </a>
                @endauth
                {{-- Hamburger Button --}}
                <button class="hamburger-btn" id="hamburgerBtn" aria-label="Menu">
                    <i data-lucide="menu" style="width:20px;height:20px"></i>
                </button>
            </div>
        </div>
    </nav>

    {{-- Mobile Sidebar --}}
    <div class="mobile-overlay" id="mobileOverlay"></div>
    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-sidebar-header">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <img src="{{ asset('logogcr.png') }}" alt="Logo" style="height:36px; width:auto; object-fit:contain;">
            </a>
            <button class="mobile-sidebar-close" id="closeSidebar">
                <i data-lucide="x" style="width:16px;height:16px"></i>
            </button>
        </div>
        <div class="mobile-sidebar-nav">
            <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" style="width:18px;height:18px"></i>
                Dashboard
            </a>
            @auth
            <div class="mobile-nav-group-title">Data Master</div>
            <a href="{{ route('warga.index') }}" class="mobile-nav-item {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                <i data-lucide="users" style="width:18px;height:18px"></i>
                Warga Terdaftar
            </a>
            <a href="{{ route('users.index') }}" class="mobile-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i data-lucide="user-cog" style="width:18px;height:18px"></i>
                Manajemen Admin
            </a>
            <div class="mobile-nav-divider"></div>
            <div class="mobile-nav-group-title">Keuangan</div>
            <a href="{{ route('pembayaran.index') }}" class="mobile-nav-item {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}">
                <i data-lucide="wallet" style="width:18px;height:18px"></i>
                Pembayaran Iuran
            </a>
            <a href="{{ route('pemasukan_lain.index') }}" class="mobile-nav-item {{ request()->routeIs('pemasukan_lain.*') ? 'active' : '' }}">
                <i data-lucide="trending-up" style="width:18px;height:18px"></i>
                Pemasukan Lain
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="mobile-nav-item {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">
                <i data-lucide="receipt" style="width:18px;height:18px"></i>
                Catat Pengeluaran
            </a>
            <div class="mobile-nav-divider"></div>
            <a href="{{ route('settings.index') }}" class="mobile-nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i data-lucide="settings" style="width:18px;height:18px"></i>
                Pengaturan
            </a>
            @endauth
        </div>
        <div class="mobile-sidebar-footer">
            @auth
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:0.75rem;">
                    <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-lighter);display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="user" style="width:16px;height:16px;color:var(--primary);"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:0.88rem;">{{ auth()->user()->name }}</div>
                        <div style="font-size:0.72rem;color:var(--text-secondary);">Administrator</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center;">
                        <i data-lucide="log-out" style="width:16px;height:16px"></i>
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary" style="width:100%;justify-content:center;text-decoration:none;">
                    <i data-lucide="log-in" style="width:16px;height:16px"></i>
                    Login Admin
                </a>
            @endauth
        </div>
    </div>

    {{-- Main Content --}}
    <main class="main-content">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer style="text-align:center; padding:2rem 1rem; color:var(--text-muted); font-size:0.82rem;">
        <p>&copy; {{ date('Y') }} Iuran Warga — Warga Bijak, Lingkungan Nyaman</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize Lucide icons after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            
            // Toast configuration
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Flash Messages using Toast
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                });
            @endif

            // Navbar Dropdown Toggle for Mobile/Click
            document.querySelectorAll('.nav-dropdown-trigger').forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        const parent = this.parentElement;
                        parent.classList.toggle('active');
                        
                        // Close other dropdowns
                        document.querySelectorAll('.nav-item-dropdown').forEach(item => {
                            if (item !== parent) item.classList.remove('active');
                        });
                    }
                });
            });

            // Mobile Sidebar Toggle
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const closeSidebar = document.getElementById('closeSidebar');

            function openSidebar() {
                mobileSidebar.classList.add('active');
                mobileOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebarFn() {
                mobileSidebar.classList.remove('active');
                mobileOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (hamburgerBtn) hamburgerBtn.addEventListener('click', openSidebar);
            if (closeSidebar) closeSidebar.addEventListener('click', closeSidebarFn);
            if (mobileOverlay) mobileOverlay.addEventListener('click', closeSidebarFn);
        });

        // Format number to Rupiah
        function formatRupiah(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        }
    </script>

    @yield('scripts')
</body>
</html>
