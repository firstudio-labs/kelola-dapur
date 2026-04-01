<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    @php
        $isSubscriptionActive = session('is_subscription_active', false);
        $subscriptionStatus = session('subscription_status', null);
        $idDapur = session('id_dapur', $dapur->id_dapur ?? ($dapur ?? null));
    @endphp    
    <div class="app-brand demo d-flex align-items-center justify-content-between px-3 py-2">
        
        <a href="/" class="app-brand-link d-flex align-items-center text-decoration-none">
            <span class="app-brand-logo demo">
                <img src="{{ asset('logo_kelola_dapur_black.png') }}" alt="Logo" style="height: 45px; width: auto" />
            </span>
        </a>
    </div>

    <div class="menu-container d-flex flex-column h-100">
        
        <div class="user-profile-section mt-3 px-3 pb-3">
            <div class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center w-100 p-2 rounded"
                    href="javascript:void(0);" data-bs-toggle="dropdown"
                    style="
                        background: rgba(255, 255, 255, 0.15);
                        transition: all 0.3s ease;
                        border: 1px solid rgba(255, 255, 255, 0.2);
                    "
                    onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <div class="avatar avatar-online me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                        <img src="{{ auth()->user()->adminGudang && auth()->user()->adminGudang->foto_diri ? Storage::url(auth()->user()->adminGudang->foto_diri) : asset('admin/assets/img/avatars/1.png') }}"
                            alt class="rounded-circle" style="width: 100% !important; height: 100% !important; object-fit: cover !important;" />
                    </div>
                    <div class="flex-grow-1 text-start user-info">
                        <div class="fw-semibold text-black">
                            {{ auth()->user()->nama ?? 'Unknown' }}
                        </div>
                        <small class="text-muted">
                            {{ ucfirst(str_replace('_', ' ', session('role_type', 'Unknown'))) }}
                        </small>
                        @if (session('subscription_status') && session('subscription_status') !== 'active')
                            <small class="text-warning d-block">
                                <i class="bx bx-warning-alt bx-xs"></i>
                                @if (session('subscription_status') === 'expired')
                                    Subscription Expired
                                @elseif (session('subscription_status') === 'expiring_soon')
                                    Expires in {{ session('subscription_days_left', 0) }} days
                                @else
                                    {{ ucfirst(str_replace('_', ' ', session('subscription_status'))) }}
                                @endif
                            </small>
                        @endif
                    </div>
                    <i class="bx bx-chevron-up user-chevron"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <img src="{{ auth()->user()->adminGudang && auth()->user()->adminGudang->foto_diri ? Storage::url(auth()->user()->adminGudang->foto_diri) : asset('admin/assets/img/avatars/1.png') }}"
                                            alt class="rounded-circle" style="width: 100% !important; height: 100% !important; object-fit: cover !important;" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">
                                        {{ auth()->user()->nama ?? 'Unknown' }}
                                    </span>
                                    <small class="text-muted">
                                        {{ ucfirst(str_replace('_', ' ', session('role_type', 'Unknown'))) }}
                                    </small>
                                    @if (session('subscription_end'))
                                        <small class="text-info d-block">
                                            Dapur: {{ session('dapur_name', 'Tidak Tersedia') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            Expires:
                                            {{ session('subscription_end') ? \Carbon\Carbon::parse(session('subscription_end'))->format('d M Y') : 'N/A' }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin-gudang.profile.edit', ['dapur' => $idDapur]) }}">
                            <i class="bx bx-edit me-2"></i>
                            <span class="align-middle">Edit Profil</span>
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">Log Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>


        @if (!$isSubscriptionActive && $subscriptionStatus)
            <div class="px-3 mb-3">
                <div class="alert alert-warning alert-dismissible fade show py-2 px-3" role="alert"
                    style="font-size: 0.875rem; line-height: 1.2">
                    <div class="d-flex align-items-start">
                        <i class="bx bx-info-circle me-2 mt-1"></i>
                        <div>
                            @if ($subscriptionStatus === 'expired')
                                <strong>Subscription Expired!</strong>
                                <br />
                                <small>Contact Kepala Dapur to renew</small>
                            @elseif ($subscriptionStatus === 'expiring_soon')
                                <strong>Subscription Expiring!</strong>
                                <br />
                                <small>
                                    {{ session('subscription_days_left', 0) }} days remaining
                                </small>
                            @else
                                <strong>Limited Access</strong>
                                <br />
                                <small>Contact Kepala Dapur for renewal</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <ul class="menu-inner py-1 flex-grow-1">
            
            <li class="menu-item {{ request()->routeIs('admin-gudang.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin-gudang.dashboard', ['dapur' => $idDapur]) }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>

            @if ($isSubscriptionActive)
                
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Admin Gudang</span>
                </li>

                <li class="menu-item {{ request()->routeIs('admin-gudang.stock.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-package"></i>
                        <div data-i18n="Kelola Stok">Kelola Stok</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('admin-gudang.stock.index') ? 'active' : '' }}">
                            <a href="{{ route('admin-gudang.stock.index', ['dapur' => $idDapur]) }}" class="menu-link">
                                <div data-i18n="Daftar Stok">Daftar Stok</div>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-item {{ request()->routeIs('admin-gudang.laporan-kekurangan.*') ? 'active' : '' }}">
                    <a href="{{ route('admin-gudang.laporan-kekurangan.index', ['dapur' => $idDapur]) }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-error"></i>
                        <div data-i18n="Laporan Kekurangan Stok">
                            Laporan Kekurangan Stok
                        </div>
                    </a>
                </li>
            @else
                
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text text-warning">
                        Limited Access
                    </span>
                </li>

                @php
                    $disabledMenus = [
                        'Kelola Stok' => 'bx-package',
                        'Laporan Kekurangan' => 'bx-error',
                    ];
                @endphp

                @foreach ($disabledMenus as $menuName => $icon)
                    <li class="menu-item disabled" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Subscription required to access this feature">
                        <a href="javascript:void(0);" class="menu-link text-muted"
                            style="cursor: not-allowed; opacity: 0.6">
                            <i class="menu-icon tf-icons bx {{ $icon }}"></i>
                            <div>{{ $menuName }}</div>
                            <i class="bx bx-lock-alt ms-auto text-warning"></i>
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</aside>

<nav class="mobile-bottom-nav d-lg-none" id="mobileBottomNav">
    <div class="bottom-nav-container">
        @php
            $mainMenus = [];
            
            // Menu Akun (selalu ada)
            $akunSubmenu = [
                ['type' => 'link', 'label' => 'Edit', 'url' => '#'],
                ['type' => 'logout', 'label' => 'Logout', 'url' => route('logout'), 'method' => 'POST'],
            ];
            
            if ($isSubscriptionActive) {
                $mainMenus = [
                    ['icon' => 'bx-user', 'label' => 'Akun', 'hasSubmenu' => true, 'submenu' => $akunSubmenu],
                    ['route' => 'admin-gudang.dashboard', 'icon' => 'bx-home-circle', 'label' => 'Dashboard', 'param' => ['dapur' => $idDapur]],
                    ['route' => 'admin-gudang.stock.index', 'icon' => 'bx-package', 'label' => 'Stok', 'param' => ['dapur' => $idDapur], 'hasSubmenu' => true, 'submenu' => [
                        ['type' => 'link', 'label' => 'Daftar Stok', 'url' => route('admin-gudang.stock.index', ['dapur' => $idDapur])],
                    ]],
                    ['route' => 'admin-gudang.laporan-kekurangan.index', 'icon' => 'bx-error', 'label' => 'Laporan Kekurangan', 'param' => ['dapur' => $idDapur]],
                ];
            } else {
                $mainMenus = [
                    ['icon' => 'bx-user', 'label' => 'Akun', 'hasSubmenu' => true, 'submenu' => $akunSubmenu],
                    ['route' => 'admin-gudang.dashboard', 'icon' => 'bx-home-circle', 'label' => 'Dashboard', 'param' => ['dapur' => $idDapur]],
                ];
            }
        @endphp

        @foreach ($mainMenus as $menu)
            @if(isset($menu['hasSubmenu']) && $menu['hasSubmenu'])
                @php
                    // Build submenu with full URLs
                    $submenuWithUrls = [];
                    foreach($menu['submenu'] as $sub) {
                        $submenuWithUrls[] = [
                            'label' => $sub['label'],
                            'url' => $sub['url'],
                            'type' => $sub['type'] ?? 'link',
                            'method' => $sub['method'] ?? 'GET',
                            'isActive' => isset($sub['url']) && $sub['url'] !== '#' && request()->url() === $sub['url']
                        ];
                    }
                @endphp
                <a href="javascript:void(0);" 
                   class="bottom-nav-item {{ (isset($menu['route']) && request()->routeIs($menu['route'] . '*')) ? 'active' : '' }}"
                   data-submenu-popup="true"
                   data-menu-label="{{ $menu['label'] }}"
                   data-submenu='@json($submenuWithUrls)'>
                    <i class="bx {{ $menu['icon'] }}"></i>
                    <span class="bottom-nav-label">{{ $menu['label'] }}</span>
                </a>
            @else
                <a href="{{ route($menu['route'], $menu['param']) }}" 
                   class="bottom-nav-item {{ request()->routeIs($menu['route'] . '*') ? 'active' : '' }}">
                    <i class="bx {{ $menu['icon'] }}"></i>
                    <span class="bottom-nav-label">{{ $menu['label'] }}</span>
                </a>
            @endif
        @endforeach
    </div>
</nav>

<div class="modal fade" id="subscriptionExpiredModal" tabindex="-1" aria-labelledby="subscriptionExpiredModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 0.5rem; overflow: hidden">
            <div class="modal-header bg-gradient-danger text-white p-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle bx-md me-3"></i>
                    <h5 class="modal-title mb-0" id="subscriptionExpiredModalLabel">
                        Subscription Expired
                    </h5>
                </div>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bx bx-time-five bx-lg text-danger mb-3"></i>
                    <h6 class="fw-semibold">
                        Your Dapur Subscription Has Expired
                    </h6>
                    <p class="text-muted mb-0">
                        To regain full access to all features, please contact
                        your Kepala Dapur to renew the subscription.
                    </p>
                </div>
                <div class="alert alert-info bg-light-info border-0 d-flex align-items-center justify-content-center p-3"
                    role="alert">
                    <i class="bx bx-info-circle me-2"></i>
                    <small>
                        Renew now to continue managing stock!
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 justify-content-center">
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                    Understood
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS untuk toggle sidebar */
    .layout-menu {
        transition:
            width 0.3s ease-in-out,
            transform 0.3s ease-in-out;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 260px;
        z-index: 1045;
        display: block !important;
        transform: translateX(0);
    }

    /* Menu container styling */
    .menu-container {
        height: calc(100vh - 80px);
        min-height: 500px;
        overflow-y: auto;
    }

    /* User profile section styling */
    .user-profile-section {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
        min-height: 60px;
    }

    /* Force avatar image to be square and cover the area */
    .avatar img {
        width: 40px !important;
        height: 40px !important;
        object-fit: cover;
    }

    /* Disabled menu items styling */
    .menu-item.disabled .menu-link {
        pointer-events: none;
        opacity: 0.6;
    }

    .menu-item.disabled:hover .menu-link {
        background: transparent !important;
    }

    /* State ketika sidebar collapsed */
    .layout-menu.collapsed {
        width: 78px;
    }

    /* Sembunyikan text saat collapsed */
    .layout-menu.collapsed .app-brand-text,
    .layout-menu.collapsed .menu-header-text,
    .layout-menu.collapsed .menu-link>div:not(.menu-icon) {
        display: none;
    }

    /* User profile collapsed state */
    .layout-menu.collapsed .user-profile-section .user-info,
    .layout-menu.collapsed .user-profile-section .user-chevron {
        display: none;
    }

    .layout-menu.collapsed .user-profile-section .nav-link {
        justify-content: center;
        padding: 0.5rem;
        background: rgba(255, 255, 255, 0.2) !important;
        border: none;
    }

    .layout-menu.collapsed .user-profile-section .avatar {
        margin: 0;
        transform: scale(1.1);
    }

    /* Hide alert when collapsed */
    .layout-menu.collapsed .alert {
        display: none;
    }

    /* Submenu styling */
    .menu-sub {
        display: none;
        padding-left: 1rem;
    }

    .menu-item.open .menu-sub {
        display: block;
    }

    .layout-menu.collapsed .menu-sub {
        display: none !important;
    }

    /* Mobile styles - Sidebar disembunyikan, bottom nav ditampilkan */
    @media (max-width: 991.98px) {
        .layout-menu {
            display: none !important;
        }

        .layout-page {
            padding-left: 0 !important;
            padding-bottom: 70px !important; /* Space untuk bottom nav */
        }

        /* Bottom Navigation Styles */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            padding: 8px 0;
            padding-bottom: max(8px, env(safe-area-inset-bottom));
        }

        .bottom-nav-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .bottom-nav-container::-webkit-scrollbar {
            display: none;
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 60px;
            padding: 8px 4px;
            text-decoration: none;
            color: #6c757d;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 8px;
            margin: 0 2px;
        }

        .bottom-nav-item i {
            font-size: 24px;
            margin-bottom: 4px;
            transition: all 0.3s ease;
        }

        .bottom-nav-label {
            font-size: 11px;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
            white-space: nowrap;
        }

        .bottom-nav-item.active {
            color: var(--bs-primary, #696cff);
        }

        .bottom-nav-item.active i {
            transform: scale(1.1);
        }

        .bottom-nav-item:active {
            background-color: rgba(105, 108, 255, 0.1);
            transform: scale(0.95);
        }

        .bottom-nav-badge {
            position: absolute;
            top: 4px;
            right: 8px;
            background: #dc3545;
            color: white;
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
            line-height: 14px;
        }

        /* Safe area untuk iPhone dengan notch */
        @@supports (padding: max(0px)) {
            .mobile-bottom-nav {
                padding-bottom: max(8px, env(safe-area-inset-bottom));
            }
        }

        /* Submenu Popup Drawer */
        .submenu-popup {
            position: fixed;
            bottom: -100%;
            left: 0;
            right: 0;
            width: 100%;
            background: #fff;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1060;
            max-height: 70vh;
            overflow-y: auto;
            transition: bottom 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding-bottom: max(20px, env(safe-area-inset-bottom));
        }

        .submenu-popup.show {
            bottom: 70px;
        }

        .submenu-popup-header {
            padding: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
            border-radius: 20px 20px 0 0;
        }

        .submenu-popup-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .submenu-popup-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .submenu-popup-close:hover {
            background: rgba(0, 0, 0, 0.05);
            color: #333;
        }

        .submenu-popup-body {
            padding: 10px 0;
        }

        .submenu-item {
            display: block;
            padding: 16px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            position: relative;
        }

        .submenu-item:last-child {
            border-bottom: none;
        }

        .submenu-item:hover,
        .submenu-item:active {
            background: rgba(105, 108, 255, 0.05);
            color: var(--bs-primary, #696cff);
        }

        .submenu-item.active {
            background: rgba(105, 108, 255, 0.1);
            color: var(--bs-primary, #696cff);
            font-weight: 500;
        }

        .submenu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--bs-primary, #696cff);
        }

        .submenu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1059;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .submenu-overlay.show {
            opacity: 1;
            visibility: visible;
        }
    }

    /* Desktop: Always visible */
    @media (min-width: 992px) {
        .layout-menu {
            transform: translateX(0) !important;
            display: block !important;
        }

        .layout-page {
            padding-bottom: 0 !important;
        }

        /* Hide bottom nav di desktop */
        .mobile-bottom-nav {
            display: none !important;
        }
    }

    /* Adjust main content */
    .layout-page {
        padding-left: 260px;
        transition: padding-left 0.3s ease-in-out;
    }

    .layout-page.sidebar-collapsed {
        padding-left: 78px;
    }

    @media (max-width: 991.98px) {

        .layout-page,
        .layout-page.sidebar-collapsed {
            padding-left: 0 !important;
        }
    }

    /* Modal styling */
    .modal-content {
        border-radius: 0.5rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }

    .modal-header.bg-gradient-danger {
        background: linear-gradient(45deg, #dc3545, #e4606d);
        border-bottom: none;
    }

    /* Enhanced animations */
    .layout-menu .menu-link {
        transition: all 0.2s ease-in-out;
    }

    .layout-menu:not(.collapsed) .menu-link:hover {
        transform: translateX(4px);
        background: rgba(255, 255, 255, 0.1);
    }

    .menu-icon {
        transition: transform 0.2s ease, color 0.2s ease;
    }

    .menu-sub {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
        opacity: 0;
    }

    .menu-item.open .menu-sub {
        max-height: 500px;
        opacity: 1;
    }

    .user-profile-section .nav-link {
        transition: all 0.3s ease;
    }

    .user-profile-section .nav-link:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .subscription-expired .menu-link {
        background: rgba(220, 53, 69, 0.1) !important;
        border-left: 3px solid #dc3545;
    }

    .subscription-expiring .menu-link {
        background: rgba(255, 193, 7, 0.1) !important;
        border-left: 3px solid #ffc107;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('layout-menu');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const layoutPage = document.querySelector('.layout-page') || document.body;

        // Initialize tooltips for disabled menu items
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Desktop toggle functionality
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                layoutPage.classList.toggle('sidebar-collapsed');

                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);

                if (isCollapsed) {
                    document.querySelectorAll('.menu-item.open').forEach(function(item) {
                        item.classList.remove('open');
                    });
                }
            });
        }

        // Mobile bottom navigation - tidak perlu toggle karena selalu visible

        // Restore sidebar state - Start expanded by default on desktop
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('collapsed');
            layoutPage.classList.remove('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'false');
        }

        // Handle submenu toggles
        document.querySelectorAll('.menu-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (sidebar.classList.contains('collapsed') && window.innerWidth >= 992) {
                    return;
                }

                const menuItem = this.closest('.menu-item');

                if (menuItem.classList.contains('disabled')) {
                    return;
                }

                const isCurrentlyOpen = menuItem.classList.contains('open');
                const parent = menuItem.parentElement;

                parent.querySelectorAll('.menu-item.open').forEach(function(openItem) {
                    if (openItem !== menuItem) {
                        openItem.classList.remove('open');
                    }
                });

                if (isCurrentlyOpen) {
                    menuItem.classList.remove('open');
                } else {
                    menuItem.classList.add('open');
                }
            });
        });

        // Handle disabled menu item clicks
        document.querySelectorAll('.menu-item.disabled .menu-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const subscriptionModal = new bootstrap.Modal(
                    document.getElementById('subscriptionExpiredModal'), {
                        backdrop: 'static',
                        keyboard: false,
                    }
                );
                subscriptionModal.show();
            });
        });

        // Auto-show modal if completely expired
        const subscriptionStatus = '{{ $subscriptionStatus ?? '' }}';
        const isSubscriptionActive = {{ $isSubscriptionActive ? 'true' : 'false' }};

        if (!isSubscriptionActive && subscriptionStatus === 'expired') {
            setTimeout(function() {
                const subscriptionModal = new bootstrap.Modal(
                    document.getElementById('subscriptionExpiredModal'), {
                        backdrop: 'static',
                        keyboard: false,
                    }
                );
                subscriptionModal.show();
            }, 3000);
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                // Desktop mode - restore sidebar
                sidebar.style.display = 'block';
            } else {
                // Mobile mode - hide sidebar, show bottom nav
                sidebar.style.display = 'none';
            }
        });

        // Handle submenu popup
        document.querySelectorAll('[data-submenu-popup="true"]').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const submenuData = JSON.parse(this.getAttribute('data-submenu'));
                const menuLabel = this.getAttribute('data-menu-label');
                showSubmenuPopup(menuLabel, submenuData);
            });
        });

        // Function to show submenu popup
        function showSubmenuPopup(title, submenuItems) {
            // Remove existing popup if any
            const existingPopup = document.getElementById('submenuPopup');
            if (existingPopup) {
                existingPopup.remove();
            }

            // Create overlay
            const overlay = document.createElement('div');
            overlay.className = 'submenu-overlay';
            overlay.id = 'submenuOverlay';
            overlay.addEventListener('click', function() {
                closeSubmenuPopup();
            });

            // Create popup
            const popup = document.createElement('div');
            popup.className = 'submenu-popup';
            popup.id = 'submenuPopup';

            // Create header
            const header = document.createElement('div');
            header.className = 'submenu-popup-header';
            header.innerHTML = `
                <h5>${title}</h5>
                <button class="submenu-popup-close" onclick="closeSubmenuPopup()">
                    <i class="bx bx-x"></i>
                </button>
            `;

            // Create body
            const body = document.createElement('div');
            body.className = 'submenu-popup-body';
            
            submenuItems.forEach(function(submenu) {
                const submenuItem = document.createElement(submenu.type === 'logout' ? 'button' : 'a');
                submenuItem.className = 'submenu-item';
                
                if (submenu.type === 'logout') {
                    submenuItem.type = 'button';
                    submenuItem.textContent = submenu.label;
                    submenuItem.addEventListener('click', function(e) {
                        e.preventDefault();
                        closeSubmenuPopup();
                        
                        // Create and submit logout form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = submenu.url;
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        form.appendChild(csrfToken);
                        
                        document.body.appendChild(form);
                        form.submit();
                    });
                } else {
                    submenuItem.href = submenu.url;
                    submenuItem.textContent = submenu.label;
                    
                    if (submenu.isActive) {
                        submenuItem.classList.add('active');
                    }

                    submenuItem.addEventListener('click', function(e) {
                        if (submenu.url === '#') {
                            e.preventDefault();
                            closeSubmenuPopup();
                            return;
                        }
                        closeSubmenuPopup();
                        // Navigation will happen via href
                    });
                }

                body.appendChild(submenuItem);
            });

            popup.appendChild(header);
            popup.appendChild(body);
            document.body.appendChild(overlay);
            document.body.appendChild(popup);

            // Trigger animation
            setTimeout(function() {
                overlay.classList.add('show');
                popup.classList.add('show');
            }, 10);
        }

        // Function to close submenu popup
        function closeSubmenuPopup() {
            const popup = document.getElementById('submenuPopup');
            const overlay = document.getElementById('submenuOverlay');
            
            if (popup) {
                popup.classList.remove('show');
                if (overlay) overlay.classList.remove('show');
                
                setTimeout(function() {
                    if (popup) popup.remove();
                    if (overlay) overlay.remove();
                }, 300);
            }
        }

        // Make function globally available
        window.closeSubmenuPopup = closeSubmenuPopup;

        // Initialize subscription features
        initializeSubscriptionFeatures();
    });

    function initializeSubscriptionFeatures() {
        const isSubscriptionActive = {{ $isSubscriptionActive ? 'true' : 'false' }};
        const subscriptionStatus = '{{ $subscriptionStatus ?? '' }}';

        if (subscriptionStatus === 'expiring_soon') {
            const userProfile = document.querySelector('.user-profile-section .nav-link');
            if (userProfile) {
                userProfile.style.borderLeft = '3px solid #ffc107';
            }
        } else if (subscriptionStatus === 'expired') {
            const userProfile = document.querySelector('.user-profile-section .nav-link');
            if (userProfile) {
                userProfile.style.borderLeft = '3px solid #dc3545';
            }
        }
    }

    function showSubscriptionNotification() {
        const subscriptionModal = new bootstrap.Modal(
            document.getElementById('subscriptionExpiredModal'), {
                backdrop: 'static',
                keyboard: false,
            }
        );
        subscriptionModal.show();
    }

    window.showSubscriptionNotification = showSubscriptionNotification;

    // Subscription expiration warning
    document.addEventListener('DOMContentLoaded', function() {
        const subscriptionStatus = '{{ $subscriptionStatus ?? '' }}';
        const daysLeft = {{ session('subscription_days_left', 0) }};

        if (subscriptionStatus === 'expiring_soon' && daysLeft <= 5) {
            setTimeout(function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Subscription Expiring Soon!',
                        html: `
                            <div class="text-center">
                                <i class="bx bx-time bx-lg text-warning mb-3"></i>
                                <p>The dapur subscription expires in <strong>${daysLeft}</strong> day${daysLeft !== 1 ? 's' : ''}.</p>
                                <p class="text-muted">Please inform the Kepala Dapur to renew before expiration.</p>
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffc107',
                    });
                }
            }, 3000);
        }
    });
</script>
