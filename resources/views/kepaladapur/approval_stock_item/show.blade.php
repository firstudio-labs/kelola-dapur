@extends("template_kepala_dapur.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Approval Stok /</span>
            Detail Permintaan
        </h4>

        <!-- Alert Messages -->
        @if (session("success"))
            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >
                {{ session("success") }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif

        @if (session("error"))
            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
            >
                {{ session("error") }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="card-title mb-0">Detail Permintaan Stok</h5>
                        <div class="d-flex gap-2">
                            @if ($approval->isPending())
                                <button
                                    type="button"
                                    class="btn btn-success btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveModal"
                                >
                                    <i class="bx bx-check me-1"></i>
                                    Setujui
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal"
                                >
                                    <i class="bx bx-x me-1"></i>
                                    Tolak
                                </button>
                            @endif

                            <a
                                href="{{ route("kepala-dapur.approvals.index", $dapur) }}"
                                class="btn btn-outline-secondary btn-sm"
                            >
                                <i class="bx bx-arrow-back me-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Informasi Permintaan -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            Informasi Permintaan
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td>
                                                    <strong>
                                                        ID Permintaan:
                                                    </strong>
                                                </td>
                                                <td>
                                                    #{{ $approval->id_approval_stock_item }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>
                                                        Tanggal Permintaan:
                                                    </strong>
                                                </td>
                                                <td>
                                                    {{ $approval->created_at->format("d/m/Y H:i") }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Status:</strong>
                                                </td>
                                                <td>
                                                    @if ($approval->isPending())
                                                        <span
                                                            class="badge bg-warning"
                                                        >
                                                            Menunggu
                                                        </span>
                                                    @elseif ($approval->isApproved())
                                                        <span
                                                            class="badge bg-success"
                                                        >
                                                            Disetujui
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge bg-danger"
                                                        >
                                                            Ditolak
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>
                                                        Tanggal Diproses:
                                                    </strong>
                                                </td>
                                                <td>
                                                    @if ($approval->updated_at != $approval->created_at)
                                                        {{ $approval->updated_at->format("d/m/Y H:i") }}
                                                    @else
                                                            -
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Pemohon -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            Informasi Pemohon
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td>
                                                    <strong>
                                                        Nama Admin Gudang:
                                                    </strong>
                                                </td>
                                                <td>
                                                    {{ $approval->adminGudang->user->nama }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>
                                                    {{ $approval->adminGudang->user->email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Dapur:</strong></td>
                                                <td>
                                                    {{ $approval->stockItem->dapur->nama_dapur }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>
                                                        Alamat Dapur:
                                                    </strong>
                                                </td>
                                                <td>
                                                    {{ $approval->stockItem->dapur->alamat_dapur }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Barang -->
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            Detail Barang
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table
                                                    class="table table-borderless"
                                                >
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                Nama Bahan:
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            {{ $approval->stockItem->templateItem->nama_bahan }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                Kategori:
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            {{ ucfirst($approval->stockItem->templateItem->kategori) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                Satuan:
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            {{ $approval->stockItem->templateItem->satuan }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table
                                                    class="table table-borderless"
                                                >
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                Stok Saat Ini:
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            {{ number_format($approval->stockItem->jumlah_stok, 0) }}
                                                            {{ $approval->stockItem->templateItem->satuan }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                Jumlah Diminta:
                                                            </strong>
                                                        </td>
                                                        <td
                                                            class="fw-bold text-primary"
                                                        >
                                                            {{ number_format($approval->jumlah_diminta, 0) }}
                                                            {{ $approval->stockItem->templateItem->satuan }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                Harga per
                                                                Satuan:
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            Rp
                                                            {{ number_format($approval->stockItem->harga_per_unit, 0, ",", ".") }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            @if ($approval->keterangan)
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                Keterangan
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                {!! nl2br(e($approval->keterangan)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Approve -->
    @if ($approval->isPending())
        <div
            class="modal fade"
            id="approveModal"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <form
                        action="{{ route("kepala-dapur.approvals.approve", ["dapur" => $dapur, "approval" => $approval]) }}"
                        method="POST"
                    >
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Setujui Permintaan</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Apakah Anda yakin ingin menyetujui permintaan
                                stok untuk:
                            </p>
                            <div class="alert alert-info">
                                <strong>
                                    {{ $approval->stockItem->templateItem->nama_bahan }}
                                </strong>
                                <br />
                                Jumlah:
                                {{ number_format($approval->jumlah_diminta, 0) }}
                                {{ $approval->stockItem->templateItem->satuan }}
                            </div>

                            <div class="mb-3">
                                <label
                                    for="keterangan_approval"
                                    class="form-label"
                                >
                                    Catatan (Opsional)
                                </label>
                                <textarea
                                    class="form-control"
                                    id="keterangan_approval"
                                    name="keterangan_approval"
                                    rows="3"
                                    placeholder="Tambahkan catatan untuk persetujuan ini..."
                                ></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal"
                            >
                                Batal
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-check me-1"></i>
                                Setujui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Reject -->
        <div
            class="modal fade"
            id="rejectModal"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <form
                        action="{{ route("kepala-dapur.approvals.reject", ["dapur" => $dapur, "approval" => $approval]) }}"
                        method="POST"
                    >
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title text-danger">
                                Tolak Permintaan
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Apakah Anda yakin ingin menolak permintaan stok
                                untuk:
                            </p>
                            <div class="alert alert-warning">
                                <strong>
                                    {{ $approval->stockItem->templateItem->nama_bahan }}
                                </strong>
                                <br />
                                Jumlah:
                                {{ number_format($approval->jumlah_diminta, 0) }}
                                {{ $approval->stockItem->templateItem->satuan }}
                            </div>

                            <div class="mb-3">
                                <label
                                    for="alasan_penolakan"
                                    class="form-label"
                                >
                                    Alasan Penolakan
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    class="form-control @error("alasan_penolakan") is-invalid @enderror"
                                    id="alasan_penolakan"
                                    name="alasan_penolakan"
                                    rows="3"
                                    placeholder="Masukkan alasan penolakan..."
                                    required
                                ></textarea>
                                @error("alasan_penolakan")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal"
                            >
                                Batal
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bx bx-x me-1"></i>
                                Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push("scripts")
    <script>
        // Auto focus pada textarea ketika modal dibuka
        $('#approveModal').on('shown.bs.modal', function () {
            $('#keterangan_approval').focus();
        });

        $('#rejectModal').on('shown.bs.modal', function () {
            $('#alasan_penolakan').focus();
        });
    </script>
@endpush
