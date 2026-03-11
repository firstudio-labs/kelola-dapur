<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo d-flex align-items-center justify-content-center px-3 py-2">
        <a href="{{ route('produksi.dashboard') }}" class="app-brand-link text-decoration-none">
            <span class="app-brand-logo demo">
                <img src="{{ asset('logo_kelola_dapur_black.png') }}" alt="Logo" style="height: 40px; width: auto" />
            </span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ Request::is('produksi/dashboard*') ? 'active' : '' }}">
            <a href="{{ route('produksi.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Menu Produksi</span>
        </li>

        <!-- Order Produksi -->
        <li class="menu-item {{ Request::is('produksi/order-produksi*') ? 'active' : '' }}">
            <a href="{{ route('produksi.order.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div>Order Produksi</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>

        <!-- Profile -->
        <li class="menu-item {{ Request::is('produksi/profile*') ? 'active' : '' }}">
            <a href="{{ route('produksi.profile.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Profil Saya</div>
            </a>
        </li>

        <!-- Logout -->
        <li class="menu-item">
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
            </form>
            <a href="javascript:void(0);" class="menu-link" onclick="document.getElementById('logout-form').submit();">
                <i class="menu-icon tf-icons bx bx-power-off"></i>
                <div>Keluar</div>
            </a>
        </li>
    </ul>
</aside>
