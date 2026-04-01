@php
    $user = auth()->user();
    $mitra = $user->mitra ?? null;
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo d-flex align-items-center justify-content-between px-3 py-2">
        <a href="{{ route('mitra.dashboard') }}"
            class="app-brand-link d-flex align-items-center text-decoration-none">
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
                        <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt="Foto"
                            class="rounded-circle" style="width: 100% !important; height: 100% !important; object-fit: cover !important;" />
                    </div>
                    <div class="flex-grow-1 text-start user-info">
                        <div class="fw-semibold text-black">
                            {{ $user->nama ?? 'Unknown' }}
                        </div>
                        <small class="text-muted">
                            Mitra
                        </small>
                    </div>
                    <i class="bx bx-chevron-up user-chevron"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('mitra.profile.index') }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <img src="{{ $mitra && $mitra->foto_diri ? Storage::url($mitra->foto_diri) : asset('admin/assets/img/avatars/1.png') }}" alt="Foto"
                                            class="rounded-circle"
                                            style="width: 100% !important; height: 100% !important; object-fit: cover !important;" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">
                                        {{ $user->nama ?? 'Unknown' }}
                                    </span>
                                    <small class="text-muted">Mitra</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item mt-1" href="{{ route('mitra.profile.index') }}">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">Profil Saya</span>
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

        <ul class="menu-inner py-1 flex-grow-1">
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Menu</span>
            </li>
            <li class="menu-item {{ request()->routeIs('mitra.dashboard') ? 'active' : '' }}">
                <a href="{{ route('mitra.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('mitra.dapur.*') ? 'active' : '' }}">
                <a href="{{ route('mitra.dapur.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-store"></i>
                    <div data-i18n="Dapur">Dapur</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('mitra.laporan-transaksi.*') ? 'active' : '' }}">
                <a href="{{ route('mitra.laporan-transaksi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Laporan Transaksi">Laporan Transaksi</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('mitra.laporan-aduan.*') ? 'active' : '' }}">
                <a href="{{ route('mitra.laporan-aduan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-message-error"></i>
                    <div data-i18n="Laporan Aduan">Laporan Aduan</div>
                </a>
            </li>

        </ul>
    </div>
</aside>
