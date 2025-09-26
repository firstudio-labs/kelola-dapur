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
                    src="{{ asset("logo_kelola_dapur_black.png") }}"
                    alt="Logo"
                    style="height: 45px; width: auto"
                />
            </span>
            {{-- <span class="demo fw-bolder ms-4 fs-4">Super Admin</span> --}}
        </a>
    </div>

    <!-- Menu Utama -->
    <ul class="menu-inner py-1">
        <!-- User dropdown -->
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
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    {{--
                        <li>
                        <a
                        class="dropdown-item"
                        href="{{ route("kepala-dapur.edit-profil") }}"
                        >
                        <i class="bx bx-edit me-2"></i>
                        <span class="align-middle">Edit Profil</span>
                        </a>
                        </li>
                    --}}
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
        <!-- Dashboard -->
        <li
            class="menu-item {{ request()->routeIs("dashboard") ? "active" : "" }}"
        >
            <a href="{{ route("dashboard") }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- Admin -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Admin</span>
        </li>

        <!-- Dapur -->
        <li
            class="menu-item {{ request()->routeIs("superadmin.dapur.*") ? "active open" : "" }}"
        >
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-buildings"></i>
                <div>Dapur</div>
            </a>
            <ul class="menu-sub">
                <li
                    class="menu-item {{ request()->routeIs("superadmin.dapur.index") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.dapur.index") }}"
                        class="menu-link"
                    >
                        <div>Daftar Dapur</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Menu Makanan -->
        <li
            class="menu-item {{ request()->routeIs("superadmin.menu-makanan.*") ? "active open" : "" }}"
        >
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div>Menu Makanan</div>
            </a>
            <ul class="menu-sub">
                <li
                    class="menu-item {{ request()->routeIs("superadmin.menu-makanan.index") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.menu-makanan.index") }}"
                        class="menu-link"
                    >
                        <div>Daftar Menu Makanan</div>
                    </a>
                </li>
                <li
                    class="menu-item {{ request()->routeIs("superadmin.menu-makanan.create") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.menu-makanan.create") }}"
                        class="menu-link"
                    >
                        <div>Tambah Menu Makanan</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Template Bahan -->
        <li
            class="menu-item {{ request()->routeIs("superadmin.template-items.*") ? "active open" : "" }}"
        >
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>Template Bahan</div>
            </a>
            <ul class="menu-sub">
                <li
                    class="menu-item {{ request()->routeIs("superadmin.template-items.index") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.template-items.index") }}"
                        class="menu-link"
                    >
                        <div>Daftar Template Bahan</div>
                    </a>
                </li>
                <li
                    class="menu-item {{ request()->routeIs("superadmin.template-items.create") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.template-items.create") }}"
                        class="menu-link"
                    >
                        <div>Tambah Template Bahan</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Subscription Packages -->
        <li
            class="menu-item {{ request()->routeIs("superadmin.subscription-packages.*") ? "active open" : "" }}"
        >
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-credit-card"></i>
                <div>Paket Subscription</div>
            </a>
            <ul class="menu-sub">
                <li
                    class="menu-item {{ request()->routeIs("superadmin.subscription-packages.index") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.subscription-packages.index") }}"
                        class="menu-link"
                    >
                        <div>Daftar Paket</div>
                    </a>
                </li>
                <li
                    class="menu-item {{ request()->routeIs("superadmin.subscription-packages.create") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.subscription-packages.create") }}"
                        class="menu-link"
                    >
                        <div>Tambah Paket</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Promo Codes -->
        <li
            class="menu-item {{ request()->routeIs("superadmin.promo-codes.*") ? "active open" : "" }}"
        >
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-purchase-tag"></i>
                <div>Kode Promo</div>
            </a>
            <ul class="menu-sub">
                <li
                    class="menu-item {{ request()->routeIs("superadmin.promo-codes.index") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.promo-codes.index") }}"
                        class="menu-link"
                    >
                        <div>Daftar Kode Promo</div>
                    </a>
                </li>
                <li
                    class="menu-item {{ request()->routeIs("superadmin.promo-codes.create") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.promo-codes.create") }}"
                        class="menu-link"
                    >
                        <div>Tambah Kode Promo</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Subscription Requests -->
        <li
            class="menu-item {{ request()->routeIs("superadmin.subscription-requests.*") ? "active open" : "" }}"
        >
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <div>Request Subscription</div>
            </a>
            <ul class="menu-sub">
                <li
                    class="menu-item {{ request()->routeIs("superadmin.subscription-requests.index") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("superadmin.subscription-requests.index") }}"
                        class="menu-link"
                    >
                        <div>Daftar Request</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>

    <style>
        /* Sidebar Styling */
        .layout-menu {
            width: 260px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease-in-out;
            transform: translateX(0);
        }

        .layout-menu.collapsed {
            width: 60px;
        }

        .layout-menu.collapsed .menu-inner,
        .layout-menu.collapsed .menu-header-text,
        .layout-menu.collapsed .app-brand-text,
        .layout-menu.collapsed .menu-sub {
            display: none;
        }

        .layout-menu.collapsed .app-brand-logo {
            margin: 0 auto;
        }

        .layout-menu.collapsed .menu-link {
            justify-content: center;
        }

        .layout-menu.collapsed .menu-icon {
            margin-right: 0;
        }

        /* Mobile menu */
        @media (max-width: 991.98px) {
            .layout-menu {
                transform: translateX(-100%);
                width: 260px !important;
                z-index: 1050;
                top: auto;
            }

            .layout-menu.show {
                transform: translateX(0);
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
            padding-left: 60px;
        }

        @media (max-width: 991.98px) {
            .layout-page {
                padding-left: 0 !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('layout-menu');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileMenuToggle =
                document.getElementById('mobileMenuToggle');
            const layoutOverlay = document.getElementById('layoutOverlay');
            const layoutPage =
                document.querySelector('.layout-page') || document.body;

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

            // Hover functionality for desktop
            if (sidebar) {
                sidebar.addEventListener('mouseenter', function () {
                    if (window.innerWidth >= 992) {
                        // Hanya aktif di desktop
                        sidebar.classList.remove('collapsed');
                        layoutPage.classList.remove('sidebar-collapsed');
                    }
                });

                sidebar.addEventListener('mouseleave', function () {
                    if (window.innerWidth >= 992) {
                        // Hanya aktif di desktop
                        const savedState =
                            localStorage.getItem('sidebarCollapsed');
                        if (savedState === 'true') {
                            sidebar.classList.add('collapsed');
                            layoutPage.classList.add('sidebar-collapsed');
                        }
                    }
                });
            }

            // Mobile toggle functionality
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function () {
                    sidebar.classList.remove('d-none');
                    sidebar.classList.toggle('show');
                    layoutOverlay.style.display = sidebar.classList.contains(
                        'show',
                    )
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

            // Restore sidebar state from localStorage
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true' && window.innerWidth >= 992) {
                sidebar.classList.add('collapsed');
                layoutPage.classList.add('sidebar-collapsed');
            }

            // Handle submenu toggles
            document
                .querySelectorAll('.menu-toggle')
                .forEach(function (toggle) {
                    toggle.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Jangan buka submenu jika sidebar collapsed
                        if (sidebar.classList.contains('collapsed')) {
                            return;
                        }

                        const menuItem = this.closest('.menu-item');
                        const isCurrentlyOpen =
                            menuItem.classList.contains('open');

                        // Tutup semua submenu lain di level yang sama
                        const parent = menuItem.parentElement;
                        parent
                            .querySelectorAll('.menu-item.open')
                            .forEach(function (openItem) {
                                if (openItem !== menuItem) {
                                    openItem.classList.remove('open');
                                }
                            });

                        // Toggle submenu saat ini
                        if (isCurrentlyOpen) {
                            menuItem.classList.remove('open');
                        } else {
                            menuItem.classList.add('open');
                        }
                    });
                });

            // Handle window resize
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992) {
                    // Desktop mode
                    sidebar.classList.remove('show');
                    layoutOverlay.style.display = 'none';
                    // Terapkan state dari localStorage
                    const savedState = localStorage.getItem('sidebarCollapsed');
                    if (savedState === 'true') {
                        sidebar.classList.add('collapsed');
                        layoutPage.classList.add('sidebar-collapsed');
                    } else {
                        sidebar.classList.remove('collapsed');
                        layoutPage.classList.remove('sidebar-collapsed');
                    }
                } else {
                    // Mobile mode
                    sidebar.classList.remove('collapsed');
                    layoutPage.classList.remove('sidebar-collapsed');
                }
            });
        });
    </script>
</aside>
