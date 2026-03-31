@extends("template_admin.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        
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
                            <span class="text-dark">Tambah Template Bahan</span>
                        </nav>
                        <h4 class="mb-1">Tambah Template Bahan</h4>
                        <p class="mb-0 text-muted">
                            Buat template bahan baru untuk digunakan dalam menu
                            makanan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form
                    action="{{ route("superadmin.template-items.store") }}"
                    method="POST"
                    class="row g-4"
                >
                    @csrf

                    <div class="col-12">
                        <h5 class="card-title mb-0">
                            Informasi Template Bahan
                        </h5>
                        <div class="row g-4 mt-2">
                            
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
                                    value="{{ old("nama_bahan") }}"
                                />
                                @error("nama_bahan")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

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

                            <div class="col-md-12 mt-4 pt-3 border-top">
                                <h6 class="fw-semibold">Kandungan Gizi</h6>
                                <p class="text-muted small">Pilih kandungan gizi dominan pada bahan ini (bisa lebih dari satu).</p>
                                <div class="row g-3">
                                    @php
                                        $kandunganGiziEnum = ['Protein', 'Karbohidrat', 'Lemak', 'Vitamin', 'Omega', 'Mineral'];
                                    @endphp
                                    @foreach($kandunganGiziEnum as $gizi)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check custom-option custom-option-basic">
                                            <label class="form-check-label custom-option-content" for="gizi_{{ Str::slug($gizi) }}">
                                                <input name="kandungan_gizi[]" class="form-check-input" type="checkbox" value="{{ $gizi }}" id="gizi_{{ Str::slug($gizi) }}" {{ is_array(old('kandungan_gizi')) && in_array($gizi, old('kandungan_gizi')) ? 'checked' : '' }} />
                                                <span class="custom-option-header">
                                                    <span class="h6 mb-0">{{ $gizi }}</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @error('kandungan_gizi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-4 pt-3 border-top">
                                <h6 class="fw-semibold">Jenis Bahan Makanan</h6>
                                <p class="text-muted small">Pilih klasifikasi jenis bahan makanan ini (bisa lebih dari satu).</p>
                                <div class="row g-3">
                                    @php
                                        $jenisBahanEnum = [
                                            'Serealia dan olahannya', 'Kacang dan olahannya', 'Umbi dan sejenisnya', 
                                            'Daging', 'Unggas', 'Ikan dan seafood', 'Telur', 'Susu', 'Sayuran', 
                                            'Buah-buahan', 'Bumbu dan rempah', 'Gula dan madu', 'Beras dan makanan pokok', 'Air mineral dan air kemasan'
                                        ];
                                    @endphp
                                    @foreach($jenisBahanEnum as $jenis)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check custom-option custom-option-basic">
                                            <label class="form-check-label custom-option-content" for="jenis_{{ Str::slug($jenis) }}">
                                                <input name="jenis_bahan[]" class="form-check-input" type="checkbox" value="{{ $jenis }}" id="jenis_{{ Str::slug($jenis) }}" {{ is_array(old('jenis_bahan')) && in_array($jenis, old('jenis_bahan')) ? 'checked' : '' }} />
                                                <span class="custom-option-header">
                                                    <span class="h6 mb-0">{{ $jenis }}</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @error('jenis_bahan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4 pt-3 border-top">
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
{{ old("keterangan") }}</textarea
                                >
                                @error("keterangan")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between">
                            <a
                                href="{{ route("superadmin.template-items.index") }}"
                                class="btn btn-label-secondary"
                            >
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Simpan Template Bahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Preview Template Bahan</h5>
            </div>
            <div class="card-body">
                <h4 id="preview-nama">Nama Bahan</h4>
                <p id="preview-satuan">Satuan: -</p>
                <p id="preview-keterangan">Keterangan: -</p>
            </div>
        </div>

        <div class="alert alert-info alert-dismissible" role="alert">
            <h6 class="alert-heading mb-2">Instruksi Tambah Template Bahan</h6>
            <ul class="mb-0">
                <li>Nama bahan harus unik.</li>
                <li>Satuan wajib diisi (misal: gram, ml, buah).</li>
                <li>Keterangan bersifat opsional untuk catatan tambahan.</li>
            </ul>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    </div>
@endsection

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

            satuanInput.addEventListener('change', function () {
                const selectedText = this.options[this.selectedIndex].text;
                previewSatuan.textContent =
                    'Satuan: ' + (this.value ? selectedText : '-');
            });

            keteranganInput.addEventListener('input', function () {
                previewKeterangan.textContent =
                    'Keterangan: ' + (this.value || '-');
            });
        });
    </script>
@endpush
