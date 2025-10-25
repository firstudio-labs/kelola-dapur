<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Brand + Toggle -->
    <div class="app-brand demo d-flex align-items-center justify-content-between px-3 py-2">
        <!-- Logo -->
        <a href="/" class="app-brand-link d-flex align-items-center text-decoration-none">
            <span class="app-brand-logo demo">
                <img src="{{ asset('logo_kelola_dapur_black.png') }}" alt="Logo" style="height: 45px; width: auto" />
            </span>
        </a>
    </div>

    <!-- Menu Container with flex layout -->
    <div class="menu-container d-flex flex-column h-100">
        <!-- User Profile Section - Moved to Top of Menu -->
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
                    <div class="avatar avatar-online me-3">
                        <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt
                            class="w-px-40 h-auto rounded-circle" />
                    </div>
                    <div class="flex-grow-1 text-start user-info">
                        <div class="fw-semibold text-black">
                            {{ auth()->user()->nama ?? 'Unknown' }}
                        </div>
                        <small class="text-muted">
                            {{ ucfirst(str_replace('_', ' ', session('role_type') ?? 'Unknown')) }}
                        </small>
                    </div>
                    <i class="bx bx-chevron-up user-chevron"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">
                                        {{ auth()->user()->nama ?? 'Unknown' }}
                                    </span>
                                    <small class="text-muted">
                                        {{ ucfirst(str_replace('_', ' ', session('role_type') ?? 'Unknown')) }}
                                    </small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
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

        <!-- Menu Utama -->
        <ul class="menu-inner py-1 flex-grow-1">
            <!-- Dashboard -->
            <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>

            <!-- Admin Header -->
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Admin</span>
            </li>

            <!-- Dapur -->
            <li class="menu-item {{ request()->routeIs('superadmin.dapur.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-buildings"></i>
                    <div data-i18n="Dapur">Dapur</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('superadmin.dapur.index') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.dapur.index') }}" class="menu-link">
                            <div data-i18n="Daftar Dapur">Daftar Dapur</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Menu Makanan -->
            <li class="menu-item {{ request()->routeIs('superadmin.menu-makanan.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-food-menu"></i>
                    <div data-i18n="Menu Makanan">Menu Makanan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('superadmin.menu-makanan.index') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.menu-makanan.index') }}" class="menu-link">
                            <div data-i18n="Daftar Menu Makanan">Daftar Menu Makanan</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('superadmin.menu-makanan.create') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.menu-makanan.create') }}" class="menu-link">
                            <div data-i18n="Tambah Menu Makanan">Tambah Menu Makanan</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Template Bahan -->
            <li class="menu-item {{ request()->routeIs('superadmin.template-items.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-package"></i>
                    <div data-i18n="Template Bahan">Template Bahan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('superadmin.template-items.index') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.template-items.index') }}" class="menu-link">
                            <div data-i18n="Daftar Template Bahan">Daftar Template Bahan</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('superadmin.template-items.create') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.template-items.create') }}" class="menu-link">
                            <div data-i18n="Tambah Template Bahan">Tambah Template Bahan</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Subscription Packages -->
            <li class="menu-item {{ request()->routeIs('superadmin.subscription-packages.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-credit-card"></i>
                    <div data-i18n="Paket Subscription">Paket Subscription</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->routeIs('superadmin.subscription-packages.index') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.subscription-packages.index') }}" class="menu-link">
                            <div data-i18n="Daftar Paket">Daftar Paket</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->routeIs('superadmin.subscription-packages.create') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.subscription-packages.create') }}" class="menu-link">
                            <div data-i18n="Tambah Paket">Tambah Paket</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Promo Codes -->
            <li class="menu-item {{ request()->routeIs('superadmin.promo-codes.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-purchase-tag"></i>
                    <div data-i18n="Kode Promo">Kode Promo</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('superadmin.promo-codes.index') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.promo-codes.index') }}" class="menu-link">
                            <div data-i18n="Daftar Kode Promo">Daftar Kode Promo</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('superadmin.promo-codes.create') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.promo-codes.create') }}" class="menu-link">
                            <div data-i18n="Tambah Kode Promo">Tambah Kode Promo</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Subscription Requests -->
            <li class="menu-item {{ request()->routeIs('superadmin.subscription-requests.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-receipt"></i>
                    <div data-i18n="Request Subscription">Request Subscription</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->routeIs('superadmin.subscription-requests.index') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.subscription-requests.index') }}" class="menu-link">
                            <div data-i18n="Daftar Request">Daftar Request</div>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>

<!-- Mobile Menu Toggle Button (tampil hanya di mobile) -->
<button class="btn btn-primary position-fixed d-lg-none" id="mobileMenuToggle"
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
    ">
    <i class="bx bx-chevron-right bx-sm align-middle"></i>
</button>

<!-- Overlay untuk mobile -->
<div class="layout-overlay d-lg-none" id="layoutOverlay" style="display: none"></div>

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

    /* Tablet & Mobile: Sidebar hidden by default, show with toggle */
    @media (max-width: 991.98px) {
        .layout-menu {
            transform: translateX(-100%);
        }

        .layout-menu.show {
            transform: translateX(0);
        }
    }

    /* Desktop: Always visible */
    @media (min-width: 992px) {
        .layout-menu {
            transform: translateX(0) !important;
            display: block !important;
        }
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
        transform: scale(1.1);
    }

    /* Pastikan user profile section tetap terlihat */
    .layout-menu .user-profile-section {
        display: block !important;
        position: relative;
        z-index: 1;
        order: -1;
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
        .layout-menu.collapsed .menu-link>div:not(.menu-icon) {
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
        .layout-menu.show+#mobileMenuToggle i {
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

        .layout-page,
        .layout-page.sidebar-collapsed {
            padding-left: 0 !important;
        }
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
    .layout-menu.collapsed .menu-link>div:not(.menu-icon) {
        opacity: 0;
        transform: translateX(-10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .layout-menu:not(.collapsed) .app-brand-text,
    .layout-menu:not(.collapsed) .menu-header-text,
    .layout-menu:not(.collapsed) .menu-link>div:not(.menu-icon) {
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

    /* User profile hover enhancement */
    .user-profile-section .nav-link {
        transition: all 0.3s ease;
    }

    .user-profile-section .nav-link:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('layout-menu');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const layoutOverlay = document.getElementById('layoutOverlay');
        const layoutPage = document.querySelector('.layout-page') || document.body;

        // Desktop toggle functionality
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                layoutPage.classList.toggle('sidebar-collapsed');

                // Simpan state ke localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);

                // Tutup semua submenu saat sidebar collapsed
                if (isCollapsed) {
                    document.querySelectorAll('.menu-item.open').forEach(function(item) {
                        item.classList.remove('open');
                    });
                }
            });
        }

        // Mobile toggle functionality
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                layoutOverlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            });
        }

        // Close mobile menu when clicking overlay
        if (layoutOverlay) {
            layoutOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                layoutOverlay.style.display = 'none';
            });
        }

        // Restore sidebar state from localStorage - Start expanded by default on desktop
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (window.innerWidth >= 992) {
            // Ensure sidebar is expanded by default on desktop
            sidebar.classList.remove('collapsed');
            layoutPage.classList.remove('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'false');
        }

        // Handle submenu toggles
        document.querySelectorAll('.menu-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Don't open submenu if sidebar is collapsed (desktop)
                if (sidebar.classList.contains('collapsed') && window.innerWidth >= 992) {
                    return;
                }

                const menuItem = this.closest('.menu-item');
                const isCurrentlyOpen = menuItem.classList.contains('open');

                // Close all other submenus at the same level
                const parent = menuItem.parentElement;
                parent.querySelectorAll('.menu-item.open').forEach(function(openItem) {
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

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                // Desktop mode
                sidebar.classList.remove('show');
                layoutOverlay.style.display = 'none';

                sidebar.classList.remove('collapsed');
                layoutPage.classList.remove('sidebar-collapsed');
            } else {
                // Mobile mode
                sidebar.classList.remove('collapsed');
                layoutPage.classList.remove('sidebar-collapsed');
            }
        });
    });
</script>
