<aside
    id="layout-menu"
    class="layout-menu menu-vertical menu bg-menu-theme d-none d-lg-block"
>
    <!-- Brand + Toggle -->
    <div
        class="app-brand demo d-flex align-items-center justify-content-between px-3 py-2"
    >
        <!-- Logo -->
        <a
            href="/"
            class="app-brand-link d-flex align-items-center text-decoration-none"
        >
            <span class="app-brand-logo demo">
                <img
                    src="{{ asset("logo.png") }}"
                    alt="Logo"
                    style="height: 45px; width: auto"
                />
            </span>
            <span class="app-brand-text demo fw-bolder ms-4 fs-3">
                Ahli Gizi
            </span>
        </a>

        <!-- Desktop Toggle Button -->
        <button
            class="btn btn-outline-secondary d-none d-lg-inline-flex layout-menu-toggle"
            id="sidebarToggle"
            style="
                border: none;
                background: transparent;
                color: inherit;
                width: 32px;
                height: 32px;
                display: flex !important;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease-in-out;
            "
        >
            <i class="bx bx-chevron-left bx-sm"></i>
        </button>
    </div>

    <!-- Menu Container with flex layout -->
    <div class="menu-container d-flex flex-column h-100">
        <!-- User Profile Section - Moved to Top of Menu -->
        <div class="user-profile-section mt-3 px-3 pb-3">
            <div class="nav-item navbar-dropdown dropdown-user dropdown">
                <a
                    class="nav-link dropdown-toggle hide-arrow d-flex align-items-center w-100 p-2 rounded"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    style="
                        background: rgba(255, 255, 255, 0.15);
                        transition: all 0.3s ease;
                        border: 1px solid rgba(255, 255, 255, 0.2);
                    "
                    onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.15)'"
                >
                    <div class="avatar avatar-online me-3">
                        <img
                            src="{{ asset("admin/assets/img/avatars/1.png") }}"
                            alt
                            class="w-px-40 h-auto rounded-circle"
                        />
                    </div>
                    <div class="flex-grow-1 text-start user-info">
                        <div class="fw-semibold text-black">
                            {{ auth()->user()->nama ?? "Unknown" }}
                        </div>
                        <small class="text-muted">
                            {{ ucfirst(str_replace("_", " ", session("role_type") ?? "Unknown")) }}
                        </small>
                        @if (session("subscription_status") && session("subscription_status") !== "active")
                            <small class="text-warning d-block">
                                <i class="bx bx-warning-alt bx-xs"></i>
                                @if (session("subscription_status") === "expired")
                                    Subscription Expired
                                @elseif (session("subscription_status") === "expiring_soon")
                                    Expires in
                                    {{ session("subscription_days_left", 0) }}
                                    days
                                @else
                                    {{ ucfirst(str_replace("_", " ", session("subscription_status"))) }}
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
                                    <div class="avatar avatar-online">
                                        <img
                                            src="{{ asset("admin/assets/img/avatars/1.png") }}"
                                            alt
                                            class="w-px-40 h-auto rounded-circle"
                                        />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">
                                        {{ auth()->user()->nama ?? "Unknown" }}
                                    </span>
                                    <small class="text-muted">
                                        {{ ucfirst(str_replace("_", " ", session("role_type") ?? "Unknown")) }}
                                    </small>
                                    @if (session("subscription_end"))
                                        <small class="text-info d-block">
                                            Dapur:
                                            {{ session("dapur_name") ?? "Tidak Tersedia" }}
                                        </small>
                                        <small class="text-muted d-block">
                                            Expires:
                                            {{ \Carbon\Carbon::parse(session("subscription_end"))->format("d M Y") }}
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
                        <form action="{{ route("logout") }}" method="POST">
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

        @php
            $isSubscriptionActive = session("is_subscription_active", false);
            $subscriptionStatus = session("subscription_status");
            $idDapur = session("id_dapur");
        @endphp

        <!-- Subscription Status Alert -->
        @if (! $isSubscriptionActive && $subscriptionStatus)
            <div class="px-3 mb-3">
                <div
                    class="alert alert-warning alert-dismissible fade show py-2 px-3"
                    role="alert"
                    style="font-size: 0.875rem; line-height: 1.2"
                >
                    <div class="d-flex align-items-start">
                        <i class="bx bx-info-circle me-2 mt-1"></i>
                        <div>
                            @if ($subscriptionStatus === "expired")
                                <strong>Subscription Expired!</strong>
                                <br />
                                <small>Contact Kepala Dapur to renew</small>
                            @elseif ($subscriptionStatus === "expiring_soon")
                                <strong>Subscription Expiring!</strong>
                                <br />
                                <small>
                                    {{ session("subscription_days_left", 0) }}
                                    days remaining
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

        <!-- Menu Utama -->
        <ul class="menu-inner py-1 flex-grow-1">
            <!-- Dashboard - Always accessible -->
            <li
                class="menu-item {{ request()->routeIs("dashboard") ? "active" : "" }}"
            >
                <a
                    href="{{ route("dashboard", request()->current_dapur->id_dapur ?? (auth()->user()->userRole->id_dapur ?? null)) }}"
                    class="menu-link"
                >
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>

            @if ($isSubscriptionActive)
                <!-- Ahli Gizi Header - Only show when subscription active -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Ahli Gizi</span>
                </li>

                <!-- Menu Makanan -->
                <li
                    class="menu-item {{ request()->routeIs("ahli-gizi.menu-makanan.*") ? "active open" : "" }}"
                >
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-food-menu"></i>
                        <div data-i18n="Menu Makanan">Menu Makanan</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item {{ request()->routeIs("ahli-gizi.menu-makanan.index") ? "active" : "" }}"
                        >
                            <a
                                href="{{ route("ahli-gizi.menu-makanan.index", ["dapur" => request()->current_dapur->id_dapur ?? (auth()->user()->userRole->id_dapur ?? null)]) }}"
                                class="menu-link"
                            >
                                <div data-i18n="Daftar Menu Makanan">
                                    Daftar Menu Makanan
                                </div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ request()->routeIs("ahli-gizi.menu-makanan.create") ? "active" : "" }}"
                        >
                            <a
                                href="{{ route("ahli-gizi.menu-makanan.create", ["dapur" => request()->current_dapur->id_dapur ?? (auth()->user()->userRole->id_dapur ?? null)]) }}"
                                class="menu-link"
                            >
                                <div data-i18n="Tambah Menu Makanan">
                                    Tambah Menu Makanan
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Transaksi Dapur -->
                <li
                    class="menu-item {{ request()->routeIs("ahli-gizi.transaksi.*") ? "active open" : "" }}"
                >
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-cart"></i>
                        <div data-i18n="Transaksi Dapur">Transaksi Dapur</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item {{ request()->routeIs("ahli-gizi.transaksi.index") ? "active" : "" }}"
                        >
                            <a
                                href="{{ route("ahli-gizi.transaksi.index", ["dapur" => request()->current_dapur->id_dapur ?? (auth()->user()->userRole->id_dapur ?? null)]) }}"
                                class="menu-link"
                            >
                                <div data-i18n="Daftar Transaksi">
                                    Daftar Transaksi
                                </div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ request()->routeIs("ahli-gizi.transaksi.create") ? "active" : "" }}"
                        >
                            <a
                                href="{{ route("ahli-gizi.transaksi.create", ["dapur" => request()->current_dapur->id_dapur ?? (auth()->user()->userRole->id_dapur ?? null)]) }}"
                                class="menu-link"
                            >
                                <div data-i18n="Buat Transaksi">
                                    Buat Transaksi
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                <!-- Limited Access Message -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text text-warning">
                        Limited Access
                    </span>
                </li>

                <!-- Disabled menu items with tooltips -->
                @php
                    $disabledMenus = [
                        "Menu Makanan" => "bx-food-menu",
                        "Transaksi Dapur" => "bx-cart",
                    ];
                @endphp

                @foreach ($disabledMenus as $menuName => $icon)
                    <li
                        class="menu-item disabled"
                        data-bs-toggle="tooltip"
                        data-bs-placement="right"
                        title="Subscription required to access this feature"
                    >
                        <a
                            href="javascript:void(0);"
                            class="menu-link text-muted"
                            style="cursor: not-allowed; opacity: 0.6"
                        >
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

<!-- Mobile Menu Toggle Button (tampil hanya di mobile) -->
<button
    class="btn btn-primary d-lg-none position-fixed"
    id="mobileMenuToggle"
    style="
        top: 10px;
        left: 10px;
        z-index: 1050;
        border-radius: 4px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    "
>
    <i class="bx bx-chevron-right bx-sm align-middle"></i>
</button>

<!-- Overlay untuk mobile -->
<div
    class="layout-overlay d-lg-none"
    id="layoutOverlay"
    style="display: none"
></div>

<!-- Subscription Expired Modal -->
<div
    class="modal fade"
    id="subscriptionExpiredModal"
    tabindex="-1"
    aria-labelledby="subscriptionExpiredModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div
            class="modal-content border-0 shadow-lg"
            style="border-radius: 0.5rem; overflow: hidden"
        >
            <div class="modal-header bg-gradient-danger text-white p-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle bx-md me-3"></i>
                    <h5
                        class="modal-title mb-0"
                        id="subscriptionExpiredModalLabel"
                    >
                        Subscription Expired
                    </h5>
                </div>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i
                        class="bx bx-time-five bx-lg text-danger mb-3 animate__animated animate__pulse animate__infinite"
                    ></i>
                    <h6 class="fw-semibold">
                        Your Dapur Subscription Has Expired
                    </h6>
                    <p class="text-muted mb-0">
                        To regain full access to all features, please contact
                        your Kepala Dapur to renew the subscription.
                    </p>
                </div>
                <div
                    class="alert alert-info bg-light-info border-0 d-flex align-items-center justify-content-center p-3"
                    role="alert"
                >
                    <i class="bx bx-info-circle me-2"></i>
                    <small>
                        Renew now to continue managing menus and transactions!
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 justify-content-center">
                <button
                    type="button"
                    class="btn btn-primary px-4"
                    data-bs-dismiss="modal"
                >
                    Understood
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS untuk toggle sidebar - IDENTICAL TO ADMIN GUDANG */
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
    }

    /* Menu container styling */
    .menu-container {
        height: calc(100vh - 80px); /* Adjust based on brand height */
        min-height: 500px; /* Ensure minimum height */
        overflow-y: auto; /* Allow scrolling if content is too long */
    }

    /* User profile section styling */
    .user-profile-section {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        flex-shrink: 0; /* Prevent shrinking */
        min-height: 60px; /* Reduced height for better positioning */
    }

    /* Disabled menu items styling */
    .menu-item.disabled .menu-link {
        pointer-events: none;
        opacity: 0.6;
    }

    .menu-item.disabled:hover .menu-link {
        background: transparent !important;
    }

    /* Subscription warning styling */
    .text-warning {
        color: #ffc107 !important;
    }

    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }

    /* Tooltip styling for disabled items */
    .tooltip {
        font-size: 0.875rem;
    }

    /* Alert styling */
    .alert {
        border: 1px solid transparent;
        border-radius: 0.375rem;
    }

    .alert-warning {
        background-color: rgba(255, 193, 7, 0.1);
        border-color: rgba(255, 193, 7, 0.3);
        color: #856404;
    }

    /* State ketika sidebar collapsed */
    .layout-menu.collapsed {
        width: 78px;
    }

    /* Sembunyikan text saat collapsed */
    .layout-menu.collapsed .app-brand-text,
    .layout-menu.collapsed .menu-header-text,
    .layout-menu.collapsed .menu-link > div:not(.menu-icon) {
        display: none;
    }

    /* User profile collapsed state - hanya tampilkan avatar */
    .layout-menu.collapsed .user-profile-section .user-info,
    .layout-menu.collapsed .user-profile-section .user-chevron {
        display: none;
    }

    /* Show only avatar when collapsed */
    .layout-menu.collapsed .user-profile-section .nav-link {
        justify-content: center;
        padding: 0.5rem;
        background: rgba(255, 255, 255, 0.2) !important;
        border: none;
    }

    .layout-menu.collapsed .user-profile-section .avatar {
        margin: 0;
        transform: scale(1.1); /* Slightly larger avatar for visibility */
    }

    /* Hide subscription alert when collapsed */
    .layout-menu.collapsed .alert {
        display: none;
    }

    /* Pastikan user profile section tetap terlihat */
    .layout-menu .user-profile-section {
        display: block !important;
        position: relative;
        z-index: 1;
        order: -1; /* Move to top of menu-container */
    }

    /* Posisikan tombol toggle di samping logo saat collapsed */
    .layout-menu.collapsed .layout-menu-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        z-index: 1050;
        background: var(--bs-primary);
        color: white;
        border-radius: 4px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease-in-out;
        font-size: 14px;
    }

    .layout-menu.collapsed .layout-menu-toggle:hover {
        background: var(--bs-primary-dark);
    }

    /* Rotate icon saat collapsed */
    .layout-menu.collapsed .layout-menu-toggle i {
        transform: rotate(180deg);
    }

    /* Sembunyikan submenu saat collapsed */
    .layout-menu.collapsed .menu-sub {
        display: none !important;
    }

    /* Submenu styling */
    .menu-sub {
        display: none;
        padding-left: 1rem;
    }

    .menu-item.open .menu-sub {
        display: block;
    }

    /* Mobile styles */
    @media (max-width: 991.98px) {
        .layout-menu {
            transform: translateX(-100%);
            width: 260px !important;
            transition: transform 0.3s ease-in-out;
        }

        .layout-menu.show {
            transform: translateX(0);
        }

        /* Reset collapsed state di mobile */
        .layout-menu.collapsed {
            width: 260px !important;
            transform: translateX(-100%);
        }

        .layout-menu.collapsed.show {
            transform: translateX(0);
        }

        /* Tampilkan kembali semua elemen di mobile */
        .layout-menu.collapsed .app-brand-text,
        .layout-menu.collapsed .menu-header-text,
        .layout-menu.collapsed .menu-link > div:not(.menu-icon) {
            display: block;
        }

        /* Show alert in mobile even when collapsed */
        .layout-menu.collapsed .alert {
            display: block;
        }

        /* Mobile user profile styling */
        .layout-menu.collapsed .user-profile-section .user-info,
        .layout-menu.collapsed .user-profile-section .user-chevron {
            display: block;
        }

        .layout-menu.collapsed .user-profile-section .nav-link {
            justify-content: flex-start;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .layout-menu.collapsed .user-profile-section .avatar {
            margin-right: 0.75rem;
            transform: none;
        }

        .layout-menu.collapsed .app-brand {
            justify-content: space-between;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Reset tombol toggle di mobile */
        .layout-menu.collapsed .layout-menu-toggle {
            position: static;
            background: transparent;
            color: inherit;
            border-radius: 0;
            width: auto;
            height: auto;
            display: flex;
            transform: none;
            font-size: inherit;
            right: auto;
            top: auto;
        }

        .layout-menu.collapsed .layout-menu-toggle i {
            transform: none;
        }

        .layout-menu.collapsed .app-brand {
            justify-content: space-between;
            position: static;
        }

        .layout-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        /* Style tombol mobile agar mirip desktop */
        #mobileMenuToggle {
            background: var(--bs-primary);
            color: white;
            transition: all 0.3s ease-in-out;
        }

        #mobileMenuToggle:hover {
            background: var(--bs-primary-dark);
        }

        /* Rotate ikon saat sidebar terbuka di mobile */
        .layout-menu.show + #mobileMenuToggle i {
            transform: rotate(180deg);
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
        .layout-page {
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

    .modal-footer {
        border-top: none;
    }

    .modal-body {
        padding: 1.5rem;
    }

    /* Additional Sneat-inspired modal styling */
    .modal-content {
        transform: scale(0.95);
        transition:
            transform 0.3s ease-in-out,
            opacity 0.3s ease-in-out;
        opacity: 0;
    }

    .modal.fade.show .modal-content {
        transform: scale(1);
        opacity: 1;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        transition: transform 0.2s ease;
    }

    .modal-header .btn-close:hover {
        transform: scale(1.2);
    }

    .modal-body .alert.bg-light-info {
        background-color: rgba(0, 123, 255, 0.1) !important;
        border-radius: 0.375rem;
        color: #0057b8;
    }

    .modal-body .bx-time-five {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .modal-footer .btn-primary {
        background: #696cff;
        border-color: #696cff;
        transition: all 0.2s ease-in-out;
    }

    .modal-footer .btn-primary:hover {
        background: #5f61e6;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('layout-menu');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const layoutOverlay = document.getElementById('layoutOverlay');
        const layoutPage =
            document.querySelector('.layout-page') || document.body;

        // Initialize tooltips for disabled menu items
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]'),
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Desktop toggle functionality
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                layoutPage.classList.toggle('sidebar-collapsed');

                // Simpan state ke localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);

                // Tutup semua submenu saat sidebar collapsed
                if (isCollapsed) {
                    document
                        .querySelectorAll('.menu-item.open')
                        .forEach(function (item) {
                            item.classList.remove('open');
                        });
                }
            });
        }

        // Enhanced hover functionality for desktop with animation
        if (sidebar) {
            let hoverTimeout;
            let isUserCollapsed = false; // Track if user manually collapsed

            sidebar.addEventListener('mouseenter', function () {
                if (window.innerWidth >= 992) {
                    // Only active on desktop
                    clearTimeout(hoverTimeout);

                    // Check if user manually collapsed
                    const savedState = localStorage.getItem('sidebarCollapsed');
                    isUserCollapsed = savedState === 'true';

                    // Always expand on hover
                    sidebar.classList.remove('collapsed');
                    layoutPage.classList.remove('sidebar-collapsed');
                }
            });

            sidebar.addEventListener('mouseleave', function () {
                if (window.innerWidth >= 992) {
                    // Only active on desktop
                    // Add delay before collapsing
                    hoverTimeout = setTimeout(function () {
                        // Only collapse if user had it collapsed or if it was auto-collapsed
                        const savedState =
                            localStorage.getItem('sidebarCollapsed');
                        if (savedState === 'true' || isUserCollapsed) {
                            sidebar.classList.add('collapsed');
                            layoutPage.classList.add('sidebar-collapsed');
                        }
                    }, 300); // 300ms delay before collapsing
                }
            });
        }

        // Mobile toggle functionality
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function () {
                sidebar.classList.remove('d-none');
                sidebar.classList.toggle('show');
                layoutOverlay.style.display = sidebar.classList.contains('show')
                    ? 'block'
                    : 'none';
            });
        }

        // Close mobile menu when clicking overlay
        if (layoutOverlay) {
            layoutOverlay.addEventListener('click', function () {
                sidebar.classList.remove('show');
                layoutOverlay.style.display = 'none';
            });
        }

        // Restore sidebar state from localStorage - Start collapsed by default
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (window.innerWidth >= 992) {
            // Default to collapsed state on desktop for hover animation
            sidebar.classList.add('collapsed');
            layoutPage.classList.add('sidebar-collapsed');

            // If user previously had it expanded, keep it collapsed but remember the preference
            if (savedState !== 'false') {
                localStorage.setItem('sidebarCollapsed', 'true');
            }
        }

        // Handle submenu toggles - only allow when subscription is active
        document.querySelectorAll('.menu-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Don't open submenu if sidebar is collapsed (except during hover)
                if (
                    sidebar.classList.contains('collapsed') &&
                    window.innerWidth >= 992
                ) {
                    // During hover, sidebar will be expanded, so allow submenu toggle
                    const isHovering = sidebar.matches(':hover');
                    if (!isHovering) {
                        return;
                    }
                }

                const menuItem = this.closest('.menu-item');

                // Check if this is a disabled menu item
                if (menuItem.classList.contains('disabled')) {
                    return;
                }

                const isCurrentlyOpen = menuItem.classList.contains('open');

                // Close all other submenus at the same level
                const parent = menuItem.parentElement;
                parent
                    .querySelectorAll('.menu-item.open')
                    .forEach(function (openItem) {
                        if (openItem !== menuItem) {
                            openItem.classList.remove('open');
                        }
                    });

                // Toggle current submenu
                if (isCurrentlyOpen) {
                    menuItem.classList.remove('open');
                } else {
                    menuItem.classList.add('open');
                }
            });
        });

        // Handle disabled menu item clicks - show notification about contacting Kepala Dapur
        document
            .querySelectorAll('.menu-item.disabled .menu-link')
            .forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Show a modal notification about contacting Kepala Dapur
                    const subscriptionModal = new bootstrap.Modal(
                        document.getElementById('subscriptionExpiredModal'),
                        {
                            backdrop: 'static',
                            keyboard: false,
                        },
                    );
                    subscriptionModal.show();
                });
            });

        // Auto-show modal if completely expired
        const subscriptionStatus = '{{ $subscriptionStatus ?? "" }}';
        const isSubscriptionActive =
            {{ $isSubscriptionActive ? "true" : "false" }};

        if (!isSubscriptionActive && subscriptionStatus === 'expired') {
            setTimeout(function () {
                const subscriptionModal = new bootstrap.Modal(
                    document.getElementById('subscriptionExpiredModal'),
                    {
                        backdrop: 'static',
                        keyboard: false,
                    },
                );
                subscriptionModal.show();
            }, 3000); // Show after 3 seconds
        }

        // Handle window resize
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                // Desktop mode
                sidebar.classList.remove('show');
                layoutOverlay.style.display = 'none';

                // Apply hover-based collapsed state for desktop
                sidebar.classList.add('collapsed');
                layoutPage.classList.add('sidebar-collapsed');
            } else {
                // Mobile mode
                sidebar.classList.remove('collapsed');
                layoutPage.classList.remove('sidebar-collapsed');
            }
        });

        // Initialize any additional subscription-related functionality
        initializeSubscriptionFeatures();
    });

    // Function to handle subscription-related features for Ahli Gizi
    function initializeSubscriptionFeatures() {
        const isSubscriptionActive =
            {{ $isSubscriptionActive ? "true" : "false" }};
        const subscriptionStatus = '{{ $subscriptionStatus ?? "" }}';

        // Add warning styles to user profile when subscription issues exist
        if (subscriptionStatus === 'expiring_soon') {
            const userProfile = document.querySelector(
                '.user-profile-section .nav-link',
            );
            if (userProfile) {
                userProfile.style.borderLeft = '3px solid #ffc107';
            }
        } else if (subscriptionStatus === 'expired') {
            const userProfile = document.querySelector(
                '.user-profile-section .nav-link',
            );
            if (userProfile) {
                userProfile.style.borderLeft = '3px solid #dc3545';
            }
        }
    }

    // Add CSS keyframes for animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse-warning {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .subscription-expired .menu-link {
            background: rgba(220, 53, 69, 0.1) !important;
            border-left: 3px solid #dc3545;
        }

        .subscription-expiring .menu-link {
            background: rgba(255, 193, 7, 0.1) !important;
            border-left: 3px solid #ffc107;
        }

        /* Enhanced hover animations */
        .layout-menu {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .layout-menu .menu-link {
            transition: all 0.2s ease-in-out;
        }

        .layout-menu:not(.collapsed) .menu-link:hover {
            transform: translateX(4px);
            background: rgba(255, 255, 255, 0.1);
        }

        /* Smooth text reveal animation */
        .layout-menu.collapsed .app-brand-text,
        .layout-menu.collapsed .menu-header-text,
        .layout-menu.collapsed .menu-link > div:not(.menu-icon) {
            opacity: 0;
            transform: translateX(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .layout-menu:not(.collapsed) .app-brand-text,
        .layout-menu:not(.collapsed) .menu-header-text,
        .layout-menu:not(.collapsed) .menu-link > div:not(.menu-icon) {
            opacity: 1;
            transform: translateX(0);
            transition: opacity 0.3s ease 0.1s, transform 0.3s ease 0.1s;
        }

        /* Icon animations */
        .menu-icon {
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .layout-menu:hover .menu-icon {
            transform: scale(1.05);
        }

        /* Submenu slide animation */
        .menu-sub {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
            opacity: 0;
        }

        .menu-item.open .menu-sub {
            max-height: 500px;
            opacity: 1;
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }

        /* Hover effects for disabled items */
        .menu-item.disabled:hover {
            transform: translateX(2px);
            transition: transform 0.2s ease;
        }

        /* Badge animations */
        .badge {
            animation: badge-pulse 2s infinite;
        }

        @keyframes badge-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* User profile hover enhancement */
        .user-profile-section .nav-link {
            transition: all 0.3s ease;
        }

        .user-profile-section .nav-link:hover {
            background: rgba(255, 255, 255, 0.25) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Loading animation for subscription checks */
        .subscription-loading {
            position: relative;
            overflow: hidden;
        }

        .subscription-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: loading-sweep 1.5s infinite;
        }

        @keyframes loading-sweep {
            0% { left: -100%; }
            100% { left: 100%; }
        }
    `;
    document.head.appendChild(style);

    // Additional utility functions for Ahli Gizi
    function showSubscriptionNotification() {
        const subscriptionModal = new bootstrap.Modal(
            document.getElementById('subscriptionExpiredModal'),
            {
                backdrop: 'static',
                keyboard: false,
            },
        );
        subscriptionModal.show();
    }

    // Export functions for external use
    window.showSubscriptionNotification = showSubscriptionNotification;

    // Initialize subscription status monitoring for Ahli Gizi
    document.addEventListener('DOMContentLoaded', function () {
        const subscriptionStatus = '{{ $subscriptionStatus ?? "" }}';
        const daysLeft = {{ session("subscription_days_left", 0) }};

        // Show expiration warning for Ahli Gizi users
        if (subscriptionStatus === 'expiring_soon' && daysLeft <= 5) {
            setTimeout(function () {
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
            }, 3000); // Show after 3 seconds
        }
    });
</script>
