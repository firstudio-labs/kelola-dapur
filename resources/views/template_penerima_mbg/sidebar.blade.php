<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('penerima-mbg.dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Portal MBG</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('penerima-mbg.dashboard') ? 'active' : '' }}">
            <a href="{{ route('penerima-mbg.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Profil Saya</span>
        </li>

        <li class="menu-item {{ request()->routeIs('penerima-mbg.profile.*') ? 'active' : '' }}">
            <a href="{{ route('penerima-mbg.profile.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Profil">Profil Saya</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('penerima-mbg.porsi.*') ? 'active' : '' }}">
            <a href="{{ route('penerima-mbg.porsi.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dish"></i>
                <div data-i18n="Porsi">Jumlah Porsi MBG</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Akun</span>
        </li>

        <li class="menu-item">
            <a href="{{ route('logout') }}" class="menu-link"
               onclick="event.preventDefault(); document.getElementById('logout-form-side').submit();">
                <i class="menu-icon tf-icons bx bx-power-off"></i>
                <div data-i18n="Logout">Logout</div>
            </a>
        </li>
        <form id="logout-form-side" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </ul>
</aside>
