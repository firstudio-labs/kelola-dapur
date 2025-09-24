@extends("template_admin.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("superadmin.dashboard") }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a
                                href="{{ route("superadmin.template-items.index") }}"
                                class="text-muted me-2"
                            >
                                Kelola Template Bahan
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">
                                Edit {{ $templateItem->nama_bahan }}
                            </span>
                        </nav>
                        <h4 class="mb-1">Edit Template Bahan</h4>
                        <p class="mb-0 text-muted">
                            Perbarui detail template bahan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="card mb-4">
            <div class="card-body">
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

                <form
                    action="{{ route("superadmin.template-items.update", $templateItem) }}"
                    method="POST"
                    class="row g-4"
                >
                    @csrf
                    @method("PUT")

                    <!-- Template Item Information -->
                    <div class="col-12">
                        <h5 class="card-title mb-0">
                            Informasi Template Bahan
                        </h5>
                        <div class="row g-4 mt-2">
                            <!-- Nama Bahan -->
                            <div class="col-md-6">
                                <label for="nama_bahan" class="form-label">
                                    Nama Bahan
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="nama_bahan"
                                    id="nama_bahan"
                                    required
                                    class="form-control @error("nama_bahan") is-invalid @enderror"
                                    placeholder="Contoh: Beras"
                                    value="{{ old("nama_bahan", $templateItem->nama_bahan) }}"
                                />
                                @error("nama_bahan")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Satuan -->
                            <div class="col-md-6">
                                <label for="satuan" class="form-label">
                                    Satuan
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="satuan"
                                    id="satuan"
                                    required
                                    class="form-select @error("satuan") is-invalid @enderror"
                                >
                                    <option value="">Pilih Satuan</option>
                                    @foreach (["kg", "liter", "pcs"] as $satuan)
                                        <option
                                            value="{{ $satuan }}"
                                            {{ old("satuan") == $satuan ? "selected" : "" }}
                                        >
                                            {{ ucfirst($satuan) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("satuan")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Keterangan -->
                            <div class="col-12">
                                <label for="keterangan" class="form-label">
                                    Keterangan
                                </label>
                                <textarea
                                    name="keterangan"
                                    id="keterangan"
                                    rows="3"
                                    class="form-control @error("keterangan") is-invalid @enderror"
                                    placeholder="Deskripsi atau catatan tentang bahan ini"
                                >
{{ old("keterangan", $templateItem->keterangan) }}</textarea
                                >
                                @error("keterangan")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between">
                            <a
                                href="{{ route("superadmin.template-items.index") }}"
                                class="btn btn-label-secondary"
                            >
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Preview Template Bahan</h5>
            </div>
            <div class="card-body">
                <h4 id="preview-nama">{{ $templateItem->nama_bahan }}</h4>
                <p id="preview-satuan">Satuan: {{ $templateItem->satuan }}</p>
                <p id="preview-keterangan">
                    Keterangan: {{ $templateItem->keterangan ?: "-" }}
                </p>
            </div>
        </div>

        <!-- Instructions Alert -->
        <div class="alert alert-info alert-dismissible" role="alert">
            <h6 class="alert-heading mb-2">Instruksi Edit Template Bahan</h6>
            <ul class="mb-0">
                <li>Nama bahan harus unik.</li>
                <li>Satuan wajib diisi (misal: gram, ml, buah).</li>
                <li>Keterangan bersifat opsional untuk catatan tambahan.</li>
                <li>
                    Pastikan perubahan tidak mengganggu menu atau stock yang
                    menggunakan bahan ini.
                </li>
            </ul>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    </div>

    @push("scripts")
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const namaInput = document.getElementById('nama_bahan');
                const satuanInput = document.getElementById('satuan');
                const keteranganInput = document.getElementById('keterangan');

                const previewNama = document.getElementById('preview-nama');
                const previewSatuan = document.getElementById('preview-satuan');
                const previewKeterangan =
                    document.getElementById('preview-keterangan');

                namaInput.addEventListener('input', function () {
                    previewNama.textContent = this.value || 'Nama Bahan';
                });

                satuanInput.addEventListener('input', function () {
                    previewSatuan.textContent =
                        'Satuan: ' + (this.value || '-');
                });

                keteranganInput.addEventListener('input', function () {
                    previewKeterangan.textContent =
                        'Keterangan: ' + (this.value || '-');
                });
            });
        </script>
    @endpush
@endsection
