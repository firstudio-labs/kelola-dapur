<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo d-flex align-items-center justify-content-center px-3 py-2">
        <a href="{{ route('distributor.dashboard') }}" class="app-brand-link text-decoration-none">
            <span class="app-brand-logo demo">
                <img src="{{ asset('logo_kelola_dapur_black.png') }}" alt="Logo" style="height: 40px; width: auto" />
            </span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        
        <li class="menu-item {{ Request::is('distributor/dashboard*') ? 'active' : '' }}">
            <a href="{{ route('distributor.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Menu Distributor</span>
        </li>

        <li class="menu-item {{ Request::is('distributor/order-distribusi*') ? 'active' : '' }}">
            <a href="{{ route('distributor.order.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>Order Pengiriman</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>

        <li class="menu-item {{ Request::is('distributor/profile*') ? 'active' : '' }}">
            <a href="{{ route('distributor.profile.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Profil Saya</div>
            </a>
        </li>

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
