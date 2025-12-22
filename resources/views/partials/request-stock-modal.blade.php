<!-- Request Stock Modal -->
<div
    class="modal fade"
    id="requestStockModal"
    tabindex="-1"
    aria-labelledby="requestStockModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestStockModalLabel">
                    Ajukan Tambah Stok
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <form
                id="requestStockForm"
                action="{{ isset($stockItem) ? route("admin-gudang.stock.request", [$dapur, $stockItem]) : '#' }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Bahan</label>
                            <input
                                type="text"
                                id="modalBahanName"
                                class="form-control"
                                value="{{ isset($stockItem) ? $stockItem->templateItem->nama_bahan : '' }}"
                                readonly
                            />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Saat Ini</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    id="modalCurrentStock"
                                    class="form-control"
                                    value="{{ isset($stockItem) ? rtrim(rtrim(number_format($stockItem->jumlah, 3), "0"), ".") : '' }}"
                                    readonly
                                />
                                <span class="input-group-text" id="modalCurrentSatuan">
                                    {{ isset($stockItem) ? $stockItem->templateItem->satuan : '' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">
                            Jumlah Penambahan
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input
                                type="number"
                                name="jumlah"
                                id="jumlah"
                                class="form-control @error("jumlah") is-invalid @enderror"
                                step="0.001"
                                min="0.1"
                                max="2000000000"
                                required
                                placeholder="0.000"
                                value="{{ old("jumlah") }}"
                            />
                            <span class="input-group-text" id="modalSatuan">
                                {{ isset($stockItem) ? $stockItem->templateItem->satuan : '' }}
                            </span>
                        </div>
                        @error("jumlah")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jam_kedatangan" class="form-label">
                                Jam Kedatangan
                            </label>
                            <input
                                type="time"
                                name="jam_kedatangan"
                                id="jam_kedatangan"
                                class="form-control @error("jam_kedatangan") is-invalid @enderror"
                                value="{{ old("jam_kedatangan") }}"
                            />
                            @error("jam_kedatangan")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_produksi" class="form-label">
                                Tanggal Produksi
                            </label>
                            <input
                                type="date"
                                name="tanggal_produksi"
                                id="tanggal_produksi"
                                class="form-control @error("tanggal_produksi") is-invalid @enderror"
                                value="{{ old("tanggal_produksi") }}"
                            />
                            @error("tanggal_produksi")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_expired" class="form-label">
                                Tanggal Expired
                            </label>
                            <input
                                type="date"
                                name="tanggal_expired"
                                id="tanggal_expired"
                                class="form-control @error("tanggal_expired") is-invalid @enderror"
                                value="{{ old("tanggal_expired") }}"
                            />
                            @error("tanggal_expired")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="suhu_bahan_makanan" class="form-label">
                                Suhu Bahan Makanan (°C)
                            </label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    name="suhu_bahan_makanan"
                                    id="suhu_bahan_makanan"
                                    class="form-control @error("suhu_bahan_makanan") is-invalid @enderror"
                                    step="0.01"
                                    min="-50"
                                    max="100"
                                    placeholder="0.00"
                                    value="{{ old("suhu_bahan_makanan") }}"
                                />
                                <span class="input-group-text">°C</span>
                            </div>
                            @error("suhu_bahan_makanan")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="warna_bahan_makanan" class="form-label">
                                Warna Bahan Makanan
                            </label>
                            <input
                                type="text"
                                name="warna_bahan_makanan"
                                id="warna_bahan_makanan"
                                class="form-control @error("warna_bahan_makanan") is-invalid @enderror"
                                maxlength="50"
                                placeholder="Contoh: Merah, Hijau, Kuning"
                                value="{{ old("warna_bahan_makanan") }}"
                            />
                            @error("warna_bahan_makanan")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="foto_bahan" class="form-label">
                                Upload Foto Bahan
                            </label>
                            <input
                                type="file"
                                name="foto_bahan"
                                id="foto_bahan"
                                class="form-control @error("foto_bahan") is-invalid @enderror"
                                accept="image/jpeg,image/jpg,image/png"
                            />
                            @error("foto_bahan")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                Format: JPEG, JPG, PNG (Maks. 5MB). Akan dikonversi ke WebP otomatis.
                            </div>
                            <div id="fotoPreview" class="mt-2" style="display: none;">
                                <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">
                            Keterangan
                        </label>
                        <textarea
                            name="keterangan"
                            id="keterangan"
                            class="form-control @error("keterangan") is-invalid @enderror"
                            rows="3"
                            maxlength="500"
                            placeholder="Alasan penambahan stok (opsional)..."
                        >{{ old("keterangan") }}</textarea>
                        @error("keterangan")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">Maksimal 500 karakter</div>
                    </div>

                    <!-- Preview Section -->
                    <div class="bg-light rounded p-3">
                        <h6 class="mb-2">Preview Permintaan:</h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">
                                    Stok Saat Ini:
                                </small>
                                <div class="fw-medium" id="previewCurrentStock">
                                    {{ isset($stockItem) ? rtrim(rtrim(number_format($stockItem->jumlah, 3), "0"), ".") . ' ' . $stockItem->templateItem->satuan : '-' }}
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">
                                    Stok Setelah Disetujui:
                                </small>
                                <div
                                    class="fw-medium text-success"
                                    id="previewStock"
                                >
                                    -
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-send me-1"></i>
                        Ajukan Permintaan (Auto Approve)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle request stock modal
        const requestStockModal = document.getElementById('requestStockModal');
        const jumlahInput = document.getElementById('jumlah');
        const previewStock = document.getElementById('previewStock');
        const previewCurrentStock = document.getElementById('previewCurrentStock');
        const modalBahanName = document.getElementById('modalBahanName');
        const modalCurrentStock = document.getElementById('modalCurrentStock');
        const modalCurrentSatuan = document.getElementById('modalCurrentSatuan');
        const modalSatuan = document.getElementById('modalSatuan');
        const requestStockForm = document.getElementById('requestStockForm');
        
        @if(isset($stockItem))
            let currentStock = {{ $stockItem->jumlah }};
            let satuan = '{{ $stockItem->templateItem->satuan }}';
        @else
            let currentStock = 0;
            let satuan = '';
        @endif

        // Update preview when amount changes
        if (jumlahInput && previewStock) {
            function updatePreview() {
                const additionalAmount = parseFloat(jumlahInput.value) || 0;
                const newStock = currentStock + additionalAmount;
                let formattedNewStock = newStock.toFixed(3);
                formattedNewStock = formattedNewStock.replace(/\.?0+$/, '');
                previewStock.textContent = formattedNewStock + ' ' + satuan;
            }

            jumlahInput.addEventListener('input', updatePreview);

            // Initialize preview if stockItem is available
            @if(isset($stockItem))
                updatePreview();
            @endif
        }

        // Preview image before upload
        const fotoInput = document.getElementById('foto_bahan');
        const fotoPreview = document.getElementById('fotoPreview');
        const previewImage = document.getElementById('previewImage');

        if (fotoInput && fotoPreview && previewImage) {
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        fotoPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    fotoPreview.style.display = 'none';
                }
            });
        }

        // Set minimum date for expired date based on production date
        const tanggalProduksi = document.getElementById('tanggal_produksi');
        const tanggalExpired = document.getElementById('tanggal_expired');

        if (tanggalProduksi && tanggalExpired) {
            tanggalProduksi.addEventListener('change', function() {
                if (this.value) {
                    tanggalExpired.min = this.value;
                }
            });
        }

        // Show modal if there are validation errors
        @if($errors->any())
            const modal = new bootstrap.Modal(requestStockModal);
            modal.show();
        @endif

        // Function to update modal data dynamically (for index page)
        window.updateRequestStockModal = function(stockId, bahanName, currentStockValue, satuanValue) {
            if (modalBahanName) modalBahanName.value = bahanName;
            if (modalCurrentStock) {
                let formattedStock = parseFloat(currentStockValue).toFixed(3);
                formattedStock = formattedStock.replace(/\.?0+$/, '');
                modalCurrentStock.value = formattedStock;
            }
            if (modalCurrentSatuan) modalCurrentSatuan.textContent = satuanValue;
            if (modalSatuan) modalSatuan.textContent = satuanValue;
            if (previewCurrentStock) {
                let formattedStock = parseFloat(currentStockValue).toFixed(3);
                formattedStock = formattedStock.replace(/\.?0+$/, '');
                previewCurrentStock.textContent = formattedStock + ' ' + satuanValue;
            }
            if (requestStockForm) {
                const actionUrl = '{{ route("admin-gudang.stock.request", [$dapur, ":stockId"]) }}';
                requestStockForm.action = actionUrl.replace(':stockId', stockId);
            }
            
            // Update global variables for preview calculation
            currentStock = parseFloat(currentStockValue);
            satuan = satuanValue;
            
            // Reset form
            if (requestStockForm) {
                requestStockForm.reset();
                // Restore non-input values
                if (modalBahanName) modalBahanName.value = bahanName;
                if (modalCurrentStock) {
                    let formattedStock = parseFloat(currentStockValue).toFixed(3);
                    formattedStock = formattedStock.replace(/\.?0+$/, '');
                    modalCurrentStock.value = formattedStock;
                }
                if (modalCurrentSatuan) modalCurrentSatuan.textContent = satuanValue;
                if (modalSatuan) modalSatuan.textContent = satuanValue;
                if (previewCurrentStock) {
                    let formattedStock = parseFloat(currentStockValue).toFixed(3);
                    formattedStock = formattedStock.replace(/\.?0+$/, '');
                    previewCurrentStock.textContent = formattedStock + ' ' + satuanValue;
                }
                if (previewStock) previewStock.textContent = '-';
                
                // Reset preview image
                const fotoPreview = document.getElementById('fotoPreview');
                const previewImage = document.getElementById('previewImage');
                if (fotoPreview) fotoPreview.style.display = 'none';
                if (previewImage) previewImage.src = '';
                
                // Reset date min attribute
                const tanggalExpired = document.getElementById('tanggal_expired');
                if (tanggalExpired) tanggalExpired.min = '';
            }
        };
    });
</script>

