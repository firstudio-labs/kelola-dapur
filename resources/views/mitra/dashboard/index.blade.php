@extends('template_mitra.layout')
@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Welcome Card --}}
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Selamat Datang, {{ auth()->user()->nama }}! 🎉</h5>
                            <p class="mb-4">
                                Senang melihat Anda kembali. Di sini Anda dapat memantau status pengajuan dapur dan aktivitas terbaru Anda sebagai Mitra Pembagian.
                            </p>
                            <a href="{{ route('mitra.dapur.index') }}" class="btn btn-sm btn-outline-primary">Kelola Dapur</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('admin/assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-buildings"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Dapur</span>
                    <h3 class="card-title mb-2">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Semua pengajuan</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-circle"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Disetujui</span>
                    <h3 class="card-title mb-2 text-success">{{ $stats['approved'] }}</h3>
                    <small class="text-muted">Siap beraktivitas</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time-five"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Pending</span>
                    <h3 class="card-title mb-2 text-warning">{{ $stats['pending'] }}</h3>
                    <small class="text-muted">Menunggu approval</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-x-circle"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Ditolak</span>
                    <h3 class="card-title mb-2 text-danger">{{ $stats['rejected'] }}</h3>
                    <small class="text-muted">Butuh perbaikan</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">Aktivitas Dapur Terbaru</h5>
                    <a href="{{ route('mitra.dapur.index') }}" class="btn btn-sm btn-label-primary">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Dapur</th>
                                <th>Wilayah</th>
                                <th>Status</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $activity)
                            <tr>
                                <td><strong>{{ $activity->dapur->nama_dapur }}</strong></td>
                                <td>{{ $activity->dapur->province_name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $activity->status_badge_class }}">
                                        {{ $activity->status_label }}
                                    </span>
                                </td>
                                <td>{{ $activity->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Belum ada aktivitas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
