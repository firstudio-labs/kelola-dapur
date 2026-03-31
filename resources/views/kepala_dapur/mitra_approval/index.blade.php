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
                        <span class="text-dark">Approval Mitra</span>
                    </nav>
                    <h4 class="mb-1">Approval Mitra</h4>
                    <p class="mb-0 text-muted">Mengelola mitra yang mengajukan akses ke dapur Anda</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Pemilik</th>
                            <th width="20%">NIK</th>
                            <th width="20%">Tanggal Pengajuan</th>
                            <th width="15%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($mitraDapurList as $index => $md)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $md->mitra->nama_pemilik }}</strong><br>
                                    <small class="text-muted">{{ $md->mitra->user->email ?? '-' }}</small>
                                </td>
                                <td>{{ $md->mitra->nik_pemilik }}</td>
                                <td><small class="text-muted">{{ $md->created_at->format('d M Y, H:i') }}</small></td>
                                <td>
                                    <span class="badge {{ $md->status_badge_class }}">
                                        {{ $md->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('kepala-dapur.mitra-approval.show', ['dapur' => $md->id_dapur, 'mitraDapur' => $md->id]) }}" 
                                       class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada pengajuan mitra.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
