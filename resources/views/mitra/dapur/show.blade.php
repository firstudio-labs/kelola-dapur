@extends('template_mitra.layout')
@section('title', 'Detail Dapur')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('mitra.dashboard') }}" class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a href="{{ route('mitra.dapur.index') }}" class="text-muted me-2">
                                Manajemen Dapur
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Detail Dapur</span>
                        </nav>
                        <h4 class="mb-1">Detail Dapur — {{ $dapur->nama_dapur }}</h4>
                        <p class="mb-0 text-muted">Informasi lengkap mengenai dapur yang Anda ikuti.</p>
                    </div>
                    <div>
                        <a href="{{ route('mitra.dapur.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-tabs nav-justified w-100" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-top-info" aria-controls="navs-top-info" aria-selected="true">
                                <i class="bx bx-info-circle me-1"></i> Informasi Dapur
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-top-org" aria-controls="navs-top-org" aria-selected="false">
                                <i class="bx bx-group me-1"></i> Organisasi Dapur
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-top-stok" aria-controls="navs-top-stok" aria-selected="false">
                                <i class="bx bx-package me-1"></i> Stok
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-top-menu" aria-controls="navs-top-menu" aria-selected="false">
                                <i class="bx bx-food-menu me-1"></i> Menu Makanan
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-top-mbg" aria-controls="navs-top-mbg" aria-selected="false">
                                <i class="bx bx-list-ol me-1"></i> Penerima MBG
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-top-fasilitas" aria-controls="navs-top-fasilitas"
                                aria-selected="false">
                                <i class="bx bx-wrench me-1"></i> Fasilitas Dapur
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content bg-transparent border-0 p-0 shadow-none mt-4">

                        <div class="tab-pane fade show active" id="navs-top-info" role="tabpanel">
                            <div class="row g-4 mb-4">
                                <div class="col-sm-6 col-xl-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span class="text-muted d-block mb-1">Total Staf</span>
                                                    <div class="d-flex align-items-center">
                                                        <h4 class="mb-0 me-2">{{ count($staffMembers) }}</h4>
                                                    </div>
                                                </div>
                                                <div class="avatar bg-light-primary rounded p-2">
                                                    <i class="bx bx-group text-primary fs-3"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span class="text-muted d-block mb-1">Bahan Baku</span>
                                                    <div class="d-flex align-items-center">
                                                        <h4 class="mb-0 me-2">{{ $dapur->stockItems->count() }}</h4>
                                                    </div>
                                                </div>
                                                <div class="avatar bg-light-warning rounded p-2">
                                                    <i class="bx bx-package text-warning fs-3"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span class="text-muted d-block mb-1">Total Menu</span>
                                                    <div class="d-flex align-items-center">
                                                        <h4 class="mb-0 me-2">{{ $menus->count() }}</h4>
                                                    </div>
                                                </div>
                                                <div class="avatar bg-light-info rounded p-2">
                                                    <i class="bx bx-food-menu text-info fs-3"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span class="text-muted d-block mb-1">Penerima MBG</span>
                                                    <div class="d-flex align-items-center">
                                                        <h4 class="mb-0 me-2">{{ $penerimaMbgList->count() }}</h4>
                                                    </div>
                                                </div>
                                                <div class="avatar bg-light-success rounded p-2">
                                                    <i class="bx bx-building text-success fs-3"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">Spesifikasi Detail Dapur</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label fw-bold">Nama Dapur</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0">{{ $dapur->nama_dapur }}</p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label fw-bold">No Registrasi SPPG</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0">
                                                        {{ $dapur->no_registrasi_sppg ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label fw-bold">NIK Pemilik</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0">
                                                        {{ $dapur->nik_pemilik ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label fw-bold">Telepon</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0">{{ $dapur->telepon ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label fw-bold">Alamat</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0">{{ $dapur->alamat ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label fw-bold">Wilayah</label>
                                                <div class="col-sm-8">
                                                    <div class="mt-1">
                                                        @if ($dapur->province_name)
                                                            <div class="d-flex mb-1"><span class="text-muted me-2"
                                                                    style="min-width:90px;">Provinsi</span><span>{{ $dapur->province_name }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($dapur->regency_name)
                                                            <div class="d-flex mb-1"><span class="text-muted me-2"
                                                                    style="min-width:90px;">Kab/Kota</span><span>{{ $dapur->regency_name }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($dapur->district_name)
                                                            <div class="d-flex mb-1"><span class="text-muted me-2"
                                                                    style="min-width:90px;">Kecamatan</span><span>{{ $dapur->district_name }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($dapur->village_name)
                                                            <div class="d-flex mb-1"><span class="text-muted me-2"
                                                                    style="min-width:90px;">Kelurahan/Desa</span><span>{{ $dapur->village_name }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($dapur->alamat)
                                                            <div class="d-flex mb-1"><span class="text-muted me-2"
                                                                    style="min-width:90px;">Alamat
                                                                    Lengkap</span><span>{{ $dapur->alamat }}</span></div>
                                                        @endif
                                                        @if (!$dapur->province_name && !$dapur->regency_name && !$dapur->district_name && !$dapur->village_name)
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($dapur->kepalaDapur && $dapur->kepalaDapur->isNotEmpty())
                                        @foreach ($dapur->kepalaDapur as $kd)
                                            <div class="card border-0 shadow-sm mb-4">
                                                <div class="card-header">
                                                    <h5 class="mb-0">Profil Kepala Dapur</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-3 text-center mb-3 mb-md-0">
                                                            @if ($kd->foto_diri)
                                                                <img src="{{ Storage::url($kd->foto_diri) }}"
                                                                    alt="Foto Kepala Dapur"
                                                                    class="rounded-circle img-fluid shadow-sm"
                                                                    style="width: 140px; height: 140px; object-fit: cover;">
                                                            @else
                                                                <div class="avatar avatar-xl mx-auto"
                                                                    style="width: 140px; height: 140px;">
                                                                    <span
                                                                        class="avatar-initial rounded-circle bg-label-primary fs-1"
                                                                        style="display: flex; align-items: center; justify-content: center;"><i
                                                                            class="bx bx-user"
                                                                            style="font-size: 60px;"></i></span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-9">
                                                            <h4 class="mb-1 fw-bold">{{ $kd->nama_lengkap }}</h4>
                                                            <p class="text-muted mb-3">Kepala SPPG Dapur</p>
                                                            <div class="row">
                                                                <div class="col-md-6 mb-2">
                                                                    <span
                                                                        class="fw-bold d-block text-muted small">NIK</span>
                                                                    <span>{{ $kd->nik_kepala_sppg ?? '-' }}</span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <span
                                                                        class="fw-bold d-block text-muted small">Email</span>
                                                                    <span>{{ $kd->userRole->user->email ?? '-' }}</span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <span
                                                                        class="fw-bold d-block text-muted small">WhatsApp</span>
                                                                    <span>{{ $kd->kontak_wa ?? '-' }}</span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <span
                                                                        class="fw-bold d-block text-muted small">Pendidikan
                                                                        Terakhir</span>
                                                                    <span>{{ $kd->pendidikan_terakhir ?? '-' }}</span>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <span
                                                                        class="fw-bold d-block text-muted small">Wilayah</span>
                                                                    <div class="mt-1">
                                                                        @if ($kd->province_name)
                                                                            <div class="d-flex mb-1"><span
                                                                                    class="text-muted me-2"
                                                                                    style="min-width:90px;">Provinsi</span><span>{{ $kd->province_name }}</span>
                                                                            </div>
                                                                        @endif
                                                                        @if ($kd->regency_name)
                                                                            <div class="d-flex mb-1"><span
                                                                                    class="text-muted me-2"
                                                                                    style="min-width:90px;">Kab/Kota</span><span>{{ $kd->regency_name }}</span>
                                                                            </div>
                                                                        @endif
                                                                        @if ($kd->district_name)
                                                                            <div class="d-flex mb-1"><span
                                                                                    class="text-muted me-2"
                                                                                    style="min-width:90px;">Kecamatan</span><span>{{ $kd->district_name }}</span>
                                                                            </div>
                                                                        @endif
                                                                        @if ($kd->village_name)
                                                                            <div class="d-flex mb-1"><span
                                                                                    class="text-muted me-2"
                                                                                    style="min-width:90px;">Kel/Desa</span><span>{{ $kd->village_name }}</span>
                                                                            </div>
                                                                        @endif
                                                                        @if ($kd->alamat_detail)
                                                                            <div class="d-flex mb-1"><span
                                                                                    class="text-muted me-2"
                                                                                    style="min-width:90px;">Alamat
                                                                                    Lengkap</span><span>{{ $kd->alamat_detail }}</span>
                                                                            </div>
                                                                        @endif
                                                                        @if (!$kd->province_name && !$kd->regency_name && !$kd->district_name && !$kd->village_name)
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">Foto Bangunan</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            @if ($dapur->foto_bangunan)
                                                <img src="{{ Storage::url($dapur->foto_bangunan) }}" alt="Foto Bangunan"
                                                    class="img-fluid rounded"
                                                    style="max-height: 220px; object-fit: cover;">
                                            @else
                                                <div class="py-5 bg-light rounded text-muted">
                                                    <i class="bx bx-image fs-1 mb-2"></i>
                                                    <p class="mb-0">Belum ada foto</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($dapur->tag_lokasi || $dapur->alamat)
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-header">
                                                <h5 class="mb-0"><i class="bx bx-map me-2"></i>Lokasi Dapur</h5>
                                            </div>
                                            <div class="card-body p-0">
                                                @php
                                                    $mapQuery = '';
                                                    $parsedQuery = false;
                                                    $embedUrl = null;

                                                    if ($dapur->tag_lokasi) {
                                                        $url = trim($dapur->tag_lokasi);

                                                        // Resolve shortened url to get the full URL with coordinates
                                                        if (str_contains($url, 'maps.app.goo.gl') || str_contains($url, 'goo.gl/maps')) {
                                                            $url = \Illuminate\Support\Facades\Cache::remember('resolved_maps_url_' . md5($url), 86400 * 30, function() use ($url) {
                                                                try {
                                                                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                                                                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                                                                    ])->withoutRedirecting()->head($url);
                                                                    
                                                                    if ($response->header('Location')) {
                                                                        return $response->header('Location');
                                                                    }
                                                                } catch (\Exception $e) {
                                                                    // Ignore
                                                                }
                                                                return $url;
                                                            });
                                                        }

                                                        // 1. Check if it's already an embed URL
                                                        if (str_contains($url, '/maps/embed') || str_contains($url, 'output=embed')) {
                                                            $embedUrl = $url;
                                                            $parsedQuery = true;
                                                        } 
                                                        // 2. Check for !3dLAT!4dLNG (actual pin point of google maps link)
                                                        elseif (preg_match('/!3d([-\d.]+)!4d([-\d.]+)/', $url, $matches)) {
                                                            $mapQuery = $matches[1] . ',' . $matches[2];
                                                            $parsedQuery = true;
                                                        }
                                                        // 3. Check for @lat,lng (view port / place location)
                                                        elseif (preg_match('/@([-\d.]+),([-\d.]+)/', $url, $matches)) {
                                                            $mapQuery = $matches[1] . ',' . $matches[2];
                                                            $parsedQuery = true;
                                                        }
                                                        // 4. Check for q=lat,lng or query=lat,lng
                                                        elseif (preg_match('/[?&](q|query)=([-\d.]+),([-\d.]+)/', $url, $matches)) {
                                                            $mapQuery = $matches[2] . ',' . $matches[3];
                                                            $parsedQuery = true;
                                                        }
                                                        // 5. Check if it is a raw coordinate pair like -6.123, 106.123
                                                        elseif (preg_match('/^[-\d.]+,\s*[\d.]+$/', $url)) {
                                                            $mapQuery = $url;
                                                            $parsedQuery = true;
                                                        }
                                                    }

                                                    if (!$parsedQuery) {
                                                        $mapQuery = $dapur->alamat;
                                                        if ($dapur->village_name) $mapQuery .= ', ' . $dapur->village_name;
                                                        if ($dapur->district_name) $mapQuery .= ', ' . $dapur->district_name;
                                                        if ($dapur->regency_name) $mapQuery .= ', ' . $dapur->regency_name;
                                                        if ($dapur->province_name) $mapQuery .= ', ' . $dapur->province_name;
                                                    }

                                                    if (!$embedUrl) {
                                                        $embedUrl = "https://maps.google.com/maps?q=" . urlencode($mapQuery) . "&t=&z=17&ie=UTF8&iwloc=&output=embed";
                                                    }
                                                @endphp
                                                <iframe src="{{ $embedUrl }}" width="100%" height="220"
                                                    style="border:0; display:block;" allowfullscreen="" loading="lazy"
                                                    referrerpolicy="no-referrer-when-downgrade">
                                                </iframe>
                                            </div>
                                            @if ($dapur->tag_lokasi)
                                                <div class="card-footer bg-transparent py-2">
                                                    <a href="{{ $dapur->tag_lokasi }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-map-alt me-1"></i> Buka di Google Maps
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">Status Kemitraan</h5>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex mb-3">
                                                    <div class="me-3">
                                                        <span class="badge bg-label-primary p-2"><i
                                                                class="bx bx-calendar"></i></span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">Tanggal Pengajuan</h6>
                                                        <small
                                                            class="text-muted">{{ $mitraDapur->created_at->format('d M Y, H:i') }}</small>
                                                    </div>
                                                </li>
                                                <li class="d-flex mb-3">
                                                    <div class="me-3">
                                                        <span class="badge bg-label-success p-2"><i
                                                                class="bx bx-check-circle"></i></span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">Tanggal Disetujui</h6>
                                                        <small
                                                            class="text-muted">{{ $mitraDapur->approved_at ? \Carbon\Carbon::parse($mitraDapur->approved_at)->format('d M Y, H:i') : '-' }}</small>
                                                    </div>
                                                </li>
                                                @if ($mitraDapur->catatan)
                                                    <li class="d-flex mb-3">
                                                        <div class="me-3">
                                                            <span class="badge bg-label-info p-2"><i
                                                                    class="bx bx-message-square-detail"></i></span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Catatan</h6>
                                                            <small class="text-muted">{{ $mitraDapur->catatan }}</small>
                                                        </div>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="tab-pane fade" id="navs-top-org" role="tabpanel">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Staf & Organisasi Dapur</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                                <input type="text" id="search-org" class="form-control"
                                                    placeholder="Cari nama, email, NIK, wilayah...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="filter-org" class="form-select">
                                                <option value="">Semua Peran</option>
                                                <option value="Kepala Dapur">Kepala Dapur</option>
                                                <option value="Ahli Gizi">Ahli Gizi</option>
                                                <option value="Admin Gudang">Admin Gudang</option>
                                                <option value="Akuntan">Akuntan</option>
                                                <option value="Tim Produksi">Tim Produksi</option>
                                                <option value="Distributor">Distributor</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama / User</th>
                                                    <th>Peran</th>
                                                    <th>NIK</th>
                                                    <th>Kontak WA</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-org">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="pagination-org"
                                        class="d-flex justify-content-between align-items-center mt-3 px-3">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="navs-top-stok" role="tabpanel">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Inventaris Stok Bahan Baku</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                                <input type="text" id="search-stok" class="form-control"
                                                    placeholder="Cari bahan baku...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="filter-stok" class="form-select">
                                                <option value="">Semua Kategori (Jenis Bahan)</option>
                                                <option value="Serealia dan olahannya">Serealia dan olahannya</option>
                                                <option value="Kacang dan olahannya">Kacang dan olahannya</option>
                                                <option value="Umbi dan sejenisnya">Umbi dan sejenisnya</option>
                                                <option value="Daging">Daging</option>
                                                <option value="Unggas">Unggas</option>
                                                <option value="Ikan dan seafood">Ikan dan seafood</option>
                                                <option value="Telur">Telur</option>
                                                <option value="Susu">Susu</option>
                                                <option value="Sayuran">Sayuran</option>
                                                <option value="Buah-buahan">Buah-buahan</option>
                                                <option value="Bumbu dan rempah">Bumbu dan rempah</option>
                                                <option value="Gula dan madu">Gula dan madu</option>
                                                <option value="Beras dan makanan pokok">Beras dan makanan pokok</option>
                                                <option value="Air mineral dan air kemasan">Air mineral dan air kemasan
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Bahan Baku</th>
                                                    <th>Jumlah Stok</th>
                                                    <th>Satuan</th>
                                                    <th>Terakhir Diperbarui</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-stok">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="pagination-stok"
                                        class="d-flex justify-content-between align-items-center mt-3 px-3">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="navs-top-menu" role="tabpanel">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Daftar Menu Makanan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                                <input type="text" id="search-menu" class="form-control"
                                                    placeholder="Cari menu...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="filter-menu" class="form-select">
                                                <option value="">Semua Kategori</option>
                                                <option value="Karbohidrat">Karbohidrat</option>
                                                <option value="Lauk">Lauk</option>
                                                <option value="Sayur">Sayur</option>
                                                <option value="Tambahan">Tambahan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Foto</th>
                                                    <th>Nama Menu</th>
                                                    <th>Kategori</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-menu">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="pagination-menu"
                                        class="d-flex justify-content-between align-items-center mt-3 px-3">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="navs-top-mbg" role="tabpanel">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">List Instansi & Penerima MBG</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                                <input type="text" id="search-mbg" class="form-control"
                                                    placeholder="Cari penanggung jawab, instansi, alamat...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="filter-mbg" class="form-select">
                                                <option value="">Semua Status Approval</option>
                                                <option value="approved">Disetujui</option>
                                                <option value="pending">Menunggu</option>
                                                <option value="rejected">Ditolak</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Instansi / PJ</th>
                                                    <th>Identitas</th>
                                                    <th class="text-end">Jumlah Sasaran Porsi</th>
                                                    <th>Kontak Akun</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-mbg">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="pagination-mbg"
                                        class="d-flex justify-content-between align-items-center mt-3 px-3">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="navs-top-fasilitas" role="tabpanel">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Kelengkapan Fasilitas & Prasarana Dapur</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $checkedPrasarana = $dapur->prasarana->pluck('id_item')->toArray();
                                    @endphp
                                    <div class="row">
                                        @foreach ($kategoriPrasarana as $kategori)
                                            <div class="col-md-4 mb-4">
                                                <div class="card h-100 shadow-none border">
                                                    <div class="card-header bg-lighter py-2">
                                                        <h6 class="mb-0">{{ $kategori->nama_kategori }}</h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        @foreach ($kategori->items as $item)
                                                            @php
                                                                $dp = $dapur->prasarana
                                                                    ->where('id_item', $item->id_item)
                                                                    ->first();
                                                                $isChecked = in_array(
                                                                    $item->id_item,
                                                                    $checkedPrasarana,
                                                                );
                                                            @endphp
                                                            <div
                                                                class="form-check mb-2 d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center">
                                                                    <input class="form-check-input me-2" type="checkbox"
                                                                        id="item_{{ $item->id_item }}"
                                                                        {{ $isChecked ? 'checked' : '' }}
                                                                        onclick="return false;">
                                                                    <label class="form-check-label"
                                                                        for="item_{{ $item->id_item }}">
                                                                        {{ $item->nama_item }}
                                                                    </label>
                                                                    @if ($dp)
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-icon text-primary ms-2"
                                                                            onclick="openDetailModal('{{ addslashes($item->nama_item) }}', `{{ $dp->keterangan }}`, {{ json_encode($dp->fotos) }})"
                                                                            title="Lihat Detail">
                                                                            <i class="bx bx-show"
                                                                                style="font-size: 16px;"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detailPrasaranaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title">
                            <i class="bx bx-info-circle me-2"></i> Detail <span id="detail_item_name"
                                class="fw-bold"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Informasi Keterangan</label>
                            <p id="detail_keterangan" class="form-control-plaintext bg-light p-3 rounded"
                                style="white-space: pre-wrap; min-height: 80px;"></p>
                        </div>
                        <div id="detail_fotos_container" class="mb-4" style="display: none;">
                            <label class="form-label fw-semibold d-block mb-3">Dokumentasi Foto</label>
                            <div class="d-flex flex-wrap gap-3" id="detail_fotos_list">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detailOrgModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title">
                            <i class="bx bx-user me-2"></i> Detail Staf
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img id="detail-org-foto" src="" alt="Foto Diri" class="rounded-circle img-fluid"
                                style="width: 120px; height: 120px; object-fit: cover; display: none;">
                            <div id="detail-org-avatar-placeholder" class="avatar avatar-xl mx-auto mb-2"
                                style="width: 120px; height: 120px; display: none;">
                                <span class="avatar-initial rounded-circle bg-label-primary fs-1"
                                    style="display: flex; align-items: center; justify-content: center;"><i
                                        class="bx bx-user"></i></span>
                            </div>
                        </div>
                        <h4 id="detail-org-nama" class="mb-1"></h4>
                        <p id="detail-org-role" class="badge bg-label-primary mb-3"></p>
                        <div class="text-start bg-light p-3 rounded">
                            <div class="row mb-2">
                                <span class="col-5 fw-bold">Email:</span>
                                <span id="detail-org-email" class="col-7"></span>
                            </div>
                            <div class="row mb-2">
                                <span class="col-5 fw-bold">NIK:</span>
                                <span id="detail-org-nik" class="col-7"></span>
                            </div>
                            <div class="row mb-2">
                                <span class="col-5 fw-bold">WhatsApp:</span>
                                <span id="detail-org-wa" class="col-7"></span>
                            </div>

                            <div class="border-top pt-2 mt-2">
                                <p class="fw-bold mb-1 text-muted small">Wilayah</p>
                                <div class="row mb-1">
                                    <span class="col-5 fw-bold">Provinsi:</span>
                                    <span id="detail-org-province" class="col-7"></span>
                                </div>
                                <div class="row mb-1">
                                    <span class="col-5 fw-bold">Kab/Kota:</span>
                                    <span id="detail-org-regency" class="col-7"></span>
                                </div>
                                <div class="row mb-1">
                                    <span class="col-5 fw-bold">Kecamatan:</span>
                                    <span id="detail-org-district" class="col-7"></span>
                                </div>
                                <div class="row mb-1">
                                    <span class="col-5 fw-bold">Kelurahan/Desa:</span>
                                    <span id="detail-org-village" class="col-7"></span>
                                </div>
                                <div class="row mb-1">
                                    <span class="col-5 fw-bold">Alamat Lengkap:</span>
                                    <span id="detail-org-alamat" class="col-7"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detailStockModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title">
                            <i class="bx bx-package me-2"></i> Detail Stok & Template Bahan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Bahan</label>
                                <input type="text" id="detail-stok-nama" class="form-control bg-transparent" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Satuan</label>
                                <input type="text" id="detail-stok-satuan" class="form-control bg-transparent"
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Stok Saat Ini</label>
                                <input type="text" id="detail-stok-jumlah" class="form-control bg-transparent"
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Terakhir Diperbarui</label>
                                <input type="text" id="detail-stok-updated" class="form-control bg-transparent"
                                    readonly>
                            </div>
                            <div class="col-12 border-top pt-3">
                                <h6 class="fw-semibold">Kandungan Gizi</h6>
                                <div class="row g-3" id="detail-stok-gizi-container">
                                </div>
                            </div>
                            <div class="col-12 border-top pt-3">
                                <h6 class="fw-semibold">Jenis Bahan Makanan</h6>
                                <div class="row g-3" id="detail-stok-jenis-container">
                                </div>
                            </div>
                            <div class="col-12 border-top pt-3">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <textarea id="detail-stok-keterangan" rows="3" class="form-control bg-transparent" readonly></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detailMenuModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title">
                            <i class="bx bx-food-menu me-2"></i> Detail Menu Makanan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <img id="detail-menu-foto" src="" alt="Foto Menu"
                                    class="img-fluid rounded border mb-3"
                                    style="max-height: 200px; object-fit: cover; width: 100%;">
                            </div>
                            <div class="col-md-8">
                                <h4 id="detail-menu-nama" class="mb-1"></h4>
                                <div class="d-flex gap-2 mb-3">
                                    <span id="detail-menu-kategori" class="badge"></span>
                                    <span id="detail-menu-status" class="badge"></span>
                                </div>
                                <p id="detail-menu-deskripsi" class="text-muted"></p>
                            </div>
                            <div class="col-12 border-top pt-3">
                                <h6 class="fw-semibold">Komposisi / Bahan Baku per Porsi</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Bahan Baku</th>
                                                <th class="text-end">Jumlah per Porsi</th>
                                                <th>Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail-menu-bahan-list">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detailMbgModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title">
                            <i class="bx bx-building me-2"></i> Detail Penerima MBG
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <img id="detail-mbg-foto" src="" alt="Foto Diri / Lokasi"
                                    class="img-fluid rounded border mb-3"
                                    style="max-height: 200px; object-fit: cover; width: 100%;">
                                <div id="detail-mbg-avatar-placeholder" class="avatar avatar-xl mx-auto mb-2"
                                    style="width: 120px; height: 120px; display: none;">
                                    <span class="avatar-initial rounded-circle bg-label-primary fs-1"
                                        style="display: flex; align-items: center; justify-content: center;"><i
                                            class="bx bx-building"></i></span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h4 id="detail-mbg-pj" class="mb-1"></h4>
                                <div class="d-flex gap-2 mb-3">
                                    <span id="detail-mbg-status" class="badge"></span>
                                </div>
                                <div class="bg-light p-3 rounded">
                                    <div class="row mb-2">
                                        <span class="col-5 fw-bold">Jenis Identitas:</span>
                                        <span id="detail-mbg-idtype" class="col-7"></span>
                                    </div>
                                    <div class="row mb-2">
                                        <span class="col-5 fw-bold">Nomor Identitas:</span>
                                        <span id="detail-mbg-idnumber" class="col-7"></span>
                                    </div>
                                    <div class="row mb-2">
                                        <span class="col-5 fw-bold">Jumlah Porsi Sasaran:</span>
                                        <span id="detail-mbg-porsi" class="col-7 text-primary fw-bold"></span>
                                    </div>
                                    <div class="row mb-2">
                                        <span class="col-5 fw-bold">Email Akun:</span>
                                        <span id="detail-mbg-email" class="col-7"></span>
                                    </div>
                                    <div class="row mb-2">
                                        <span class="col-5 fw-bold">Alamat:</span>
                                        <span id="detail-mbg-alamat" class="col-7"></span>
                                    </div>
                                    <div class="border-top pt-2 mt-1">
                                        <p class="fw-bold mb-1 text-muted small">Wilayah</p>
                                        <div class="row mb-1">
                                            <span class="col-5 fw-bold">Provinsi:</span>
                                            <span id="detail-mbg-province" class="col-7"></span>
                                        </div>
                                        <div class="row mb-1">
                                            <span class="col-5 fw-bold">Kab/Kota:</span>
                                            <span id="detail-mbg-regency" class="col-7"></span>
                                        </div>
                                        <div class="row mb-1">
                                            <span class="col-5 fw-bold">Kecamatan:</span>
                                            <span id="detail-mbg-district" class="col-7"></span>
                                        </div>
                                        <div class="row mb-1">
                                            <span class="col-5 fw-bold">Kelurahan/Desa:</span>
                                            <span id="detail-mbg-village" class="col-7"></span>
                                        </div>
                                    </div>
                                    <div id="detail-mbg-catatan-row" class="row mb-2" style="display:none;">
                                        <span class="col-5 fw-bold">Catatan Approval:</span>
                                        <span id="detail-mbg-catatan" class="col-7"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <div class="me-auto" id="detail-mbg-gmaps-container">
                        </div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const staffMembers = @json($staffMembers);
                const stockItems = @json($dapur->stockItems);
                const menus = @json($menus);
                const mbgList = @json($penerimaMbgList);

                const formatDate = (dateStr) => {
                    if (!dateStr) return '-';
                    const d = new Date(dateStr);
                    if (isNaN(d.getTime())) return dateStr;
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                        'Des'
                    ];
                    const pad = (n) => n.toString().padStart(2, '0');
                    return `${pad(d.getDate())} ${months[d.getMonth()]} ${d.getFullYear()}, ${pad(d.getHours())}:${pad(d.getMinutes())}`;
                };

                class ClientDataTable {
                    constructor(config) {
                        this.data = config.data;
                        this.tbody = document.getElementById(config.tbodyId);
                        this.pagination = document.getElementById(config.paginationId);
                        this.searchEl = document.getElementById(config.searchId);
                        this.filterEl = document.getElementById(config.filterId);
                        this.renderRow = config.renderRow;
                        this.matchSearch = config.matchSearch;
                        this.matchFilter = config.matchFilter;

                        this.currentPage = 1;
                        this.perPage = 10;

                        if (this.searchEl) {
                            this.searchEl.addEventListener('input', () => {
                                this.currentPage = 1;
                                this.render();
                            });
                        }

                        if (this.filterEl) {
                            this.filterEl.addEventListener('change', () => {
                                this.currentPage = 1;
                                this.render();
                            });
                        }

                        this.render();
                    }

                    getFilteredData() {
                        let filtered = this.data;

                        if (this.searchEl && this.searchEl.value) {
                            const query = this.searchEl.value.toLowerCase().trim();
                            filtered = filtered.filter(item => this.matchSearch(item, query));
                        }

                        if (this.filterEl && this.filterEl.value) {
                            const val = this.filterEl.value;
                            filtered = filtered.filter(item => this.matchFilter(item, val));
                        }

                        return filtered;
                    }

                    render() {
                        const filtered = this.getFilteredData();
                        const totalItems = filtered.length;
                        const totalPages = Math.ceil(totalItems / this.perPage) || 1;

                        if (this.currentPage > totalPages) {
                            this.currentPage = totalPages;
                        }

                        const start = (this.currentPage - 1) * this.perPage;
                        const end = start + this.perPage;
                        const pageData = filtered.slice(start, end);

                        this.tbody.innerHTML = '';
                        if (pageData.length === 0) {
                            const cols = this.tbody.closest('table').querySelector('thead tr').children.length;
                            this.tbody.innerHTML =
                                `<tr><td colspan="${cols}" class="text-center py-4 text-muted">Tidak ada data ditemukan.</td></tr>`;
                            this.pagination.innerHTML = '';
                            return;
                        }

                        pageData.forEach((item, index) => {
                            const rowNum = start + index + 1;
                            const tr = this.renderRow(item, rowNum);
                            this.tbody.appendChild(tr);
                        });

                        this.renderPagination(totalItems, totalPages);
                    }

                    renderPagination(totalItems, totalPages) {
                        if (!this.pagination) return;

                        const start = (this.currentPage - 1) * this.perPage + 1;
                        const end = Math.min(this.currentPage * this.perPage, totalItems);

                        let html =
                            `<div class="text-muted small">Menampilkan ${start} sampai ${end} dari ${totalItems} data</div>`;

                        if (totalPages > 1) {
                            html += `<ul class="pagination pagination-sm mb-0">`;

                            html += `<li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                        <button class="page-link" onclick="window['${this.tbody.id}'].setPage(${this.currentPage - 1})"><i class="bx bx-chevron-left"></i></button>
                    </li>`;

                            for (let i = 1; i <= totalPages; i++) {
                                html += `<li class="page-item ${this.currentPage === i ? 'active' : ''}">
                            <button class="page-link" onclick="window['${this.tbody.id}'].setPage(${i})">${i}</button>
                        </li>`;
                            }

                            html += `<li class="page-item ${this.currentPage === totalPages ? 'disabled' : ''}">
                        <button class="page-link" onclick="window['${this.tbody.id}'].setPage(${this.currentPage + 1})"><i class="bx bx-chevron-right"></i></button>
                    </li>`;

                            html += `</ul>`;
                        }

                        this.pagination.innerHTML = html;
                    }

                    setPage(page) {
                        this.currentPage = page;
                        this.render();
                    }
                }

                window['tbody-org'] = new ClientDataTable({
                    data: staffMembers,
                    tbodyId: 'tbody-org',
                    paginationId: 'pagination-org',
                    searchId: 'search-org',
                    filterId: 'filter-org',
                    matchSearch: (item, q) => {
                        return (item.nama && item.nama.toLowerCase().includes(q)) ||
                            (item.email && item.email.toLowerCase().includes(q)) ||
                            (item.nik && item.nik.toLowerCase().includes(q)) ||
                            (item.village_name && item.village_name.toLowerCase().includes(q)) ||
                            (item.district_name && item.district_name.toLowerCase().includes(q));
                    },
                    matchFilter: (item, val) => {
                        return item.role_filter === val;
                    },
                    renderRow: (item, num) => {
                        const tr = document.createElement('tr');
                        const avatarHtml = item.foto_diri ?
                            `<img src="${item.foto_diri}" alt="Foto" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">` :
                            `<span class="avatar-initial rounded-circle bg-label-primary" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"><i class="bx bx-user" style="font-size: 14px;"></i></span>`;
                        tr.innerHTML = `
                    <td>${num}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3" style="width: 32px; height: 32px;">
                                ${avatarHtml}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">${item.nama}</h6>
                                <small class="text-muted">${item.email || '-'}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-label-primary">${item.role}</span></td>
                    <td>${item.nik || '-'}</td>
                    <td>${item.wa || '-'}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-icon text-primary" onclick="showOrgDetail(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                            <i class="bx bx-show"></i>
                        </button>
                    </td>
                `;
                        return tr;
                    }
                });

                window['tbody-stok'] = new ClientDataTable({
                    data: stockItems,
                    tbodyId: 'tbody-stok',
                    paginationId: 'pagination-stok',
                    searchId: 'search-stok',
                    filterId: 'filter-stok',
                    matchSearch: (item, q) => {
                        return item.template_item && item.template_item.nama_bahan.toLowerCase().includes(
                            q);
                    },
                    matchFilter: (item, val) => {
                        return item.template_item && item.template_item.jenis_bahan && item.template_item
                            .jenis_bahan.includes(val);
                    },
                    renderRow: (item, num) => {
                        const tr = document.createElement('tr');
                        const nama = item.template_item ? item.template_item.nama_bahan : '-';
                        const satuan = item.template_item ? item.template_item.satuan : (item
                            .konversi_satuan || '-');
                        const jumlah = parseFloat(item.jumlah).toLocaleString('id-ID');
                        const updated = formatDate(item.updated_at);

                        tr.innerHTML = `
                    <td>${num}</td>
                    <td><strong>${nama}</strong></td>
                    <td class="fw-bold">${jumlah}</td>
                    <td>${satuan}</td>
                    <td>${updated}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-icon text-primary" onclick="showStokDetail(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                            <i class="bx bx-show"></i>
                        </button>
                    </td>
                `;
                        return tr;
                    }
                });

                window['tbody-menu'] = new ClientDataTable({
                    data: menus,
                    tbodyId: 'tbody-menu',
                    paginationId: 'pagination-menu',
                    searchId: 'search-menu',
                    filterId: 'filter-menu',
                    matchSearch: (item, q) => {
                        return (item.nama_menu && item.nama_menu.toLowerCase().includes(q)) ||
                            (item.deskripsi && item.deskripsi.toLowerCase().includes(q));
                    },
                    matchFilter: (item, val) => {
                        return item.kategori === val;
                    },
                    renderRow: (item, num) => {
                        const tr = document.createElement('tr');
                        const foto = item.gambar_url || '/images/menu/default-menu.jpg';
                        const statusBadge = item.is_active ?
                            `<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i> Aktif</span>` :
                            `<span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i> Tidak Aktif</span>`;

                        tr.innerHTML = `
                    <td>${num}</td>
                    <td>
                        <img src="${foto}" alt="${item.nama_menu}" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                    </td>
                    <td><strong>${item.nama_menu}</strong></td>
                    <td><span class="badge bg-label-info">${item.kategori}</span></td>
                    <td>${statusBadge}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-icon text-primary" onclick="showMenuDetail(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                            <i class="bx bx-show"></i>
                        </button>
                    </td>
                `;
                        return tr;
                    }
                });

                window['tbody-mbg'] = new ClientDataTable({
                    data: mbgList,
                    tbodyId: 'tbody-mbg',
                    paginationId: 'pagination-mbg',
                    searchId: 'search-mbg',
                    filterId: 'filter-mbg',
                    matchSearch: (item, q) => {
                        return (item.penanggung_jawab && item.penanggung_jawab.toLowerCase().includes(q)) ||
                            (item.alamat_detail && item.alamat_detail.toLowerCase().includes(q)) ||
                            (item.id_number && item.id_number.toLowerCase().includes(q));
                    },
                    matchFilter: (item, val) => {
                        return item.status_approval === val;
                    },
                    renderRow: (item, num) => {
                        const tr = document.createElement('tr');
                        const foto = item.foto_diri ? `/storage/${item.foto_diri.replace('storage/', '')}` :
                            null;
                        const avatarHtml = foto ?
                            `<img src="${foto}" alt="Foto" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">` :
                            `<span class="avatar-initial rounded-circle bg-label-primary" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"><i class="bx bx-building" style="font-size: 14px;"></i></span>`;

                        const email = (item.user_role && item.user_role.user) ? item.user_role.user.email :
                            '-';

                        const types = {
                            'nik': 'NIK',
                            'nisn': 'NISN',
                            'no_registrasi': 'No. Registrasi'
                        };
                        const idTypeLabel = types[item.id_type] || (item.id_type ? item.id_type
                            .toUpperCase() : '-');

                        tr.innerHTML = `
                    <td>${num}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3" style="width: 32px; height: 32px;">
                                ${avatarHtml}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">${item.penanggung_jawab}</h6>
                                <small class="text-muted">${item.alamat_detail || '-'}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="fw-bold">${idTypeLabel}</span>: ${item.id_number || '-'}</td>
                    <td class="text-end fw-bold text-primary">${parseInt(item.jumlah_porsi).toLocaleString('id-ID')} Porsi</td>
                    <td>${email}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-icon text-primary" onclick="showMbgDetail(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                            <i class="bx bx-show"></i>
                        </button>
                    </td>
                `;
                        return tr;
                    }
                });

                window.showOrgDetail = function(item) {
                    document.getElementById('detail-org-nama').innerText = item.nama;
                    document.getElementById('detail-org-role').innerText = item.role;
                    document.getElementById('detail-org-email').innerText = item.email;
                    document.getElementById('detail-org-nik').innerText = item.nik;
                    document.getElementById('detail-org-wa').innerText = item.wa;

                    document.getElementById('detail-org-province').innerText = item.province_name || '-';
                    document.getElementById('detail-org-regency').innerText = item.regency_name || '-';
                    document.getElementById('detail-org-district').innerText = item.district_name || '-';
                    document.getElementById('detail-org-village').innerText = item.village_name || '-';
                    document.getElementById('detail-org-alamat').innerText = item.alamat_detail || '-';

                    const fotoImg = document.getElementById('detail-org-foto');
                    const placeholder = document.getElementById('detail-org-avatar-placeholder');

                    if (item.foto_diri) {
                        fotoImg.src = item.foto_diri;
                        fotoImg.style.display = 'inline-block';
                        placeholder.style.display = 'none';
                    } else {
                        fotoImg.style.display = 'none';
                        placeholder.style.display = 'block';
                    }

                    new bootstrap.Modal(document.getElementById('detailOrgModal')).show();
                };

                window.showStokDetail = function(item) {
                    const template = item.template_item || {};
                    document.getElementById('detail-stok-nama').value = template.nama_bahan || '-';
                    document.getElementById('detail-stok-satuan').value = template.satuan || (item
                        .konversi_satuan || '-');
                    document.getElementById('detail-stok-jumlah').value = parseFloat(item.jumlah).toLocaleString(
                        'id-ID');
                    document.getElementById('detail-stok-updated').value = formatDate(item.updated_at);
                    document.getElementById('detail-stok-keterangan').value = template.keterangan || '-';

                    const giziEnum = ['Protein', 'Karbohidrat', 'Lemak', 'Vitamin', 'Omega', 'Mineral'];
                    const giziContainer = document.getElementById('detail-stok-gizi-container');
                    giziContainer.innerHTML = '';

                    const savedGizi = template.kandungan_gizi || [];
                    giziEnum.forEach(gizi => {
                        const checked = savedGizi.includes(gizi) ? 'checked' : '';
                        giziContainer.innerHTML += `
                    <div class="col-md-4 col-sm-6">
                        <div class="form-check custom-option custom-option-basic">
                            <label class="form-check-label custom-option-content" style="pointer-events: none;">
                                <input class="form-check-input" type="checkbox" value="${gizi}" ${checked} onclick="return false;" />
                                <span class="custom-option-header">
                                    <span class="h6 mb-0">${gizi}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                `;
                    });

                    const jenisEnum = [
                        'Serealia dan olahannya', 'Kacang dan olahannya', 'Umbi dan sejenisnya',
                        'Daging', 'Unggas', 'Ikan dan seafood', 'Telur', 'Susu', 'Sayuran',
                        'Buah-buahan', 'Bumbu dan rempah', 'Gula dan madu', 'Beras dan makanan pokok',
                        'Air mineral dan air kemasan'
                    ];
                    const jenisContainer = document.getElementById('detail-stok-jenis-container');
                    jenisContainer.innerHTML = '';

                    const savedJenis = template.jenis_bahan || [];
                    jenisEnum.forEach(jenis => {
                        const checked = savedJenis.includes(jenis) ? 'checked' : '';
                        jenisContainer.innerHTML += `
                    <div class="col-md-4 col-sm-6">
                        <div class="form-check custom-option custom-option-basic">
                            <label class="form-check-label custom-option-content" style="pointer-events: none;">
                                <input class="form-check-input" type="checkbox" value="${jenis}" ${checked} onclick="return false;" />
                                <span class="custom-option-header">
                                    <span class="h6 mb-0">${jenis}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                `;
                    });

                    new bootstrap.Modal(document.getElementById('detailStockModal')).show();
                };

                window.showMenuDetail = function(item) {
                    document.getElementById('detail-menu-nama').innerText = item.nama_menu || '-';
                    document.getElementById('detail-menu-deskripsi').innerText = item.deskripsi ||
                        'Tidak ada deskripsi.';

                    const katBadge = document.getElementById('detail-menu-kategori');
                    katBadge.innerText = item.kategori || '-';
                    katBadge.className = 'badge';

                    if (item.kategori === 'Karbohidrat') katBadge.classList.add('bg-label-primary');
                    else if (item.kategori === 'Lauk') katBadge.classList.add('bg-label-success');
                    else if (item.kategori === 'Sayur') katBadge.classList.add('bg-label-info');
                    else if (item.kategori === 'Tambahan') katBadge.classList.add('bg-label-warning');
                    else katBadge.classList.add('bg-label-secondary');

                    const statusBadge = document.getElementById('detail-menu-status');
                    statusBadge.className = 'badge';
                    if (item.is_active) {
                        statusBadge.innerText = 'Aktif';
                        statusBadge.classList.add('bg-label-success');
                    } else {
                        statusBadge.innerText = 'Tidak Aktif';
                        statusBadge.classList.add('bg-label-danger');
                    }

                    document.getElementById('detail-menu-foto').src = item.gambar_url ||
                        '/images/menu/default-menu.jpg';

                    const bahanTbody = document.getElementById('detail-menu-bahan-list');
                    bahanTbody.innerHTML = '';

                    const bahanMenu = item.bahan_menu || [];
                    if (bahanMenu.length === 0) {
                        bahanTbody.innerHTML =
                            '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada komposisi bahan.</td></tr>';
                    } else {
                        bahanMenu.forEach(bahan => {
                            const template = bahan.template_item || {};
                            bahanTbody.innerHTML += `
                        <tr>
                            <td>${template.nama_bahan || '-'}</td>
                            <td class="text-end fw-bold">${parseFloat(bahan.jumlah_per_porsi).toLocaleString('id-ID')}</td>
                            <td>${template.satuan || '-'}</td>
                        </tr>
                    `;
                        });
                    }

                    new bootstrap.Modal(document.getElementById('detailMenuModal')).show();
                };

                window.showMbgDetail = function(item) {
                    document.getElementById('detail-mbg-pj').innerText = item.penanggung_jawab || '-';
                    document.getElementById('detail-mbg-idnumber').innerText = item.id_number || '-';
                    document.getElementById('detail-mbg-porsi').innerText =
                        `${parseInt(item.jumlah_porsi).toLocaleString('id-ID')} Porsi`;
                    document.getElementById('detail-mbg-alamat').innerText = item.alamat_detail || '-';

                    const email = (item.user_role && item.user_role.user) ? item.user_role.user.email : '-';
                    document.getElementById('detail-mbg-email').innerText = email;

                    const types = {
                        'nik': 'NIK',
                        'nisn': 'NISN',
                        'no_registrasi': 'No. Registrasi'
                    };
                    document.getElementById('detail-mbg-idtype').innerText = types[item.id_type] || (item.id_type ?
                        item.id_type.toUpperCase() : '-');

                    document.getElementById('detail-mbg-province').innerText = item.province_name || '-';
                    document.getElementById('detail-mbg-regency').innerText = item.regency_name || '-';
                    document.getElementById('detail-mbg-district').innerText = item.district_name || '-';
                    document.getElementById('detail-mbg-village').innerText = item.village_name || '-';

                    const statusBadge = document.getElementById('detail-mbg-status');
                    statusBadge.className = 'badge';
                    if (item.status_approval === 'approved') {
                        statusBadge.innerText = 'Disetujui';
                        statusBadge.classList.add('bg-label-success');
                    } else if (item.status_approval === 'pending') {
                        statusBadge.innerText = 'Menunggu';
                        statusBadge.classList.add('bg-label-warning');
                    } else {
                        statusBadge.innerText = 'Ditolak';
                        statusBadge.classList.add('bg-label-danger');
                    }

                    const catatanRow = document.getElementById('detail-mbg-catatan-row');
                    if (item.catatan_approval) {
                        document.getElementById('detail-mbg-catatan').innerText = item.catatan_approval;
                        catatanRow.style.display = 'flex';
                    } else {
                        catatanRow.style.display = 'none';
                    }

                    const fotoImg = document.getElementById('detail-mbg-foto');
                    const placeholder = document.getElementById('detail-mbg-avatar-placeholder');
                    const fotoUrl = item.foto_diri ? `/storage/${item.foto_diri.replace('storage/', '')}` : (item
                        .foto_lokasi ? `/storage/${item.foto_lokasi.replace('storage/', '')}` : null);

                    if (fotoUrl) {
                        fotoImg.src = fotoUrl;
                        fotoImg.style.display = 'inline-block';
                        placeholder.style.display = 'none';
                    } else {
                        fotoImg.style.display = 'none';
                        placeholder.style.display = 'block';
                    }

                    const gmapsContainer = document.getElementById('detail-mbg-gmaps-container');
                    gmapsContainer.innerHTML = '';
                    if (item.link_gmaps) {
                        gmapsContainer.innerHTML =
                            `<a href="${item.link_gmaps}" target="_blank" class="btn btn-xs btn-outline-primary"><i class="bx bx-map-alt me-1"></i> Buka GMaps</a>`;
                    }

                    new bootstrap.Modal(document.getElementById('detailMbgModal')).show();
                };
            });

            window.openDetailModal = function(itemName, keterangan, fotos) {
                document.getElementById('detail_item_name').innerText = itemName;
                document.getElementById('detail_keterangan').innerText = keterangan || 'Tidak ada keterangan.';

                const fotosContainer = document.getElementById('detail_fotos_container');
                const fotosList = document.getElementById('detail_fotos_list');
                fotosList.innerHTML = '';

                if (fotos && fotos.length > 0) {
                    fotosContainer.style.display = 'block';
                    fotos.forEach(foto => {
                        fotosList.innerHTML += `
                    <div style="width: 120px; height: 120px;">
                        <a href="/${foto.foto_url}" target="_blank">
                            <img src="/${foto.foto_url}" class="rounded shadow-sm w-100 h-100" style="object-fit: cover; border: 1px solid #e1e3ea;">
                        </a>
                    </div>
                `;
                    });
                } else {
                    fotosContainer.style.display = 'none';
                }

                new bootstrap.Modal(document.getElementById('detailPrasaranaModal')).show();
            };
        </script>
    @endpush
@endsection
