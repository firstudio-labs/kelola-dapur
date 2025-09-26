```php
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
                    src="{{ asset('logo_kelola_dapur_black.png') }}"
                    alt="Logo"
                    style="height: 45px; width: auto"
                />
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
        <!-- User Profile Section -->
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
                            src="{{ asset('admin/assets/img/avatars/1.png') }}"
                            alt
                            class="w-px-40 h-auto rounded-circle"
                        />
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
                                    <div class="avatar avatar-online">
                                        <img
                                            src="{{ asset('admin/assets/img/avatars/1.png') }}"
                                            alt
                                            class="w-px-40 h-auto rounded-circle"
                                        />
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
                                            Expires: {{ session('subscription_end') ? \Carbon\Carbon::parse(session('subscription_end'))->format('d M Y') : 'N/A' }}
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

        @php
            $isSubscriptionActive = session('is_subscription_active', false);
            $subscriptionStatus = session('subscription_status', null);
            $idDapur = session('id_dapur', $dapur->id_dapur ?? null); // Cocokkan dengan $dapur dari controller
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

        <!-- Menu Utama -->
        <ul class="menu-inner py-1 flex-grow-1">
            <!-- Dashboard - Always accessible -->
            <li
                class="menu-item {{ request()->routeIs('admin-gudang.dashboard') ? 'active' : '' }}"
            >
                <a
                    href="{{ route('admin-gudang.dashboard', ['dapur' => $idDapur]) }}"
                    class="menu-link"
                >
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>

            @if ($isSubscriptionActive)
                <!-- Admin Gudang Header - Only show when subscription active -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Admin Gudang</span>
                </li>

                <!-- Kelola Stok -->
                <li
                    class="menu-item {{ request()->routeIs('admin-gudang.stock.*') ? 'active open' : '' }}"
                >
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-package"></i>
                        <div data-i18n="Kelola Stok">Kelola Stok</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item {{ request()->routeIs('admin-gudang.stock.index') ? 'active' : '' }}"
                        >
                            <a
                                href="{{ route('admin-gudang.stock.index', ['dapur' => $idDapur]) }}"
                                class="menu-link"
                            >
                                <div data-i18n="Daftar Stok">Daftar Stok</div>
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
                        'Kelola Stok' => 'bx-package',
                        'Permintaan Stok' => 'bx-list-ul',
                        'Laporan Stok' => 'bx-chart',
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('layout-menu');
        const layoutPage = document.querySelector('.layout-page');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileMenuToggle = document.querySelector('.menu-toggle-mobile'); // Asumsi ada jika diperlukan
        const layoutOverlay = document.querySelector('.layout-overlay');

        // Sidebar toggle functionality
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                layoutPage.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
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
            // Default to collapsed state on desktop
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
        const isSubscriptionActive = {{ $isSubscriptionActive ? 'true' : 'false' }};

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
                sidebar.classList.add('collapsed');
                layoutPage.classList.add('sidebar-collapsed');
            } else {
                // Mobile mode
                sidebar.classList.remove('collapsed');
                layoutPage.classList.remove('sidebar-collapsed');
            }
        });

        // Initialize subscription-related features
        initializeSubscriptionFeatures();
    });

    // Function to handle subscription-related features for Admin Gudang
    function initializeSubscriptionFeatures() {
        const isSubscriptionActive = {{ $isSubscriptionActive ? 'true' : 'false' }};
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

    // Additional utility functions for Admin Gudang
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

    // Initialize subscription status monitoring for Admin Gudang
    document.addEventListener('DOMContentLoaded', function () {
        const subscriptionStatus = '{{ $subscriptionStatus ?? "" }}';
        const daysLeft = {{ session('subscription_days_left', 0) }};

        // Show expiration warning for Admin Gudang users
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
            }, 3000);
        }
    });

    // Add CSS for basic styling without fade animations
    const style = document.createElement('style');
    style.textContent = 
        .subscription-expired .menu-link {
            background: rgba(220, 53, 69, 0.1) !important;
            border-left: 3px solid #dc3545;
        }

        .subscription-expiring .menu-link {
            background: rgba(255, 193, 7, 0.1) !important;
            border-left: 3px solid #ffc107;
        }

        /* Basic transitions for sidebar */
        .layout-menu {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .layout-menu .menu-link {
            transition: background 0.2s ease-in-out;
        }

        .layout-menu:not(.collapsed) .menu-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Icon animations */
        .menu-icon {
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .menu-icon:hover {
            transform: scale(1.05);
        }

        /* Submenu slide animation */
        .menu-sub {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        .menu-item.open .menu-sub {
            max-height: 500px;
        }

        /* User profile hover enhancement */
        .user-profile-section .nav-link {
            transition: all 0.3s ease;
        }

        .user-profile-section .nav-link:hover {
            background: rgba(255, 255, 255, 0.25) !important;
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
    ;
    document.head.appendChild(style);
</script>
