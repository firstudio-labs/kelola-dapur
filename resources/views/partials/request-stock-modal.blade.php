
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
                                    value="{{ isset($stockItem) ? (float) $stockItem->jumlah : '' }}"
                                    readonly
                                />
                                <span class="input-group-text" id="modalCurrentSatuan">
                                    {{ isset($stockItem) ? $stockItem->templateItem->satuan : '' }}
                                </span>
                            </div>
                        </div>
                    </div>

                        <div class="mb-3 d-none bg-light p-2 rounded" id="konversiToggleContainer">
                            <label class="form-label d-block mb-1 fw-medium" style="font-size: 0.85rem;">Format Input Satuan:</label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input unit-toggle-radio" type="radio" name="input_mode" id="unit_asli" value="asli" checked>
                                    <label class="form-check-label" for="unit_asli" id="label_unit_asli" style="font-size: 0.85rem;">Satuan Asli (-)</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input unit-toggle-radio" type="radio" name="input_mode" id="unit_konversi" value="konversi">
                                    <label class="form-check-label text-primary" for="unit_konversi" id="label_unit_konversi" style="font-size: 0.85rem;">Konversi (-)</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">
                                Total Penambahan Stok (Semua Supplier)
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="jumlah"
                                    id="jumlah"
                                    class="form-control @error("jumlah") is-invalid @enderror"
                                    required
                                    placeholder="0"
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

                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Pilih Supplier (Opsional)</span>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addSupplierRowBtn" onclick="window.createSupplierRow()">
                                    <i class="bx bx-plus me-1"></i> Tambah Supplier
                                </button>
                            </label>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-borderless mb-0" id="supplierTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="35%">Nama Supplier</th>
                                            <th width="25%">Jumlah Disuplai</th>
                                            <th width="30%">Foto (Opsional, Multi)</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="supplierContainer">
                                        
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light border-top">
                                            <td class="text-end fw-semibold">Total dari Supplier:</td>
                                            <td colspan="2">
                                                <span id="totalSupplierUi">0</span> <span id="modalSatuanBottom">{{ isset($stockItem) ? $stockItem->templateItem->satuan : '' }}</span>
                                                <small class="text-danger d-block d-none" id="supplierErrorMsg">Total melebihi Total Penambahan Stok!</small>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <small class="form-text text-muted">Jika tidak ada rincian supplier, sistem akan mencatat stok ke asal Gudang Umum.</small>
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

                    <div class="bg-light rounded p-3">
                        <h6 class="mb-2">Preview Permintaan:</h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">
                                    Stok Saat Ini:
                                </small>
                                <div class="fw-medium" id="previewCurrentStock">
                                    {{ isset($stockItem) ? ((float) $stockItem->jumlah) . ' ' . $stockItem->templateItem->satuan : '-' }}
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
        const formatIndonesianNumberJS = (value) => {
            if (value === null || value === undefined || value === '' || value === 0 || value === 0.0) return '0';
            let num = parseFloat(value);
            let numStr = parseFloat(num.toFixed(4)).toString();
            let parts = numStr.split('.');
            let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            if (parts[1]) {
                let decimals = parts[1].replace(/0+$/, '');
                if (decimals.length > 0) {
                    return integerPart + ',' + decimals;
                }
            }
            return integerPart;
        };

        const parseFormattedDecimalJS = (value) => {
            if (!value) return 0;
            const cleaned = value.toString().replace(/\./g, "").replace(/,/g, ".");
            return parseFloat(cleaned) || 0;
        };

        const bindQuantityEvents = (input) => {
            if (input.value) {
                const val = parseFloat(input.value);
                if (!isNaN(val)) {
                    input.value = formatIndonesianNumberJS(val);
                }
            }

            input.addEventListener('input', function() {
                let cursorPosition = this.selectionStart;
                let originalLength = this.value.length;
                let cleanVal = this.value.replace(/[^0-9,]/g, "");
                let parts = cleanVal.split(',');
                if (parts.length > 2) {
                    cleanVal = parts[0] + ',' + parts.slice(1).join('');
                    parts = cleanVal.split(',');
                }
                let integerPart = parts[0];
                let formattedInt = "";
                if (integerPart) {
                    if (integerPart.length > 1 && integerPart.startsWith('0')) {
                        integerPart = integerPart.replace(/^0+/, "");
                        if (integerPart === "") integerPart = "0";
                    }
                    formattedInt = new Intl.NumberFormat('id-ID').format(parseInt(integerPart) || 0);
                } else {
                    formattedInt = "0";
                }
                let result = formattedInt;
                if (parts.length > 1) {
                    result += ',' + parts[1].substring(0, 4);
                }
                this.value = result;
                let newLength = this.value.length;
                this.setSelectionRange(cursorPosition + (newLength - originalLength), cursorPosition + (newLength - originalLength));
            });

            input.addEventListener('focus', function() {
                if (this.value === "0" || this.value === "0,0" || this.value === "0,00" || this.value === "0,000" || this.value === "0,0000") {
                    this.value = "";
                }
            });

            input.addEventListener('blur', function() {
                if (this.value === "" || this.value === null) {
                    this.value = "0";
                }
            });
        };

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
            
            window.activeKonversiNilai = parseFloat({{ $stockItem->konversi_nilai ?? 0 }});
            window.activeKonversiSatuan = '{{ $stockItem->konversi_satuan ?? "" }}';
            
            if (window.activeKonversiNilai > 0 && window.activeKonversiSatuan !== '') {
                const konversiToggleContainer = document.getElementById('konversiToggleContainer');
                const labelUnitAsli = document.getElementById('label_unit_asli');
                const labelUnitKonversi = document.getElementById('label_unit_konversi');
                
                if (konversiToggleContainer) konversiToggleContainer.classList.remove('d-none');
                if (labelUnitAsli) labelUnitAsli.textContent = `Satuan Asli (${satuan})`;
                if (labelUnitKonversi) labelUnitKonversi.textContent = `Konversi (${window.activeKonversiSatuan})`;
            }
        @else
            let currentStock = 0;
            let satuan = '';
            window.activeKonversiNilai = 0;
            window.activeKonversiSatuan = '';
        @endif
        
        window.currentInputMode = 'asli';

        // Update preview when amount changes
        if (jumlahInput && previewStock) {
            bindQuantityEvents(jumlahInput);
            
            window.updatePreview = function() {
                let additionalAmount = parseFormattedDecimalJS(jumlahInput.value) || 0;
                
                // If input mode is konversi, we must convert it back to Original to add to currentStock!
                if(window.currentInputMode === 'konversi' && window.activeKonversiNilai > 0) {
                    additionalAmount = additionalAmount * window.activeKonversiNilai;
                }
                
                const newStock = currentStock + additionalAmount;
                previewStock.textContent = formatIndonesianNumberJS(newStock) + ' ' + (satuan || '');
                
                // Recalculate supplier totals
                if(typeof window.calculateSupplierTotals === 'function') {
                    window.calculateSupplierTotals();
                }
            };

            jumlahInput.addEventListener('input', window.updatePreview);

            // Initialize preview if stockItem is available
            @if(isset($stockItem))
                updatePreview();
            @endif
        }

        window.calculateSupplierTotals = function() {
            let total = 0;
            const supplierInputs = document.querySelectorAll('.supplier-jumlah-input');
            supplierInputs.forEach(input => {
                total += parseFormattedDecimalJS(input.value) || 0;
            });

            if (totalSupplierUi) totalSupplierUi.textContent = formatIndonesianNumberJS(total);

            const maxAllowed = parseFormattedDecimalJS(jumlahInput ? jumlahInput.value : 0) || 0;
            if (total > maxAllowed && total > 0) {
                if(totalSupplierUi) {
                    totalSupplierUi.classList.add('text-danger');
                    totalSupplierUi.classList.remove('text-success');
                }
                if(supplierErrorMsg) supplierErrorMsg.classList.remove('d-none');
            } else {
                if(totalSupplierUi) {
                    totalSupplierUi.classList.remove('text-danger');
                    totalSupplierUi.classList.add('text-success');
                }
                if(supplierErrorMsg) supplierErrorMsg.classList.add('d-none');
            }
        };

        let supplierOptionsHtml = '<option value="">Pilih Supplier...</option>';
        @isset($suppliers)
        @foreach($suppliers as $supplier)
            supplierOptionsHtml += '<option value="{{ $supplier->id_supplier }}">' + {!! json_encode(htmlspecialchars($supplier->nama_supplier)) !!} + '</option>';
        @endforeach
        @endisset

        window.supplierCounter = 0;

        window.createSupplierRow = function() {
            const supplierContainer = document.getElementById('supplierContainer');
            if(!supplierContainer) return;
            
            const currentIndex = window.supplierCounter++;
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="suppliers[${currentIndex}][id_supplier]" class="form-select form-select-sm" required>
                        ${supplierOptionsHtml}
                    </select>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" name="suppliers[${currentIndex}][jumlah]" class="form-control supplier-jumlah-input" required>
                    </div>
                </td>
                <td>
                    <div>
                        <label class="btn btn-sm btn-outline-secondary w-100 mb-1" style="font-size:0.75rem;">
                            <i class="bx bx-image-add me-1"></i> Pilih Foto
                            <input type="file" name="suppliers[${currentIndex}][fotos][]" class="d-none supplier-foto-input" accept="image/jpeg,image/jpg,image/png" multiple>
                        </label>
                        <div class="supplier-foto-preview" style="font-size:0.7rem;color:#555;"></div>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-supplier-btn"><i class="bx bx-trash"></i></button>
                </td>
            `;

            supplierContainer.appendChild(tr);

            // Photo preview names
            const fotoInput = tr.querySelector('.supplier-foto-input');
            const fotoPreviews = tr.querySelector('.supplier-foto-preview');
            fotoInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                fotoPreviews.textContent = files.length > 0
                    ? files.map(f => f.name).join(', ')
                    : '';
            });

            // Attach listeners
            tr.querySelector('.remove-supplier-btn').addEventListener('click', function() {
                tr.remove();
                window.calculateSupplierTotals();
            });

            const supplierInput = tr.querySelector('.supplier-jumlah-input');
            bindQuantityEvents(supplierInput);
            supplierInput.addEventListener('input', window.calculateSupplierTotals);
        };

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
        window.updateRequestStockModal = function(stockId, bahanName, currentStockValue, satuanValue, defaultJumlah = '', konversiNilaiVal = 0, konversiSatuanVal = '') {
            if (modalBahanName) modalBahanName.value = bahanName;
            if (modalCurrentStock) {
                modalCurrentStock.value = parseFloat(currentStockValue).toString();
            }
            if (modalCurrentSatuan) modalCurrentSatuan.textContent = satuanValue;
            if (modalSatuan) modalSatuan.textContent = satuanValue;
            if (modalSatuanBottom) modalSatuanBottom.textContent = satuanValue;
            if (previewCurrentStock) {
                previewCurrentStock.textContent = parseFloat(currentStockValue).toString() + ' ' + satuanValue;
            }
            if (requestStockForm) {
                const actionUrl = '{{ route("admin-gudang.stock.request", [$dapur, ":stockId"]) }}';
                requestStockForm.action = actionUrl.replace(':stockId', stockId);
            }
            
            // Update global variables for preview calculation
            currentStock = parseFloat(currentStockValue);
            satuan = satuanValue;
            
            // Konversi Logic Injection
            const konversiToggleContainer = document.getElementById('konversiToggleContainer');
            const labelUnitAsli = document.getElementById('label_unit_asli');
            const labelUnitKonversi = document.getElementById('label_unit_konversi');
            const unitAsliRadio = document.getElementById('unit_asli');
            
            window.activeKonversiNilai = parseFloat(konversiNilaiVal) || 0;
            window.activeKonversiSatuan = konversiSatuanVal || '';
            
            if (konversiToggleContainer) {
                if (window.activeKonversiNilai > 0 && window.activeKonversiSatuan !== '') {
                    konversiToggleContainer.classList.remove('d-none');
                    if(labelUnitAsli) labelUnitAsli.textContent = `Satuan Asli (${satuanValue})`;
                    if(labelUnitKonversi) labelUnitKonversi.textContent = `Konversi (${window.activeKonversiSatuan})`;
                } else {
                    konversiToggleContainer.classList.add('d-none');
                }
            }
            
            // Reset to default Asli mode strictly
            if(unitAsliRadio) unitAsliRadio.checked = true;
            window.currentInputMode = 'asli';
            
            // Reset form
            if (requestStockForm) {
                requestStockForm.reset();
                // Restore non-input values
                if (modalBahanName) modalBahanName.value = bahanName;
                if (modalCurrentStock) {
                    modalCurrentStock.value = parseFloat(currentStockValue).toString();
                }
                if (modalCurrentSatuan) modalCurrentSatuan.textContent = satuanValue;
                if (modalSatuan) modalSatuan.textContent = satuanValue;
                if (modalSatuanBottom) modalSatuanBottom.textContent = satuanValue;
                if (previewCurrentStock) {
                    previewCurrentStock.textContent = parseFloat(currentStockValue).toString() + ' ' + satuanValue;
                }
                if (previewStock) previewStock.textContent = '-';
                
                // Clear suppliers
                if (document.getElementById('supplierContainer')) {
                    document.getElementById('supplierContainer').innerHTML = '';
                    window.supplierCounter = 0;
                    window.calculateSupplierTotals();
                }
                
                // Reset preview image
                const fotoPreview = document.getElementById('fotoPreview');
                const previewImage = document.getElementById('previewImage');
                if (fotoPreview) fotoPreview.style.display = 'none';
                if (previewImage) previewImage.src = '';
                
                // Reset date min attribute
                const tanggalExpired = document.getElementById('tanggal_expired');
                if (tanggalExpired) tanggalExpired.min = '';
            }

            // Optional Default Amount (Must be applied AFTER reset)
            if (jumlahInput) {
                if (defaultJumlah !== '') {
                    // if default is provided (shortage amount) it's ALWAYS in master unit.
                    let amountToFill = parseFloat(defaultJumlah);
                    if(window.currentInputMode === 'konversi' && window.activeKonversiNilai > 0) {
                        amountToFill = amountToFill / window.activeKonversiNilai;
                    }
                    jumlahInput.value = formatIndonesianNumberJS(amountToFill);
                } else {
                    jumlahInput.value = '';
                }
                if (typeof updatePreview === 'function') updatePreview();
            }
        };

        // Attach listeners to unit radio buttons
        const unitRadios = document.querySelectorAll('.unit-toggle-radio');
        unitRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const newMode = this.value;
                if(newMode === window.currentInputMode) return;
                
                const oldMode = window.currentInputMode;
                window.currentInputMode = newMode;
                
                let multiplier = 1;
                // Asli -> Konversi
                if (oldMode === 'asli' && newMode === 'konversi') {
                    multiplier = 1 / window.activeKonversiNilai;
                } 
                // Konversi -> Asli
                else if (oldMode === 'konversi' && newMode === 'asli') {
                    multiplier = window.activeKonversiNilai;
                }
                
                // Update specific Unit Labels globally
                const targetedUnitLabel = newMode === 'asli' ? satuan : window.activeKonversiSatuan;
                if(modalSatuan) modalSatuan.textContent = targetedUnitLabel;
                if(modalSatuanBottom) modalSatuanBottom.textContent = targetedUnitLabel;
                
                // Scale existing values
                if (jumlahInput && jumlahInput.value !== '') {
                    let scaledVal = parseFormattedDecimalJS(jumlahInput.value) * multiplier;
                    jumlahInput.value = formatIndonesianNumberJS(scaledVal);
                }
                const supplierInputs = document.querySelectorAll('.supplier-jumlah-input');
                supplierInputs.forEach(input => {
                    if (input.value !== '') {
                        let scaledVal = parseFormattedDecimalJS(input.value) * multiplier;
                        input.value = formatIndonesianNumberJS(scaledVal);
                    }
                });
                
                if (typeof window.calculateSupplierTotals === 'function') {
                    window.calculateSupplierTotals();
                }
                if (typeof updatePreview === 'function') {
                    // Wait! updatePreview must calculate in Master Unit!
                    document.getElementById('jumlah').dispatchEvent(new Event('input'));
                }
            });
        });
    });
</script>
