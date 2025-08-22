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
                                <h4 class="mb-1">Preview Input Paket Menu</h4>
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

        <!-- Preview Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Transaksi</h5>
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

                        <!-- Stock Comparison -->
                        <h6 class="mb-3">Perbandingan Stok</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Bahan</th>
                                        <th>Jumlah Dibutuhkan</th>
                                        <th>Jumlah Tersedia</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockCheck["items"] as $item)
                                        <tr
                                            class="{{ $item["sufficient"] ? "" : "table-danger" }}"
                                        >
                                            <td>{{ $item["nama_bahan"] }}</td>
                                            <td>
                                                {{ $item["jumlah_dibutuhkan"] }}
                                                {{ $item["satuan"] }}
                                            </td>
                                            <td>
                                                {{ $item["jumlah_tersedia"] }}
                                                {{ $item["satuan"] }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-label-{{ $item["sufficient"] ? "success" : "danger" }}"
                                                >
                                                    {{ $item["sufficient"] ? "Cukup" : "Kurang" }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end">
                            <a
                                href="{{ route("ahli-gizi.transaksi.edit-porsi-kecil", $transaksi) }}"
                                class="btn btn-secondary me-2"
                            >
                                Kembali
                            </a>
                            @if (! $stockCheck["can_produce"])
                                <form
                                    action="{{ route("ahli-gizi.transaksi.create-shortage-report", $transaksi) }}"
                                    method="POST"
                                    class="me-2"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="btn btn-warning"
                                    >
                                        Laporkan Kekurangan
                                    </button>
                                </form>
                            @else
                                <form
                                    action="{{ route("ahli-gizi.transaksi.submit-approval", $transaksi) }}"
                                    method="POST"
                                >
                                    @csrf
                                    <div class="mb-3">
                                        <label
                                            for="keterangan"
                                            class="form-label"
                                        >
                                            Keterangan Approval
                                        </label>
                                        <textarea
                                            class="form-control"
                                            id="keterangan"
                                            name="keterangan"
                                            rows="4"
                                        ></textarea>
                                    </div>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Ajukan untuk Persetujuan
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
