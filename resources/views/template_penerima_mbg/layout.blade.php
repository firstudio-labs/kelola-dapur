<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('admin') }}/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Penerima MBG - @yield('title', 'Dashboard')</title>
    <meta name="description" content="Portal Penerima MBG" />

    @stack('styles')

    <link rel="icon" href="{{ asset('admin') }}/assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/demo.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
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
            font-weight: 500;
        }
        
        /* Padding bottom for main content to avoid overlap with bottom nav */
        @media (max-width: 767.98px) {
            .layout-page, .content-wrapper, .container-xxl {
                padding-bottom: 100px !important;
            }
        }
    </style>
    <script src="{{ asset('admin') }}/assets/vendor/js/helpers.js"></script>
    <script src="{{ asset('admin') }}/assets/js/config.js"></script>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('template_penerima_mbg.sidebar')
            <div class="layout-page">
                <div class="content-wrapper">
                    @yield('content')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <div class="mobile-bottom-nav d-md-none">
        <a href="{{ route('penerima-mbg.dashboard') }}" class="nav-item {{ request()->routeIs('penerima-mbg.dashboard') ? 'active' : '' }}">
            <i class="bx bx-home-circle"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('penerima-mbg.history.index') }}" class="nav-item {{ request()->routeIs('penerima-mbg.history.*') ? 'active' : '' }}">
            <i class="bx bx-history"></i>
            <span>History</span>
        </a>
        <a href="{{ route('penerima-mbg.profile.edit') }}" class="nav-item {{ request()->routeIs('penerima-mbg.profile.*') ? 'active' : '' }}">
            <i class="bx bx-user"></i>
            <span>Profil</span>
        </a>
    </div>

    @yield('script')
    <script src="{{ asset('admin') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/js/menu.js"></script>
    <script src="{{ asset('admin') }}/assets/js/main.js"></script>
    @stack('js-internal')
    @yield('scripts')
    @stack('scripts')
</body>
</html>
