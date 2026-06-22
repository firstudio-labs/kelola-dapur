@extends('template_akuntan.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <nav class="d-flex align-items-center mb-2">
                    <a href="{{ route('akuntan.dashboard') }}" class="text-muted me-2 small">
                        <i class="bx bx-home-alt me-1"></i>Dashboard
                    </a>
                    <i class="bx bx-chevron-right me-2 text-muted small"></i>
                    <a href="{{ route('akuntan.transaksi.index') }}" class="text-muted me-2 small">Transaksi</a>
                    <i class="bx bx-chevron-right me-2 text-muted small"></i>
                    <span class="text-dark small">Tambah Transaksi</span>
                </nav>
                <h4 class="fw-bold mb-1">Tambah Transaksi</h4>
                <p class="mb-0 text-muted small">Pencatatan data pemasukan atau pengeluaran baru</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('akuntan.transaksi.store') }}" method="POST" id="trx-form">
                    @csrf
                    <input type="hidden" name="shortages_json" id="shortages_json" value="[]">

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Periode <span class="text-danger">*</span></label>
                            <select name="period_id" class="form-select" required id="period_select">
                                <option value="">-- Pilih Periode --</option>
                                @foreach ($periods as $p)
                                    <option value="{{ $p->id }}" data-start="{{ $p->start_date->format('Y-m-d') }}"
                                        data-end="{{ $p->end_date->format('Y-m-d') }}"
                                        {{ old('period_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->year }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted" id="period-range-hint"></small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control"
                                value="{{ old('date', date('Y-m-d')) }}" required id="trx-date">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">No. Bukti</label>
                            <input type="text" name="no_bukti" class="form-control" value="{{ old('no_bukti') }}"
                                placeholder="e.g. BKU-001/2024">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required id="category_select">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories->groupBy('group_label') as $groupName => $cats)
                                    <optgroup label="{{ $groupName }}">
                                        @foreach ($cats as $c)
                                            <option value="{{ $c->id }}" data-type="{{ $c->type }}"
                                                data-is-pembelian="{{ str_contains(strtolower($c->name), 'pembelian bahan baku') ? '1' : '0' }}"
                                                {{ old('category_id') == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }} —
                                                ({{ $c->type === 'income' ? 'Penerimaan' : 'Pengeluaran' }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Uraian <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control" value="{{ old('description') }}"
                                required placeholder="Deskripsi transaksi">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Akun Kas <span class="text-danger">*</span></label>
                            <select name="cash_account_id" class="form-select" required>
                                <option value="">-- Pilih Akun Kas --</option>
                                @foreach ($cashAccounts as $ca)
                                    <option value="{{ $ca->id }}"
                                        {{ old('cash_account_id') == $ca->id ? 'selected' : '' }}>{{ $ca->name }}
                                        ({{ $ca->type_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12" id="shortage-container" style="display: none;">
                            <hr class="mb-3">
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Detail Kekurangan Stok</label>
                                <div class="text-muted small">Pilih transaksi yang memiliki kekurangan stok untuk dicatat pembeliannya.</div>
                            </div>
                            <div class="mb-3">
                                <select id="shortage-transaksi-select" name="shortage_transaksi_id" class="form-select">
                                    <option value="">-- Pilih Transaksi Dapur --</option>
                                </select>
                            </div>
                            
                            {{-- Table 1: Normal Shortages --}}
                            <div class="table-responsive mb-4" id="shortage-table-container" style="display: none;">
                                <div class="fw-bold mb-2 text-secondary" id="normal-table-title" style="display: none;">Detail Kekurangan Stok Utama</div>
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width: 20%;">Bahan Baku</th>
                                            <th style="width: 14%;">Kekurangan</th>
                                            <th style="width: 22%;">Harga Satuan (Rp)</th>
                                            <th style="width: 22%;">Jml Dibeli</th>
                                            <th style="width: 22%;">Total (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="shortage-table-body">
                                    </tbody>
                                    <tfoot class="table-light" id="normal-table-foot">
                                        <tr>
                                            <th colspan="4" class="text-end align-middle">Total Nominal Utama</th>
                                            <th id="shortage-total-nominal" class="text-end fw-bold">Rp 0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- Table 2: Handler Shortages --}}
                            <div class="table-responsive" id="handler-table-container" style="display: none;">
                                <div class="fw-bold mb-2 text-secondary">Detail Handler Kekurangan Stok</div>
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width: 20%;">Bahan Baku</th>
                                            <th style="width: 14%;">Kekurangan</th>
                                            <th style="width: 22%;">Harga Satuan (Rp)</th>
                                            <th style="width: 22%;">Jml Dibeli</th>
                                            <th style="width: 22%;">Total (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="handler-table-body">
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="4" class="text-end align-middle">Total Nominal Pengajuan Handler</th>
                                            <th id="handler-total-pengajuan" class="text-end fw-bold">Rp 0</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="text-end align-middle">Total Nominal Handler</th>
                                            <th id="handler-total-nominal" class="text-end fw-bold">Rp 0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                            {{-- Preview Saldo --}}
                            <div class="card border-0 mb-3 shadow-none" id="balance-preview-container"
                                style="display: none; background-color: #f8f9ff; border-left: 4px solid #696cff !important;">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-bold mb-0 text-primary small">
                                            <i class="bx bx-stats me-1"></i> Preview Saldo: <span
                                                id="preview-account-name">-</span>
                                        </h6>
                                        {{-- <span class="badge bg-label-primary font-size-xs" id="preview-direction">Update Otomatis</span> --}}
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-4 border-end">
                                            <div class="text-muted small mb-1">Saldo Saat Ini</div>
                                            <div class="fw-bold text-dark" id="preview-before">Rp 0</div>
                                        </div>
                                        <div class="col-4 border-end">
                                            <div class="text-muted small mb-1">Estimasi Efek</div>
                                            <div class="fw-bold" id="preview-effect">Rp 0</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-muted small mb-1">Proyeksi Akhir</div>
                                            <div class="fw-bold text-primary" id="preview-after">Rp 0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6" id="debit-container">
                            <label class="form-label fw-semibold">Debit (Pemasukan) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="debit" id="debit-field" class="form-control text-end"
                                    value="{{ old('debit', 0) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6" id="credit-container">
                            <label class="form-label fw-semibold">Kredit (Pengeluaran) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="credit" id="credit-field" class="form-control text-end"
                                    value="{{ old('credit', 0) }}">
                            </div>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Simpan
                                Transaksi</button>
                            <a href="{{ route('akuntan.transaksi.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Warning Modal -->
        <div class="modal fade" id="validationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center pt-4">
                        <div class="mb-3 text-warning">
                            <i class="bx bx-error-circle" style="font-size: 3.5rem;"></i>
                        </div>
                        <h5 class="modal-title mb-2 fw-semibold" id="validationModalTitle">Perhatian</h5>
                        <p class="text-muted small mb-0" id="validationModalMessage">Pesan peringatan akan muncul di sini.</p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                        <button type="button" class="btn btn-primary btn-sm px-4" data-bs-dismiss="modal">Mengerti</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .table-info-subtle {
        background-color: rgba(13, 202, 240, 0.08) !important;
    }
    </style>

    <script>
        let currentBalance = 0;

        function showWarningModal(message) {
            document.getElementById('validationModalMessage').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('validationModal'));
            modal.show();
        }

        function formatNumberIndonesian(value) {
            if (value === null || value === undefined || isNaN(value)) return '0';
            const num = parseFloat(value);
            const parts = num.toString().split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            if (parts[1]) {
                let decimals = parts[1].replace(/0+$/, '');
                if (decimals.length > 0) {
                    return parts[0] + ',' + decimals;
                }
            }
            return parts[0];
        }

        function formatInputThousands(input) {
            let val = input.value.replace(/\D/g, "");
            if (val.length > 1) {
                val = val.replace(/^0+/, "");
            }
            if (val) {
                input.value = new Intl.NumberFormat('id-ID').format(parseInt(val));
            } else {
                input.value = "0";
            }
        }

        function formatInputDecimal(input) {
            let cursorPosition = input.selectionStart;
            let originalLength = input.value.length;
            let cleanVal = input.value.replace(/[^0-9,]/g, "");
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
                result += ',' + parts[1].substring(0, 3);
            }
            input.value = result;
            let newLength = input.value.length;
            input.setSelectionRange(cursorPosition + (newLength - originalLength), cursorPosition + (newLength - originalLength));
        }

        function parseFormattedDecimal(value) {
            if (!value) return 0;
            const cleaned = value.replace(/\./g, "").replace(/,/g, ".");
            return parseFloat(cleaned) || 0;
        }

        async function updateBalancePreview() {
            const periodId = document.getElementById('period_select').value;
            const cashAccountId = document.querySelector('select[name="cash_account_id"]').value;
            const container = document.getElementById('balance-preview-container');

            if (!periodId || !cashAccountId) {
                container.style.display = 'none';
                return;
            }

            try {
                const response = await fetch(
                    `{{ route('akuntan.transaksi.getBalance') }}?period_id=${periodId}&cash_account_id=${cashAccountId}`
                );
                const data = await response.json();
                currentBalance = data.current_balance;

                container.style.display = 'block';
                document.getElementById('preview-account-name').textContent = data.cash_account_name;
                document.getElementById('preview-before').textContent = formatIDR(currentBalance);
                calculateAfter();
            } catch (error) {
                console.error('Error fetching balance:', error);
            }
        }

        function calculateAfter() {
            const debitVal = document.getElementById('debit-field').value.replace(/\./g, "");
            const creditVal = document.getElementById('credit-field').value.replace(/\./g, "");
            const debit = parseFloat(debitVal) || 0;
            const credit = parseFloat(creditVal) || 0;
            const effect = debit - credit;
            const after = currentBalance + effect;

            const effectEl = document.getElementById('preview-effect');
            effectEl.textContent = (effect >= 0 ? '+' : '-') + ' ' + formatIDR(Math.abs(effect));
            effectEl.className = 'fw-bold ' + (effect >= 0 ? 'text-success' : 'text-danger');

            document.getElementById('preview-after').textContent = formatIDR(after);
        }

        function formatIDR(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(amount);
        }

        document.getElementById('category_select').addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const type = opt ? opt.dataset.type : '';
            const isPembelian = opt && opt.dataset.isPembelian === '1';
            const debitCont = document.getElementById('debit-container');
            const creditCont = document.getElementById('credit-container');
            const debitFld = document.getElementById('debit-field');
            const creditFld = document.getElementById('credit-field');
            const shortageCont = document.getElementById('shortage-container');
            const periodId = document.getElementById('period_select').value;

            if (isPembelian) {
                if (!periodId) {
                    showWarningModal('Silakan pilih Periode terlebih dahulu sebelum memilih Kategori Pembelian Bahan Baku.');
                    this.value = '';
                    shortageCont.style.display = 'none';
                    return;
                }
                shortageCont.style.display = 'block';
                loadPendingShortages();
                debitFld.readOnly = true;
                creditFld.readOnly = true;
            } else {
                shortageCont.style.display = 'none';
                debitFld.readOnly = false;
                creditFld.readOnly = false;
                document.getElementById('shortage-table-body').innerHTML = '';
                document.getElementById('shortage-table-container').style.display = 'none';
                document.getElementById('handler-table-body').innerHTML = '';
                document.getElementById('handler-table-container').style.display = 'none';
                document.getElementById('normal-table-title').style.display = 'none';
                document.getElementById('shortage-transaksi-select').innerHTML = '<option value="">-- Pilih Transaksi Dapur --</option>';
            }

            if (type === 'income') {
                debitCont.style.display = 'block';
                creditCont.style.display = 'none';
                if (!isPembelian) creditFld.value = 0;
            } else if (type === 'expense') {
                debitCont.style.display = 'none';
                creditCont.style.display = 'block';
                if (!isPembelian) debitFld.value = 0;
            } else {
                debitCont.style.display = 'block';
                creditCont.style.display = 'block';
            }
            calculateAfter();
        });

        async function loadPendingShortages() {
            try {
                const periodId = document.getElementById('period_select').value;
                const response = await fetch(`{{ route('akuntan.transaksi.getPendingShortages') }}?period_id=${periodId}`);
                const data = await response.json();
                const select = document.getElementById('shortage-transaksi-select');
                select.innerHTML = '<option value="">-- Pilih Transaksi Dapur --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id_transaksi;
                    opt.textContent = item.label;
                    select.appendChild(opt);
                });
            } catch (error) {
                console.error('Error loading shortages:', error);
            }
        }

        function setupShortageRowListeners(tr, prefix) {
            const hargaInput = tr.querySelector('.' + prefix + '-harga');
            const qtyInput = tr.querySelector('.' + prefix + '-qty');
            const nominalInput = tr.querySelector('.' + prefix + '-nominal');

            if (!hargaInput || !qtyInput || !nominalInput) return;

            const updateNominal = () => {
                const rawHarga = hargaInput.value.replace(/\./g, "");
                const harga = parseFloat(rawHarga) || 0;
                const qty = parseFormattedDecimal(qtyInput.value);
                const total = harga * qty;
                nominalInput.value = new Intl.NumberFormat('id-ID').format(Math.round(total));
            };

            qtyInput.addEventListener('keypress', function(e) {
                if (e.key === '.') {
                    e.preventDefault();
                    const start = this.selectionStart;
                    const end = this.selectionEnd;
                    const text = this.value;
                    this.value = text.substring(0, start) + ',' + text.substring(end);
                    this.selectionStart = this.selectionEnd = start + 1;
                    this.dispatchEvent(new Event('input'));
                }
            });

            const onInput = function() {
                if (this === hargaInput) {
                    formatInputThousands(this);
                } else {
                    formatInputDecimal(this);
                }
                updateNominal();
                calculateShortageTotal();
            };

            hargaInput.addEventListener('input', onInput);
            qtyInput.addEventListener('input', onInput);

            const onFocus = function() {
                if (this.value === '0') {
                    this.value = '';
                }
            };

            hargaInput.addEventListener('focus', onFocus);
            qtyInput.addEventListener('focus', onFocus);

            const onBlur = function() {
                if (this.value === '') {
                    this.value = '0';
                    updateNominal();
                    calculateShortageTotal();
                }
            };

            hargaInput.addEventListener('blur', onBlur);
            qtyInput.addEventListener('blur', onBlur);
        }

        document.getElementById('shortage-transaksi-select').addEventListener('change', async function() {
            const transaksiId = this.value;
            const tableCont = document.getElementById('shortage-table-container');
            const tbody = document.getElementById('shortage-table-body');
            const handlerCont = document.getElementById('handler-table-container');
            const handlerTbody = document.getElementById('handler-table-body');
            const normalTitle = document.getElementById('normal-table-title');
            
            if (!transaksiId) {
                tableCont.style.display = 'none';
                tbody.innerHTML = '';
                handlerCont.style.display = 'none';
                handlerTbody.innerHTML = '';
                normalTitle.style.display = 'none';
                calculateShortageTotal();
                return;
            }

            try {
                const response = await fetch(`{{ route('akuntan.transaksi.getPendingShortages') }}?transaksi_id=${transaksiId}`);
                const data = await response.json();
                
                tbody.innerHTML = '';
                handlerTbody.innerHTML = '';

                const handlerShortages = data.filter(item => item.id_handler !== null);
                const normalShortages = data.filter(item => item.id_handler === null);
                const hasHandler = handlerShortages.length > 0;

                if (hasHandler) {
                    normalTitle.style.display = 'block';
                    handlerCont.style.display = 'block';
                } else {
                    normalTitle.style.display = 'none';
                    handlerCont.style.display = 'none';
                }

                // Populate Table 1: Normal Shortages
                normalShortages.forEach((item, index) => {
                    const tr = document.createElement('tr');
                    tr.dataset.laporanId = item.id_laporan;
                    tr.dataset.satuan = item.satuan;
                    tr.dataset.namaBahan = item.nama_bahan;
                    tr.dataset.alreadyPurchased = item.already_purchased ? 'true' : 'false';

                    const isReadonly = item.already_purchased;
                    const priceVal = item.already_purchased ? item.harga_satuan : 0;
                    const qtyVal = item.already_purchased ? item.qty_dibeli : item.jumlah_kurang;
                    const nominalVal = item.already_purchased ? item.nominal : 0;

                    tr.innerHTML = `
                        <td>${item.nama_bahan}</td>
                        <td class="text-center">${formatNumberIndonesian(item.jumlah_kurang)} ${item.satuan}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control shortage-harga text-end" value="${formatNumberIndonesian(priceVal)}" ${isReadonly ? 'readonly style="background-color: #f3f4f6;"' : ''}>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control shortage-qty text-end" value="${formatNumberIndonesian(qtyVal)}" ${isReadonly ? 'readonly style="background-color: #f3f4f6;"' : ''}>
                                <span class="input-group-text">${item.satuan}</span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control shortage-nominal text-end" value="${formatNumberIndonesian(nominalVal)}" readonly style="background-color: #f3f4f6;">
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                    if (!isReadonly) {
                        setupShortageRowListeners(tr, 'shortage');
                        
                        // When normal harga changes, sync handler row price if hasHandler
                        if (hasHandler) {
                            const hargaInput = tr.querySelector('.shortage-harga');
                            if (hargaInput) {
                                hargaInput.addEventListener('input', function() {
                                    // Find handler row with same nama_bahan
                                    const rows = document.querySelectorAll('#handler-table-body tr');
                                    rows.forEach(handlerRow => {
                                        if (handlerRow.dataset.namaBahan === item.nama_bahan && handlerRow.dataset.alreadyPurchased !== 'true') {
                                            const handlerHarga = handlerRow.querySelector('.handler-harga');
                                            const handlerQty = handlerRow.querySelector('.handler-qty');
                                            const handlerNominal = handlerRow.querySelector('.handler-nominal');
                                            if (handlerHarga && handlerQty && handlerNominal) {
                                                handlerHarga.value = this.value;
                                                const rawHarga = this.value.replace(/\./g, '');
                                                const harga = parseFloat(rawHarga) || 0;
                                                const qty = parseFormattedDecimal(handlerQty.value);
                                                const total = harga * qty;
                                                handlerNominal.value = new Intl.NumberFormat('id-ID').format(Math.round(total));
                                                calculateShortageTotal();
                                            }
                                        }
                                    });
                                });
                            }
                        }
                    }
                });

                // Populate Table 2: Handler Shortages
                if (hasHandler) {
                    handlerShortages.forEach((item, index) => {
                        const tr = document.createElement('tr');
                        tr.dataset.laporanId = item.id_laporan;
                        tr.dataset.satuan = item.satuan;
                        tr.dataset.namaBahan = item.nama_bahan;
                        tr.dataset.alreadyPurchased = item.already_purchased ? 'true' : 'false';

                        // Find corresponding price from normalShortages (if already purchased, use stored price; else mirror table 1 dynamically)
                        const corresponding = normalShortages.find(n => n.nama_bahan === item.nama_bahan);
                        const basePrice = corresponding ? (corresponding.already_purchased ? corresponding.harga_satuan : 0) : 0;
                        
                        const priceVal = item.already_purchased ? item.harga_satuan : basePrice;
                        const qtyVal = item.already_purchased ? item.qty_dibeli : item.jumlah_kurang;
                        const nominalVal = item.already_purchased ? item.nominal : (priceVal * qtyVal);

                        const isQtyReadonly = item.already_purchased;

                        tr.innerHTML = `
                            <td>${item.nama_bahan} - Handler</td>
                            <td class="text-center">${formatNumberIndonesian(item.jumlah_kurang)} ${item.satuan}</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control handler-harga text-end" value="${formatNumberIndonesian(priceVal)}" readonly style="background-color: #f3f4f6;">
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control handler-qty text-end" value="${formatNumberIndonesian(qtyVal)}" ${isQtyReadonly ? 'readonly style="background-color: #f3f4f6;"' : ''}>
                                    <span class="input-group-text">${item.satuan}</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control handler-nominal text-end" value="${formatNumberIndonesian(nominalVal)}" readonly style="background-color: #f3f4f6;">
                                </div>
                            </td>
                        `;
                        handlerTbody.appendChild(tr);
                        if (!isQtyReadonly) {
                            // Only setup qty listener for handler (price is synced from normal, not user-typed)
                            const qtyInput = tr.querySelector('.handler-qty');
                            const nominalInput = tr.querySelector('.handler-nominal');
                            if (qtyInput && nominalInput) {
                                qtyInput.addEventListener('keypress', function(e) {
                                    if (e.key === '.') {
                                        e.preventDefault();
                                        const start = this.selectionStart;
                                        const end = this.selectionEnd;
                                        const text = this.value;
                                        this.value = text.substring(0, start) + ',' + text.substring(end);
                                        this.selectionStart = this.selectionEnd = start + 1;
                                        this.dispatchEvent(new Event('input'));
                                    }
                                });
                                qtyInput.addEventListener('input', function() {
                                    formatInputDecimal(this);
                                    const handlerHarga = tr.querySelector('.handler-harga');
                                    const rawHarga = handlerHarga ? handlerHarga.value.replace(/\./g, '') : '0';
                                    const harga = parseFloat(rawHarga) || 0;
                                    const qty = parseFormattedDecimal(this.value);
                                    const total = harga * qty;
                                    nominalInput.value = new Intl.NumberFormat('id-ID').format(Math.round(total));
                                    calculateShortageTotal();
                                });
                                qtyInput.addEventListener('focus', function() {
                                    if (this.value === '0') this.value = '';
                                });
                                qtyInput.addEventListener('blur', function() {
                                    if (this.value === '') {
                                        this.value = '0';
                                        // Recalculate
                                        qtyInput.dispatchEvent(new Event('input'));
                                    }
                                });
                            }
                        }
                    });
                }

                tableCont.style.display = 'block';
                calculateShortageTotal();
                
            } catch (error) {
                console.error('Error loading shortage details:', error);
            }
        });

        function calculateShortageTotal() {
            let total = 0;
            // Sum newly purchased normal shortages
            document.querySelectorAll('#shortage-table-body tr').forEach(row => {
                if (row.dataset.alreadyPurchased !== 'true') {
                    const nominalInput = row.querySelector('.shortage-nominal');
                    if (nominalInput) {
                        const rawVal = nominalInput.value.replace(/\./g, "");
                        total += parseFloat(rawVal) || 0;
                    }
                }
            });
            // Sum newly purchased handler shortages
            document.querySelectorAll('#handler-table-body tr').forEach(row => {
                if (row.dataset.alreadyPurchased !== 'true') {
                    const nominalInput = row.querySelector('.handler-nominal');
                    if (nominalInput) {
                        const rawVal = nominalInput.value.replace(/\./g, "");
                        total += parseFloat(rawVal) || 0;
                    }
                }
            });

            // Calculate total for Table 1 display (all items in Table 1)
            let table1Total = 0;
            document.querySelectorAll('#shortage-table-body tr').forEach(row => {
                const nominalInput = row.querySelector('.shortage-nominal');
                if (nominalInput) {
                    const rawVal = nominalInput.value.replace(/\./g, "");
                    table1Total += parseFloat(rawVal) || 0;
                }
            });
            document.getElementById('shortage-total-nominal').textContent = formatIDR(table1Total);

            // Calculate total for Table 2 display (all items in Table 2)
            let table2Total = 0;
            let table2Pengajuan = 0;
            document.querySelectorAll('#handler-table-body tr').forEach(row => {
                const nominalInput = row.querySelector('.handler-nominal');
                if (nominalInput) {
                    const rawVal = nominalInput.value.replace(/\./g, "");
                    const val = parseFloat(rawVal) || 0;
                    table2Total += val;
                    if (row.dataset.alreadyPurchased !== 'true') {
                        table2Pengajuan += val;
                    }
                }
            });
            const handlerTotalNominalEl = document.getElementById('handler-total-nominal');
            if (handlerTotalNominalEl) {
                handlerTotalNominalEl.textContent = formatIDR(table2Total);
            }
            const handlerTotalPengajuanEl = document.getElementById('handler-total-pengajuan');
            if (handlerTotalPengajuanEl) {
                handlerTotalPengajuanEl.textContent = formatIDR(table2Pengajuan);
            }

            // Assign to Debit or Credit based on category type
            const opt = document.getElementById('category_select').options[document.getElementById('category_select').selectedIndex];
            const type = opt ? opt.dataset.type : '';
            
            const formattedTotal = new Intl.NumberFormat('id-ID').format(total);
            
            if (type === 'income') {
                document.getElementById('debit-field').value = formattedTotal;
                document.getElementById('credit-field').value = 0;
            } else if (type === 'expense') {
                document.getElementById('credit-field').value = formattedTotal;
                document.getElementById('debit-field').value = 0;
            }
            
            calculateAfter();
        }

        document.getElementById('period_select').addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const hint = document.getElementById('period-range-hint');
            const periodId = this.value;
            if (opt && opt.dataset.start) {
                const fmt = d => new Date(d).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
                hint.textContent = 'Rentang: ' + fmt(opt.dataset.start) + ' – ' + fmt(opt.dataset.end);
                document.getElementById('trx-date').min = opt.dataset.start;
                document.getElementById('trx-date').max = opt.dataset.end;
            } else {
                hint.textContent = '';
            }
            updateBalancePreview();
            
            // Reload shortages if category is pembelian bahan baku
            const catSelect = document.getElementById('category_select');
            const catOpt = catSelect.options[catSelect.selectedIndex];
            const isPembelian = catOpt && catOpt.dataset.isPembelian === '1';
            
            if (isPembelian) {
                if (periodId) {
                    loadPendingShortages();
                    document.getElementById('shortage-table-container').style.display = 'none';
                    document.getElementById('shortage-table-body').innerHTML = '';
                    document.getElementById('handler-table-container').style.display = 'none';
                    document.getElementById('handler-table-body').innerHTML = '';
                    calculateShortageTotal();
                } else {
                    showWarningModal('Periode dikosongkan. Kategori Pembelian Bahan Baku direset.');
                    catSelect.value = '';
                    catSelect.dispatchEvent(new Event('change'));
                }
            }
        });

        document.querySelector('select[name="cash_account_id"]').addEventListener('change', updateBalancePreview);

        const debitField = document.getElementById('debit-field');
        const creditField = document.getElementById('credit-field');

        debitField.addEventListener('input', function() {
            formatInputThousands(this);
            calculateAfter();
        });
        debitField.addEventListener('focus', function() {
            if (this.value === '0') this.value = '';
        });
        debitField.addEventListener('blur', function() {
            if (this.value === '') {
                this.value = '0';
                calculateAfter();
            }
        });

        creditField.addEventListener('input', function() {
            formatInputThousands(this);
            calculateAfter();
        });
        creditField.addEventListener('focus', function() {
            if (this.value === '0') this.value = '';
        });
        creditField.addEventListener('blur', function() {
            if (this.value === '') {
                this.value = '0';
                calculateAfter();
            }
        });

        // Trigger on load
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('period_select').dispatchEvent(new Event('change'));
            document.getElementById('category_select').dispatchEvent(new Event('change'));
        });
        // Intercept form submit to collect shortage data as JSON
        document.getElementById('trx-form').addEventListener('submit', function(e) {
            // Remove dots from debit & credit fields so Laravel's validation accepts it as numeric
            const dField = document.getElementById('debit-field');
            const cField = document.getElementById('credit-field');
            dField.value = dField.value.replace(/\./g, "");
            cField.value = cField.value.replace(/\./g, "");

            const shortages = [];

            // Collect normal shortages (if not already purchased)
            document.querySelectorAll('#shortage-table-body tr').forEach(row => {
                if (row.dataset.alreadyPurchased !== 'true') {
                    const laporanId = row.dataset.laporanId;
                    const hargaInput = row.querySelector('.shortage-harga');
                    const qtyInput = row.querySelector('.shortage-qty');
                    const nominalInput = row.querySelector('.shortage-nominal');
                    if (laporanId && qtyInput && nominalInput) {
                        const rawHarga = hargaInput ? hargaInput.value.replace(/\./g, "") : 0;
                        const rawNominal = nominalInput.value.replace(/\./g, "");
                        const rawQty = parseFormattedDecimal(qtyInput.value);
                        shortages.push({
                            laporan_id: laporanId,
                            harga_satuan: rawHarga,
                            qty: rawQty,
                            nominal: rawNominal
                        });
                    }
                }
            });

            // Collect handler shortages (if not already purchased)
            document.querySelectorAll('#handler-table-body tr').forEach(row => {
                if (row.dataset.alreadyPurchased !== 'true') {
                    const laporanId = row.dataset.laporanId;
                    const hargaInput = row.querySelector('.handler-harga');
                    const qtyInput = row.querySelector('.handler-qty');
                    const nominalInput = row.querySelector('.handler-nominal');
                    if (laporanId && qtyInput && nominalInput) {
                        const rawHarga = hargaInput ? hargaInput.value.replace(/\./g, "") : 0;
                        const rawNominal = nominalInput.value.replace(/\./g, "");
                        const rawQty = parseFormattedDecimal(qtyInput.value);
                        shortages.push({
                            laporan_id: laporanId,
                            harga_satuan: rawHarga,
                            qty: rawQty,
                            nominal: rawNominal
                        });
                    }
                }
            });

            if (shortages.length > 0) {
                document.getElementById('shortages_json').value = JSON.stringify(shortages);
            } else {
                document.getElementById('shortages_json').value = "[]";
            }
        });
    </script>
@endsection
