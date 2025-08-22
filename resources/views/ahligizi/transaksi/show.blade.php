@extends("template_ahli_gizi.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3">
                                <span
                                    class="avatar-initial rounded-circle bg-label-primary"
                                >
                                    <i class="bx bx-cart"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1">Detail Transaksi Dapur</h4>
                                <p class="mb-0 text-muted">
                                    Transaksi ID:
                                    {{ $transaksi->id_transaksi }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Transaksi -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        @if (session("success"))
                            <div
                                class="alert alert-success alert-dismissible"
                                role="alert"
                            >
                                {{ session("success") }}
                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close"
                                ></button>
                            </div>
                        @endif

                        @if (session("error"))
                            <div
                                class="alert alert-danger alert-dismissible"
                                role="alert"
                            >
                                {{ session("error") }}
                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close"
                                ></button>
                            </div>
                        @endif

                        <dl class="row">
                            <dt class="col-sm-4">Tanggal Transaksi</dt>
                            <dd class="col-sm-8">
                                {{ $transaksi->tanggal_transaksi->format("d M Y") }}
                            </dd>
                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                <span
                                    class="badge bg-label-{{ $transaksi->status == "draft" ? "warning" : ($transaksi->status == "pending" ? "info" : ($transaksi->status == "approved" ? "success" : "danger")) }}"
                                >
                                    {{ ucfirst($transaksi->status) }}
                                </span>
                            </dd>
                            <dt class="col-sm-4">Keterangan</dt>
                            <dd class="col-sm-8">
                                {{ $transaksi->keterangan ?? "N/A" }}
                            </dd>
                            <dt class="col-sm-4">Dibuat Oleh</dt>
                            <dd class="col-sm-8">
                                {{ $transaksi->createdBy->nama ?? "N/A" }}
                            </dd>
                        </dl>

                        <!-- Porsi Besar -->
                        <h6 class="mb-3">Porsi Besar</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Menu</th>
                                        <th>Jumlah Porsi</th>
                                        <th>Bahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksi->detailTransaksiDapur->where("tipe_porsi", "besar") as $detail)
                                        <tr>
                                            <td>
                                                {{ $detail->menuMakanan->nama_menu }}
                                            </td>
                                            <td>
                                                {{ $detail->jumlah_porsi }}
                                            </td>
                                            <td>
                                                <ul>
                                                    @foreach ($detail->menuMakanan->bahanMenu as $bahan)
                                                        <li>
                                                            {{ $bahan->templateItem->nama_bahan }}:
                                                            {{ $bahan->jumlah * $detail->jumlah_porsi }}
                                                            {{ $bahan->satuan }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Porsi Kecil -->
                        <h6 class="mb-3">Porsi Kecil</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Menu</th>
                                        <th>Jumlah Porsi</th>
                                        <th>Bahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksi->detailTransaksiDapur->where("tipe_porsi", "kecil") as $detail)
                                        <tr>
                                            <td>
                                                {{ $detail->menuMakanan->nama_menu }}
                                            </td>
                                            <td>
                                                {{ $detail->jumlah_porsi }}
                                            </td>
                                            <td>
                                                <ul>
                                                    @foreach ($detail->menuMakanan->bahanMenu as $bahan)
                                                        <li>
                                                            {{ $bahan->templateItem->nama_bahan }}:
                                                            {{ $bahan->jumlah * $detail->jumlah_porsi }}
                                                            {{ $bahan->satuan }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Laporan Kekurangan -->
                        @if ($transaksi->laporanKekuranganStock->isNotEmpty())
                            <h6 class="mb-3">Laporan Kekurangan Stock</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Bahan</th>
                                            <th>Jumlah Dibutuhkan</th>
                                            <th>Jumlah Tersedia</th>
                                            <th>Jumlah Kurang</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transaksi->laporanKekuranganStock as $laporan)
                                            <tr>
                                                <td>
                                                    {{ $laporan->templateItem->nama_bahan }}
                                                </td>
                                                <td>
                                                    {{ $laporan->jumlah_dibutuhkan }}
                                                    {{ $laporan->satuan }}
                                                </td>
                                                <td>
                                                    {{ $laporan->jumlah_tersedia }}
                                                    {{ $laporan->satuan }}
                                                </td>
                                                <td>
                                                    {{ $laporan->jumlah_kurang }}
                                                    {{ $laporan->satuan }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-label-{{ $laporan->status == "pending" ? "warning" : "success" }}"
                                                    >
                                                        {{ ucfirst($laporan->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end mt-3">
                            <a
                                href="{{ route("ahli-gizi.transaksi.index") }}"
                                class="btn btn-secondary me-2"
                            >
                                Kembali
                            </a>
                            @if ($transaksi->status == "draft")
                                <form
                                    action="{{ route("ahli-gizi.transaksi.destroy", $transaksi) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method("DELETE")
                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
