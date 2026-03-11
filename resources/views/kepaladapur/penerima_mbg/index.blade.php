@extends('template_kepala_dapur.layout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Daftar Penerima MBG</h4>
            <p class="text-muted mb-0">Kelola pengajuan penerima MBG untuk dapur {{ $dapur->nama_dapur }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-3 mb-4">
        @php
            $filterStatus = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
            $filterLabel  = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
        @endphp
        @foreach($filterStatus as $key => $color)
            <div class="col-md-4">
                <a href="{{ route('kepala-dapur.penerima-mbg.index', ['dapur' => $dapur, 'status' => $key]) }}" class="text-decoration-none">
                    <div class="card {{ $status === $key ? 'border-'.$color.' border-2' : '' }}">
                        <div class="card-body d-flex align-items-center gap-3">
                            <span class="badge bg-label-{{ $color }} p-2 fs-5">{{ $counts[$key] }}</span>
                            <div>
                                <h6 class="mb-0">{{ $filterLabel[$key] }}</h6>
                                <small class="text-muted">Pengajuan</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="mb-3 d-flex gap-2">
        @foreach(array_merge(['semua' => 'secondary'], $filterStatus) as $key => $color)
            <a href="{{ route('kepala-dapur.penerima-mbg.index', ['dapur' => $dapur, 'status' => $key]) }}"
               class="btn btn-{{ $status === $key ? $color : 'outline-'.$color }} btn-sm">
                {{ ucfirst($key === 'semua' ? 'Semua' : ($key === 'pending' ? 'Menunggu' : ($key === 'approved' ? 'Disetujui' : 'Ditolak'))) }}
            </a>
        @endforeach
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Identitas</th>
                        <th>Alamat</th>
                        <th>Porsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penerima as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold">{{ $p->userRole?->user?->nama }}</div>
                            <small class="text-muted">{{ $p->penanggung_jawab }}</small>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary">{{ strtoupper($p->id_type) }}</span><br>
                            <small>{{ $p->id_number }}</small>
                        </td>
                        <td>
                            <small>{{ $p->village_name ? $p->village_name.', ' : '' }}{{ $p->district_name }}</small>
                        </td>
                        <td><span class="badge bg-label-primary">{{ $p->jumlah_porsi }} Porsi</span></td>
                        <td>
                            @php  $col = match($p->status_approval) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }; @endphp
                            <span class="badge bg-label-{{ $col }}">{{ ucfirst($p->status_approval) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('kepala-dapur.penerima-mbg.show', ['dapur' => $dapur, 'penerima_mbg' => $p->id_penerima]) }}" class="btn btn-sm btn-icon btn-outline-info">
                                <i class="bx bx-show"></i>
                            </a>
                            @if($p->status_approval === 'pending')
                                <form action="{{ route('kepala-dapur.penerima-mbg.approve', ['dapur' => $dapur, 'penerima_mbg' => $p->id_penerima]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-success" title="Setujui">
                                        <i class="bx bx-check"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger" title="Tolak"
                                    onclick="showRejectModal({{ $p->id_penerima }})">
                                    <i class="bx bx-x"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data penerima MBG dengan status ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $penerima->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="catatan_approval" class="form-control" rows="3" required placeholder="Berikan alasan penolakan..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function showRejectModal(id) {
    const url = `{{ route('kepala-dapur.penerima-mbg.reject', ['dapur' => $dapur, 'penerima_mbg' => ':id']) }}`.replace(':id', id);
    document.getElementById('rejectForm').action = url;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
