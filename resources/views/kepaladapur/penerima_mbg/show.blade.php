@extends('template_kepala_dapur.layout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav class="d-flex align-items-center mb-4">
        <a href="{{ route('kepala-dapur.penerima-mbg.index', $dapur) }}" class="text-muted me-2">
            <i class="bx bx-arrow-back me-1"></i> Daftar Penerima MBG
        </a>
        <i class="bx bx-chevron-right me-2"></i>
        <span class="text-dark">Detail Penerima</span>
    </nav>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <h5 class="card-title mb-0">Detail Penerima MBG</h5>
                @php $col = match($penerima_mbg->status_approval) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }; @endphp
                <span class="badge bg-label-{{ $col }} fs-6">{{ ucfirst($penerima_mbg->status_approval) }}</span>
            </div>

            <div class="row g-4">
                <div class="col-md-3 text-center">
                    @if($penerima_mbg->foto_lokasi)
                        <img src="{{ Storage::url($penerima_mbg->foto_lokasi) }}" class="img-fluid rounded" alt="Foto Lokasi" style="max-height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                            <i class="bx bx-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <p class="text-muted mt-2 small">Foto Lokasi</p>
                </div>
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Nama:</strong> {{ $penerima_mbg->userRole?->user?->nama }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $penerima_mbg->userRole?->user?->email }}</p>
                            <p class="mb-1"><strong>Penanggung Jawab:</strong> {{ $penerima_mbg->penanggung_jawab }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Identitas:</strong> <span class="badge bg-secondary">{{ strtoupper($penerima_mbg->id_type) }}</span> {{ $penerima_mbg->id_number }}</p>
                            <p class="mb-1"><strong>Porsi MBG:</strong> <span class="badge bg-label-primary fs-6">{{ $penerima_mbg->jumlah_porsi }} Porsi</span></p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1"><strong>Alamat Lengkap:</strong></p>
                            <p class="text-muted">
                                {{ $penerima_mbg->alamat_detail }}<br>
                                @if($penerima_mbg->village_name) Kel. {{ $penerima_mbg->village_name }}, @endif
                                @if($penerima_mbg->district_name) Kec. {{ $penerima_mbg->district_name }}, @endif
                                {{ $penerima_mbg->regency_name }}, Prov. {{ $penerima_mbg->province_name }}
                            </p>
                        </div>
                        @if($penerima_mbg->latitude && $penerima_mbg->longitude)
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Koordinat:</strong> {{ $penerima_mbg->latitude }}, {{ $penerima_mbg->longitude }}</p>
                        </div>
                        @endif
                        @if($penerima_mbg->link_gmaps)
                        <div class="col-md-6">
                            <a href="{{ $penerima_mbg->link_gmaps }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bx bx-map me-1"></i> Buka Google Maps
                            </a>
                        </div>
                        @endif
                        @if($penerima_mbg->catatan_approval)
                        <div class="col-12">
                            <p class="mb-1"><strong>Catatan:</strong> {{ $penerima_mbg->catatan_approval }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($penerima_mbg->status_approval === 'pending')
    <div class="d-flex gap-2">
        <form action="{{ route('kepala-dapur.penerima-mbg.approve', ['dapur' => $dapur, 'penerima_mbg' => $penerima_mbg->id_penerima]) }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-success"><i class="bx bx-check me-1"></i> Setujui Pengajuan</button>
        </form>
        <button type="button" class="btn btn-danger" onclick="showRejectModal({{ $penerima_mbg->id_penerima }})">
            <i class="bx bx-x me-1"></i> Tolak Pengajuan
        </button>
        <a href="{{ route('kepala-dapur.penerima-mbg.index', $dapur) }}" class="btn btn-label-secondary">Kembali</a>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="rejectForm" action="{{ route('kepala-dapur.penerima-mbg.reject', ['dapur' => $dapur, 'penerima_mbg' => $penerima_mbg->id_penerima]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_approval" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
    function showRejectModal(id) { new bootstrap.Modal(document.getElementById('rejectModal')).show(); }
    </script>
    @endpush
    @else
    <a href="{{ route('kepala-dapur.penerima-mbg.index', $dapur) }}" class="btn btn-label-secondary">
        <i class="bx bx-arrow-back me-1"></i> Kembali
    </a>
    @endif
</div>
@endsection
