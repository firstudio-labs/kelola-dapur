<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
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
            {{-- <span class="app-brand-text demo fw-bolder ms-4 fs-3">
                Kepala Dapur
            </span> --}}
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
    <div class="menu-container d-flex flex-column">
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
                        <a
                            class="dropdown-item"
                            href="{{ route("kepala-dapur.edit-profil") }}"
                        >
                            <i class="bx bx-edit me-2"></i>
                            <span class="align-middle">Edit Profil</span>
                        </a>
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
                                <small>Renew to access all features</small>
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
                                <small>
                                    Subscription required for full features
                                </small>
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
                class="menu-item {{ request()->routeIs("kepala-dapur.dashboard") ? "active" : "" }}"
            >
                <a
                    href="{{ route("kepala-dapur.dashboard", ["dapur" => $idDapur]) }}"
                    class="menu-link"
                >
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>

            @if ($isSubscriptionActive)
                <!-- Kepala Dapur Header - Only show when subscription active -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Kepala Dapur</span>
                </li>

                <!-- Approval Permintaan Stok -->
                <li
                    class="menu-item {{ request()->routeIs("kepala-dapur.approvals.*") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("kepala-dapur.approvals.index", ["dapur" => $idDapur]) }}"
                        class="menu-link"
                    >
                        <i class="menu-icon tf-icons bx bx-check-circle"></i>
                        <div data-i18n="Approval Stok">Approval Stok</div>
                        @if (isset($pendingApprovalsCount) && $pendingApprovalsCount > 0)
                            <span class="badge bg-danger ms-2">
                                {{ $pendingApprovalsCount }}
                            </span>
                        @endif
                    </a>
                </li>

                <!-- Approval Transaksi Dapur -->
                <li
                    class="menu-item {{ request()->routeIs("kepala-dapur.approval-transaksi.*") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("kepala-dapur.approval-transaksi.index", ["dapur" => $idDapur]) }}"
                        class="menu-link"
                    >
                        <i class="menu-icon tf-icons bx bx-file"></i>
                        <div data-i18n="Approval Transaksi">
                            Approval Transaksi
                        </div>
                        @if (isset($pendingTransaksiCount) && $pendingTransaksiCount > 0)
                            <span class="badge bg-danger ms-2">
                                {{ $pendingTransaksiCount }}
                            </span>
                        @endif
                    </a>
                </li>

                <!-- Laporan Kekurangan Stok -->
                <li
                    class="menu-item {{ request()->routeIs("kepala-dapur.laporan-kekurangan.*") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("kepala-dapur.laporan-kekurangan.index") }}"
                        class="menu-link"
                    >
                        <i class="menu-icon tf-icons bx bx-error"></i>
                        <div data-i18n="Laporan Kekurangan Stok">
                            Laporan Kekurangan Stok
                        </div>
                        @if (isset($pendingShortageCount) && $pendingShortageCount > 0)
                            <span class="badge bg-danger ms-2">
                                {{ $pendingShortageCount }}
                            </span>
                        @endif
                    </a>
                </li>

                <!-- Konten Header - Only show when subscription active -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Konten</span>
                </li>

                <!-- Template Bahan -->
                <li
                    class="menu-item {{ request()->routeIs("kepala-dapur.template-items.*") ? "active open" : "" }}"
                >
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-package"></i>
                        <div data-i18n="Template Bahan">Template Bahan</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item {{ request()->routeIs("kepala-dapur.template-items.index") ? "active" : "" }}"
                        >
                            <a
                                href="{{ route("kepala-dapur.template-items.index") }}"
                                class="menu-link"
                            >
                                <div data-i18n="Daftar Template Bahan">
                                    Daftar Template Bahan
                                </div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ request()->routeIs("kepala-dapur.template-items.create") ? "active" : "" }}"
                        >
                            <a
                                href="{{ route("kepala-dapur.template-items.create") }}"
                                class="menu-link"
                            >
                                <div data-i18n="Tambah Template Bahan">
                                    Tambah Template Bahan
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Kelola Stok -->
                <li
                    class="menu-item {{ request()->routeIs("kepala-dapur.stock.*") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("kepala-dapur.stock.index", ["dapur" => $idDapur]) }}"
                        class="menu-link"
                    >
                        <i class="menu-icon tf-icons bx bx-food-menu"></i>
                        <div data-i18n="Kelola Stok">Kelola Stok</div>
                    </a>
                </li>
                <!-- Kelola Menu Makanan -->
                <li
                    class="menu-item {{ request()->routeIs("kepala-dapur.menu-makanan.*") ? "active" : "" }}"
                >
                    <a
                        href="{{ route("kepala-dapur.menu-makanan.index") }}"
                        class="menu-link"
                    >
                        <i class="menu-icon tf-icons bx bx-food-menu"></i>
                        <div data-i18n="Kelola Menu Makanan">
                            Kelola Menu Makanan
                        </div>
                    </a>
                </li>

                <!-- Kelola User -->
                <li
                    class="menu-item {{ request()->routeIs("kepala-dapur.users.*") ? "active open" : "" }}"
                >
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div data-i18n="Kelola User">Kelola User</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item {{ request()->routeIs("kepala-dapur.users.index") ? "active" : "" }}"
                        >
                            <a
                                href="{{ route("kepala-dapur.users.index", ["dapur" => $idDapur]) }}"
                                class="menu-link"
                            >
                                <div data-i18n="Daftar User">Daftar User</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ request()->routeIs("kepala-dapur.users.create") ? "active" : "" }}"
                        >
                            <a
                                href="{{ route("kepala-dapur.users.create", ["dapur" => $idDapur]) }}"
                                class="menu-link"
                            >
                                <div data-i18n="Tambah User">Tambah User</div>
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
                        "Approval Stok" => "bx-check-circle",
                        "Approval Transaksi" => "bx-file",
                        "Laporan Kekurangan" => "bx-error",
                        "Template Bahan" => "bx-package",
                        "Menu Makanan" => "bx-food-menu",
                        "Kelola User" => "bx-user",
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

            <!-- Subscription - Always accessible -->
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Account</span>
            </li>

            <li
                class="menu-item {{ request()->routeIs("kepala-dapur.subscription.*") ? "active open" : "" }}"
            >
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i
                        class="menu-icon tf-icons bx bx-credit-card @if(!$isSubscriptionActive) text-warning @endif"
                    ></i>
                    <div>
                        Subscription
                        @if (! $isSubscriptionActive)
                            <span
                                class="badge bg-warning text-dark ms-1"
                                style="font-size: 0.6rem"
                            >
                                !
                            </span>
                        @endif
                    </div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->routeIs("kepala-dapur.subscription.index") ? "active" : "" }}"
                    >
                        <a
                            href="{{ route("kepala-dapur.subscription.index", $idDapur) }}"
                            class="menu-link"
                        >
                            <div>Daftar Request</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->routeIs("kepala-dapur.subscription.create", "kepala-dapur.subscription.choose-package") ? "active" : "" }}"
                    >
                        <a
                            href="{{ route("kepala-dapur.subscription.choose-package", $idDapur) }}"
                            class="menu-link @if(!$isSubscriptionActive) text-warning fw-semibold @endif"
                        >
                            <div>
                                @if (! $isSubscriptionActive)
                                    <i class="bx bx-plus-circle me-1"></i>
                                @endif

                                Buat Request Baru
                            </div>
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
                        To regain full access to all features, please renew your
                        subscription.
                    </p>
                </div>
                <div
                    class="alert alert-info bg-light-info border-0 d-flex align-items-center justify-content-center p-3"
                    role="alert"
                >
                    <i class="bx bx-info-circle me-2"></i>
                    <small>
                        Renew subscribe now to continue managing your kitchen!
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
    /* CSS untuk toggle sidebar */
    .layout-menu {
        display: flex;
        flex-direction: column;
        transition:
            width 0.3s ease-in-out,
            transform 0.3s ease-in-out;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 260px;
        z-index: 1045;
        overflow: hidden;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .layout-menu::-webkit-scrollbar {
        display: none;
    }

    .app-brand {
        flex-shrink: 0;
    }

    .menu-container {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .menu-container::-webkit-scrollbar {
        display: none;
    }

    .menu-inner {
        flex-grow: 1;
        overflow-y: auto;
        padding-bottom: 50px; /* Diperbesar padding-bottom agar scroll lebih luas ke bawah */
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .menu-inner::-webkit-scrollbar {
        display: none;
    }

    .user-profile-section {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
        min-height: 60px;
    }

    .menu-item.disabled .menu-link {
        pointer-events: none;
        opacity: 0.6;
    }

    .menu-item.disabled:hover .menu-link {
        background: transparent !important;
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }

    .tooltip {
        font-size: 0.875rem;
    }

    .alert {
        border: 1px solid transparent;
        border-radius: 0.375rem;
    }

    .alert-warning {
        background-color: rgba(255, 193, 7, 0.1);
        border-color: rgba(255, 193, 7, 0.3);
        color: #856404;
    }

    .layout-menu.collapsed {
        width: 78px;
    }

    .layout-menu.collapsed .app-brand-text,
    .layout-menu.collapsed .menu-header-text,
    .layout-menu.collapsed .menu-link > div:not(.menu-icon) {
        display: none;
    }

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

    .layout-menu.collapsed .alert {
        display: none;
    }

    .layout-menu .user-profile-section {
        display: block !important;
        position: relative;
        z-index: 1;
        order: -1;
    }

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

    .layout-menu.collapsed .layout-menu-toggle i {
        transform: rotate(180deg);
    }

    .layout-menu.collapsed .menu-sub {
        display: none !important;
    }

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
            height: 100vh;
            overflow: hidden;
        }

        .layout-menu.show {
            transform: translateX(0);
        }

        .layout-menu.collapsed {
            width: 260px !important;
            transform: translateX(-100%);
        }

        .layout-menu.collapsed.show {
            transform: translateX(0);
        }

        .layout-menu.collapsed .app-brand-text,
        .layout-menu.collapsed .menu-header-text,
        .layout-menu.collapsed .menu-link > div:not(.menu-icon) {
            display: block;
        }

        .layout-menu.collapsed .alert {
            display: block;
        }

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

        .layout-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        #mobileMenuToggle {
            background: var(--bs-primary);
            color: white;
            transition: all 0.3s ease-in-out;
        }

        #mobileMenuToggle:hover {
            background: var(--bs-primary-dark);
        }

        .layout-menu.show + #mobileMenuToggle i {
            transform: rotate(180deg);
        }

        .layout-page {
            padding-left: 0 !important;
        }
    }

    /* Ensure sidebar visibility at 992px and above */
    @media (min-width: 992px) {
        .layout-menu {
            display: flex !important;
            transform: translateX(0) !important;
            width: 260px;
        }

        .layout-menu.collapsed {
            width: 78px;
        }

        .layout-page {
            padding-left: 260px;
            transition: padding-left 0.3s ease-in-out;
        }

        .layout-page.sidebar-collapsed {
            padding-left: 78px;
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

                // Save state to localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);

                // Close all submenus when sidebar is collapsed
                if (isCollapsed) {
                    document
                        .querySelectorAll('.menu-item.open')
                        .forEach(function (item) {
                            item.classList.remove('open');
                        });
                }
            });
        }

        // Mobile toggle functionality
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function () {
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

        // Handle submenu toggles - only allow when subscription is active
        document.querySelectorAll('.menu-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Don't open submenu if sidebar is collapsed in desktop mode
                if (
                    sidebar.classList.contains('collapsed') &&
                    window.innerWidth >= 992
                ) {
                    return;
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

        // Handle disabled menu item clicks - show subscription modal
        document
            .querySelectorAll('.menu-item.disabled .menu-link')
            .forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Show a modal notification about subscription requirement
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
            } else {
                // Mobile mode
                sidebar.classList.remove('collapsed');
                layoutPage.classList.remove('sidebar-collapsed');
                if (!sidebar.classList.contains('show')) {
                    layoutOverlay.style.display = 'none';
                }
            }
        });

        // Initialize subscription-related functionality
        initializeSubscriptionFeatures();
    });

    // Function to handle subscription-related features
    function initializeSubscriptionFeatures() {
        const isSubscriptionActive =
            {{ $isSubscriptionActive ? "true" : "false" }};
        const subscriptionStatus = '{{ $subscriptionStatus ?? "" }}';

        // Add pulsing effect to subscription menu when expired
        if (!isSubscriptionActive) {
            const subscriptionMenuItem = document.querySelector(
                '.menu-item:has([href*="subscription"])',
            );
            if (subscriptionMenuItem) {
                subscriptionMenuItem.style.animation =
                    'pulse-warning 2s infinite';
            }
        }

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

        .menu-icon {
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .layout-menu:hover .menu-icon {
            transform: scale(1.05);
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
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }

        .menu-item.disabled:hover {
            transform: translateX(2px);
            transition: transform 0.2s ease;
        }

        .badge {
            animation: badge-pulse 2s infinite;
        }

        @keyframes badge-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .user-profile-section .nav-link {
            transition: all 0.3s ease;
        }

        .user-profile-section .nav-link:hover {
            background: rgba(255, 255, 255, 0.25) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

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

    // Additional utility functions for subscription management
    function showSubscriptionModal() {
        const subscriptionModal = new bootstrap.Modal(
            document.getElementById('subscriptionExpiredModal'),
            {
                backdrop: 'static',
                keyboard: false,
            },
        );
        subscriptionModal.show();
    }

    // Initialize subscription status monitoring
    document.addEventListener('DOMContentLoaded', function () {
        const subscriptionStatus = '{{ $subscriptionStatus ?? "" }}';
        const daysLeft = {{ session("subscription_days_left", 0) }};

        // Show expiration warning for Kepala Dapur users
        if (subscriptionStatus === 'expiring_soon' && daysLeft <= 5) {
            setTimeout(function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Subscription Expiring Soon!',
                        html: `
                            <div class="text-center">
                                <i class="bx bx-time bx-lg text-warning mb-3"></i>
                                <p>The dapur subscription expires in <strong>${daysLeft}</strong> day${daysLeft !== 1 ? 's' : ''}.</p>
                                <p class="text-muted">Please renew before expiration.</p>
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

    // Export functions for external use
    window.showSubscriptionModal = showSubscriptionModal;
</script>
