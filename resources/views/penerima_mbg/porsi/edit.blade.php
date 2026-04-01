@extends('template_penerima_mbg.layout')
@section('title', 'Jumlah Porsi MBG')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Pengaturan Jumlah Porsi MBG</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($penerima->status_approval !== 'approved')
                <div class="alert alert-warning">
                    <i class="bx bx-info-circle me-1"></i>
                    Pengajuan Anda belum disetujui. Perubahan jumlah porsi hanya dapat dilakukan setelah disetujui oleh Dapur SPPG.
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="text-center mb-4">
                        <i class="bx bx-restaurant mb-2" style="font-size: 4rem; color: #696cff;"></i>
                        <h2 class="fw-bold">{{ $penerima->jumlah_porsi }}</h2>
                        <p class="text-muted">Porsi MBG Saat Ini</p>
                    </div>

                    <form action="{{ route('penerima-mbg.porsi.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="jumlah_porsi" class="form-label fw-semibold">Jumlah Porsi Baru <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_porsi" id="jumlah_porsi"
                                class="form-control form-control-lg text-center @error('jumlah_porsi') is-invalid @enderror"
                                value="{{ old('jumlah_porsi', $penerima->jumlah_porsi) }}"
                                min="1" max="1000000" required
                                {{ $penerima->status_approval !== 'approved' ? 'disabled' : '' }}>
                            @error('jumlah_porsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text text-center">Jumlah porsi yang Anda butuhkan per hari (1–1.000.000 porsi)</div>
                        </div>
                        <div class="d-grid gap-2">
                            @if($penerima->status_approval === 'approved')
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bx bx-save me-1"></i> Perbarui Jumlah Porsi
                                </button>
                            @endif
                            <a href="{{ route('penerima-mbg.dashboard') }}" class="btn btn-label-secondary">Kembali ke Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
