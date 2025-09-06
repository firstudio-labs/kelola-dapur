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

        <!-- Desktop Toggle Button - SAMA SEPERTI KEPALA DAPUR -->
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
                            {{ ucfirst(str_replace("_", " ", auth()->user()->userRole->role_type ?? "Unknown")) }}
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
                                        {{ ucfirst(str_replace("_", " ", auth()->user()->userRole->role_type ?? "Unknown")) }}
                                    </small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    {{--
                        @if (auth()->user()->userRole && auth()->user()->userRole->role_type === "ahli_gizi")
                        <li>
                        <a class="dropdown-item" href="#">
                        <i class="bx bx-edit me-2"></i>
                        <span class="align-middle">Edit Profil</span>
                        </a>
                        </li>
                        @endif
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

        <!-- Menu Utama -->
        <ul class="menu-inner py-1 flex-grow-1">
            <!-- Dashboard -->
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

            <!-- Ahli Gizi Header -->
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
                            <div data-i18n="Buat Transaksi">Buat Transaksi</div>
                        </a>
                    </li>
                </ul>
            </li>
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

<style>
    /* CSS untuk toggle sidebar - IDENTIK DENGAN KEPALA DAPUR */
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('layout-menu');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
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
                    const savedState = localStorage.getItem('sidebarCollapsed');
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

        // Restore sidebar state from localStorage
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true' && window.innerWidth >= 992) {
            sidebar.classList.add('collapsed');
            layoutPage.classList.add('sidebar-collapsed');
        }

        // Handle submenu toggles
        document.querySelectorAll('.menu-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Jangan buka submenu jika sidebar collapsed
                if (sidebar.classList.contains('collapsed')) {
                    return;
                }

                const menuItem = this.closest('.menu-item');
                const isCurrentlyOpen = menuItem.classList.contains('open');

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
