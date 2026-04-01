<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    @php
        $isSubscriptionActive = session('is_subscription_active', false);
    @endphp
    <div class="app-brand demo d-flex align-items-center justify-content-center px-3 py-2">
        <a href="{{ route('akuntan.dashboard') }}" class="app-brand-link text-decoration-none">
            <span class="app-brand-logo demo">
                <img src="{{ asset('logo_kelola_dapur_black.png') }}" alt="Logo" style="height: 40px; width: auto" />
            </span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <div class="user-profile-section mt-3 px-3 pb-3">
        <div class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center w-100 p-2 rounded"
                href="javascript:void(0);" data-bs-toggle="dropdown"
                style="background: rgba(255, 255, 255, 0.15); transition: 0.3s; border: 1px solid rgba(255, 255, 255, 0.2);"
                onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                onmouseout="this.style.background='rgba(255,255,255,0.15)'"
                aria-expanded="false">
                <div class="avatar avatar-online me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                    @php $foto = auth()->user()->akuntan->foto_diri ?? null; @endphp
                    <img src="{{ $foto ? Storage::url($foto) : asset('admin/assets/img/avatars/1.png') }}"
                        alt class="rounded-circle" style="width: 100% !important; height: 100% !important; object-fit: cover !important;" />
                </div>
                <div class="flex-grow-1 text-start user-info">
                    <div class="fw-semibold text-black">{{ auth()->user()->nama ?? 'Unknown' }}</div>
                    <small class="text-muted">Akuntan</small>
                </div>
                <i class="bx bx-chevron-up user-chevron"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end w-100">
                <li>
                    <a class="dropdown-item" href="{{ route('akuntan.profile.edit') }}">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online" style="width: 40px; height: 40px;">
                                    <img src="{{ $foto ? Storage::url($foto) : asset('admin/assets/img/avatars/1.png') }}"
                                        alt class="rounded-circle" style="width: 100% !important; height: 100% !important; object-fit: cover !important;" />
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block">{{ auth()->user()->nama ?? 'Unknown' }}</span>
                                <small class="text-muted">Akuntan</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li><div class="dropdown-divider"></div></li>
                <li>
                    <a class="dropdown-item" href="{{ route('akuntan.profile.edit') }}">
                        <i class="bx bx-user me-2"></i><span class="align-middle">Profil Saya</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('akuntan.profile.security.edit') }}">
                        <i class="bx bx-lock-alt me-2"></i><span class="align-middle">Keamanan</span>
                    </a>
                </li>
                <li><div class="dropdown-divider"></div></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bx bx-power-off me-2"></i><span class="align-middle">Log Out</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <ul class="menu-inner py-1">

        {{-- Dashboard --}}
        <li class="menu-item {{ Request::is('akuntan/dashboard*') ? 'active' : '' }}">
            <a href="{{ route('akuntan.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Akuntansi</span>
        </li>

        {{-- Transaksi --}}
        <li class="menu-item {{ Request::is('akuntan/transaksi*') ? 'active' : '' }}">
            <a href="{{ route('akuntan.transaksi.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-transfer-alt"></i>
                <div>Transaksi</div>
            </a>
        </li>

        {{-- Buku Kas Umum --}}
        <li class="menu-item {{ Request::is('akuntan/buku-kas*') ? 'active' : '' }}">
            <a href="{{ route('akuntan.buku-kas.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book-open"></i>
                <div>Buku Kas Umum</div>
            </a>
        </li>

        {{-- Buku Pembantu --}}
        <li class="menu-item {{ Request::is('akuntan/buku-pembantu*') ? 'active' : '' }}">
            <a href="{{ route('akuntan.buku-pembantu.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div>Buku Pembantu</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Laporan</span>
        </li>

        {{-- Laporan --}}
        <li class="menu-item {{ Request::is('akuntan/laporan*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                <div>Laporan</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Request::is('akuntan/laporan/resume*') ? 'active' : '' }}">
                    <a href="{{ route('akuntan.laporan.resume') }}" class="menu-link">
                        <div>Resume Penerimaan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link text-muted" style="cursor: not-allowed;">
                        <div>Penggunaan Anggaran</div>
                        <span class="badge bg-label-secondary ms-auto" style="font-size: 0.65rem;">Soon</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link text-muted" style="cursor: not-allowed;">
                        <div>Pengeluaran Bulanan</div>
                        <span class="badge bg-label-secondary ms-auto" style="font-size: 0.65rem;">Soon</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Dokumen (Locked) --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link text-muted" style="cursor: not-allowed;">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div>Dokumen</div>
                <span class="badge bg-label-secondary ms-auto" style="font-size: 0.65rem;">Soon</span>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Lainnya</span>
        </li>

        {{-- Pengaturan --}}
        <li class="menu-item {{ Request::is('akuntan/pengaturan*') ? 'active' : '' }}">
            <a href="{{ route('akuntan.pengaturan.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Pengaturan</div>
            </a>
        </li>

        <li class="menu-item">
            <form action="{{ route('logout') }}" method="POST" id="logout-form-sidebar">@csrf</form>
            <a href="javascript:void(0);" class="menu-link" onclick="document.getElementById('logout-form-sidebar').submit();">
                <i class="menu-icon tf-icons bx bx-power-off"></i>
                <div>Keluar</div>
            </a>
        </li>
    </ul>
</aside>

<style>
    .user-profile-section { border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
    .user-info { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
    .user-chevron { transition: transform 0.3s ease; font-size: 1.2rem; color: #697a8d; }
    .dropdown.show .user-chevron { transform: rotate(180deg); }
</style>
