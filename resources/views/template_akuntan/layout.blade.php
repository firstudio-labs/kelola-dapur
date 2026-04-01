<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset("admin") }}/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Akuntan - Kelola Dapur</title>
    @stack("styles")
    <link rel="icon" href="{{ asset("admin") }}/assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/css/demo.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/css/custom-badge.css" />
    <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset("admin") }}/assets/vendor/js/helpers.js"></script>
    <script src="{{ asset("admin") }}/assets/js/config.js"></script>
    <style>
        /* Mobile Bottom Navbar */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background-color: #fff;
            border-top: 1px solid #eaeaec;
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            padding-bottom: env(safe-area-inset-bottom); /* For iPhone notch */
        }
        .mobile-bottom-nav .nav-item {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #697a8d;
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .mobile-bottom-nav .nav-item i {
            font-size: 20px;
            margin-bottom: 2px;
        }
        .mobile-bottom-nav .nav-item.active {
            color: #696cff; /* Primary color */
            background-color: rgba(105, 108, 255, 0.08);
            font-weight: 700;
        }
        .mobile-bottom-nav .nav-item.active i {
            transform: translateY(-2px);
            transition: transform 0.2s;
        }
        
        @media (max-width: 767.98px) {
            .content-wrapper {
                padding-bottom: 80px !important;
            }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include("template_akuntan.sidebar")
            <div class="layout-page">
                <div class="content-wrapper">
                    @yield("content")
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <!-- Mobile Bottom Navbar -->
    <div class="mobile-bottom-nav d-md-none">
        <a href="{{ route('akuntan.dashboard') }}" class="nav-item {{ Request::is('akuntan/dashboard*') ? 'active' : '' }}">
            <i class="bx bx-home-circle"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('akuntan.transaksi.index') }}" class="nav-item {{ Request::is('akuntan/transaksi*') ? 'active' : '' }}">
            <i class="bx bx-transfer-alt"></i>
            <span>Transaksi</span>
        </a>
        <a href="{{ route('akuntan.buku-kas.index') }}" class="nav-item {{ Request::is('akuntan/buku-kas*') ? 'active' : '' }}">
            <i class="bx bx-book-open"></i>
            <span>Buku Kas</span>
        </a>
        <a href="{{ route('akuntan.profile.edit') }}" class="nav-item {{ Request::is('akuntan/profile*') ? 'active' : '' }}">
            <i class="bx bx-user"></i>
            <span>Profil</span>
        </a>
    </div>
    <script src="{{ asset("admin") }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset("admin") }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset("admin") }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset("admin") }}/assets/vendor/js/menu.js"></script>
    <script src="{{ asset("admin") }}/assets/js/main.js"></script>
    @stack("scripts")
</body>
</html>
