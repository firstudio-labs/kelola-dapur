{{-- resources/views/ahligizi/transaksi/create.blade.php --}}
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
                                    <i class="bx bx-plus"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1">Buat Input Paket Menu Baru</h4>
                                <p class="mb-0 text-muted">
                                    <i class="bx bx-building me-1"></i>
                                    Dapur: {{ $ahliGizi->dapur->nama_dapur }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Buat Paket -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Dasar Paket Menu</h5>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ route("ahli-gizi.transaksi.store") }}"
                            method="POST"
                        >
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label
                                        for="tanggal_transaksi"
                                        class="form-label"
                                    >
                                        Tanggal Transaksi
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        class="form-control @error("tanggal_transaksi") is-invalid @enderror"
                                        id="tanggal_transaksi"
                                        name="tanggal_transaksi"
                                        value="{{ old("tanggal_transaksi", date("Y-m-d")) }}"
                                        min="{{ date("Y-m-d") }}"
                                        required
                                    />
                                    @error("tanggal_transaksi")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nama_paket" class="form-label">
                                        Nama Paket Menu
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control @error("nama_paket") is-invalid @enderror"
                                        id="nama_paket"
                                        name="nama_paket"
                                        value="{{ old("nama_paket") }}"
                                        placeholder="Contoh: Paket Menu Hari Senin"
                                        required
                                    />
                                    @error("nama_paket")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">
                                    Keterangan
                                    <span class="text-muted">(Opsional)</span>
                                </label>
                                <textarea
                                    class="form-control @error("keterangan") is-invalid @enderror"
                                    id="keterangan"
                                    name="keterangan"
                                    rows="3"
                                    placeholder="Tambahkan keterangan jika diperlukan"
                                >
{{ old("keterangan") }}</textarea
                                >
                                @error("keterangan")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Info Steps -->
                            <div class="alert alert-info">
                                <h6 class="alert-heading mb-2">
                                    Langkah Selanjutnya:
                                </h6>
                                <ol class="mb-0">
                                    <li>Input Porsi Besar (Wajib)</li>
                                    <li>Input Porsi Kecil (Opsional)</li>
                                    <li>Preview & Cek Stock</li>
                                    <li>Ajukan Persetujuan</li>
                                </ol>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a
                                    href="{{ route("ahli-gizi.transaksi.index") }}"
                                    class="btn btn-outline-secondary"
                                >
                                    <i class="bx bx-arrow-back me-1"></i>
                                    Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-check me-1"></i>
                                    Lanjutkan ke Input Porsi Besar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        $(document).ready(function () {
            // Set minimum date to today
            $('#tanggal_transaksi').attr(
                'min',
                new Date().toISOString().split('T')[0],
            );
        });
    </script>
@endpush
