@extends('template_admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            {{-- <div class="avatar avatar-md me-3">
                                <img src="{{ asset('storage/profil/' . $profil->logo_perusahaan) }}" alt="Logo Kecamatan" class="rounded-circle">
                            </div> --}}
                            <div>
                                <h4 class="mb-1">Dashboard</h4>
                                <p class="mb-0 text-muted">
                                    <i class="bx bx-map-pin me-1"></i>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Utama -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">Jumlah Artikel</h6>
                                <h4 class="mb-0">{{ number_format($artikel ?? 0) }}</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-group"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">Jumlah Layanan</h6>
                                <h4 class="mb-0">{{ number_format($layanan ?? 0) }}</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-building"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">Jumlah Produk</h6>
                                <h4 class="mb-0">{{ number_format($produk ?? 0) }}</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-home"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">Jumlah Testimoni</h6>
                                <h4 class="mb-0">{{ number_format($testimoni ?? 0) }}</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-phone"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Tim -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Statistik Tim</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="bx bx-book"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Jumlah Tim</h6>
                                        <small class="text-muted">{{ $tim ?? 0 }} Tim</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="bx bx-book-open"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Jumlah Kontak</h6>
                                        <small class="text-muted">{{ $kontak ?? 0 }} Kontak</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="bx bx-book-content"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Jumlah Testimoni</h6>
                                        <small class="text-muted">{{ $testimoni ?? 0 }} Testimoni</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="bx bx-bookmark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Jumlah Kontak</h6>
                                        <small class="text-muted">{{ $kontak ?? 0 }} Kontak</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
