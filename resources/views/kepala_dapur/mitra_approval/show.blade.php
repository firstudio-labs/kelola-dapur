@extends('template_kepala_dapur.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('dashboard') }}" class="text-muted me-2">
                            <i class="bx bx-home-alt me-1"></i>
                            Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <a href="{{ route('kepala-dapur.mitra-approval.index', ['dapur' => $mitraDapur->id_dapur]) }}" class="text-muted me-2">
                            Approval Mitra
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <span class="text-dark">Detail Pengajuan</span>
                    </nav>
                    <h4 class="mb-1">Detail Pengajuan Mitra</h4>
                    <p class="mb-0 text-muted">Informasi lengkap mengenai mitra dan pengajuannya</p>
                </div>
                <div>
                    <a href="{{ route('kepala-dapur.mitra-approval.index', ['dapur' => $mitraDapur->id_dapur]) }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <h5 class="card-header border-bottom">Informasi Mitra</h5>
                <div class="card-body mt-4">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Nama Akun User:</div>
                        <div class="col-sm-8 fw-semibold">{{ $mitraDapur->mitra->user->nama ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Username:</div>
                        <div class="col-sm-8">{{ $mitraDapur->mitra->user->username ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Email:</div>
                        <div class="col-sm-8">{{ $mitraDapur->mitra->user->email ?? '-' }}</div>
                    </div>
                    <div class="divider">
                        <div class="divider-text">Detail Pemilik</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Nama Pemilik:</div>
                        <div class="col-sm-8 h6 mb-0">{{ $mitraDapur->mitra->nama_pemilik }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">NIK Pemilik:</div>
                        <div class="col-sm-8">{{ $mitraDapur->mitra->nik_pemilik }}</div>
                    </div>
                    <div class="divider">
                        <div class="divider-text">Alamat & Lokasi</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Wilayah:</div>
                        <div class="col-sm-8">{{ $mitraDapur->mitra->full_wilayah }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Detail Alamat:</div>
                        <div class="col-sm-8">{{ $mitraDapur->mitra->alamat_detail }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <h5 class="card-header border-bottom">Aksi Keputusan</h5>
                <div class="card-body mt-4 text-center">
                    
                    <div class="mb-4">
                        <span class="d-block text-muted mb-2">Status Saat Ini</span>
                        <span class="badge {{ $mitraDapur->status_badge_class }} fs-6 px-3 py-2">
                            {{ $mitraDapur->status_label }}
                        </span>
                    </div>

                    @if($mitraDapur->status === 'pending')
                        <div class="d-inline-block w-100 mb-2">
                            <button class="btn btn-success w-100" type="button" data-bs-toggle="modal" data-bs-target="#modalApprove">
                                <i class="bx mx-1 bx-check-circle"></i> Setujui Pengajuan
                            </button>
                        </div>

                        <button class="btn btn-danger w-100" type="button" data-bs-toggle="modal" data-bs-target="#modalReject">
                            <i class="bx mx-1 bx-x-circle"></i> Tolak Pengajuan
                        </button>
                    @else
                        @if($mitraDapur->catatan)
                            <div class="mt-3 text-start bg-lighter p-3 rounded">
                                <small class="fw-bold d-block mb-1">Catatan Kepala Dapur:</small>
                                <span class="small">{{ $mitraDapur->catatan }}</span>
                            </div>
                        @else
                            <div class="text-muted small mt-3">Tidak ada catatan tambahan.</div>
                        @endif
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>

@if($mitraDapur->status === 'pending')
    <!-- Modal Approve -->
    <div class="modal fade" id="modalApprove" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Setujui Mitra Pembagian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kepala-dapur.mitra-approval.approve', ['dapur' => $mitraDapur->id_dapur, 'mitraDapur' => $mitraDapur->id]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="catatanApprove" class="form-label">Catatan (Opsional)</label>
                                <textarea id="catatanApprove" name="catatan" class="form-control" placeholder="Tuliskan catatan atau instruksi tambahan..." rows="3"></textarea>
                            </div>
                        </div>
                        <div class="alert alert-warning mb-0">
                            <strong>Peringatan!</strong> Mitra yang disetujui akan dapat melihat dan melakukan aktivitas yang terkait dengan dapur Anda.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Ya, Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Tolak Pengajuan Mitra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kepala-dapur.mitra-approval.reject', ['dapur' => $mitraDapur->id_dapur, 'mitraDapur' => $mitraDapur->id]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="catatanReject" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea id="catatanReject" name="catatan" class="form-control" placeholder="Tuliskan alasan penolakan..." rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection
