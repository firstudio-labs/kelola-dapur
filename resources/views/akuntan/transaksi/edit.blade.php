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
                    <span class="text-dark small">Edit Transaksi</span>
                </nav>
                <h4 class="fw-bold mb-1">Edit Transaksi</h4>
                <p class="mb-0 text-muted small">Perbarui detail record transaksi keuangan</p>
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
                <form action="{{ route('akuntan.transaksi.update', $transaksi->id) }}" method="POST" id="trx-form">
                    @csrf @method('PUT')
                    @php
                        $isPembelianCategory =
                            $transaksi->category &&
                            str_contains(strtolower($transaksi->category->name), 'pembelian bahan baku');
                    @endphp

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Periode <span class="text-danger">*</span></label>
                            <select name="period_id" class="form-select" required id="period_select"
                                {{ $isPembelianCategory ? 'disabled' : '' }}>
                                <option value="">-- Pilih Periode --</option>
                                @foreach ($periods as $p)
                                    <option value="{{ $p->id }}" data-start="{{ $p->start_date->format('Y-m-d') }}"
                                        data-end="{{ $p->end_date->format('Y-m-d') }}"
                                        {{ old('period_id', $transaksi->period_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->year }})
                                    </option>
                                @endforeach
                            </select>
                            @if ($isPembelianCategory)
                                <input type="hidden" name="period_id" value="{{ $transaksi->period_id }}">
                            @endif
                            <small class="text-muted" id="period-range-hint"></small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control"
                                value="{{ old('date', $transaksi->date->format('Y-m-d')) }}" required id="trx-date">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">No. Bukti</label>
                            <input type="text" name="no_bukti" class="form-control"
                                value="{{ old('no_bukti', $transaksi->no_bukti) }}" placeholder="e.g. BKU-001/2024">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required id="category_select">
                                @if ($isPembelianCategory)
                                    @foreach ($categories as $c)
                                        @if (str_contains(strtolower($c->name), 'pembelian bahan baku'))
                                            <option value="{{ $c->id }}" data-type="{{ $c->type }}"
                                                data-is-pembelian="1"
                                                {{ old('category_id', $transaksi->category_id) == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }} —
                                                ({{ $c->type === 'income' ? 'Penerimaan' : 'Pengeluaran' }})
                                            </option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories->groupBy('group_label') as $groupName => $cats)
                                        <optgroup label="{{ $groupName }}">
                                            @foreach ($cats as $c)
                                                <option value="{{ $c->id }}" data-type="{{ $c->type }}"
                                                    data-is-pembelian="{{ str_contains(strtolower($c->name), 'pembelian bahan baku') ? '1' : '0' }}"
                                                    {{ old('category_id', $transaksi->category_id) == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }} —
                                                    ({{ $c->type === 'income' ? 'Penerimaan' : 'Pengeluaran' }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Uraian <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control"
                                value="{{ old('description', $transaksi->description) }}" required
                                placeholder="Deskripsi transaksi">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Akun Kas <span class="text-danger">*</span></label>
                            <select name="cash_account_id" class="form-select" required>
                                <option value="">-- Pilih Akun Kas --</option>
                                @foreach ($cashAccounts as $ca)
                                    <option value="{{ $ca->id }}"
                                        {{ old('cash_account_id', $transaksi->cash_account_id) == $ca->id ? 'selected' : '' }}>
                                        {{ $ca->name }} ({{ $ca->type_label }})</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($isPembelianCategory && $transaksi->shortages->count() > 0)
                            @php
                                $normalShortages = $transaksi->shortages->filter(
                                    fn($sh) => is_null($sh->laporanKekurangan?->id_handler),
                                );
                                $handlerShortages = $transaksi->shortages->filter(
                                    fn($sh) => !is_null($sh->laporanKekurangan?->id_handler),
                                );
                                $hasHandlerShortages = $handlerShortages->count() > 0;
                            @endphp
                            <div class="col-12" id="shortage-display">
                                <hr class="mb-3">
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Detail Pembelian Kekurangan Stok</label>
                                </div>

                                {{-- Table 1: Normal Shortages --}}
                                @if ($normalShortages->count() > 0)
                                    @if ($hasHandlerShortages)
                                        <div class="fw-bold mb-2 text-secondary small">Detail Kekurangan Stok Utama</div>
                                    @endif
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered table-sm align-middle">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th style="width: 20%;">Bahan Baku</th>
                                                    <th style="width: 14%;">Kekurangan</th>
                                                    <th style="width: 22%;">Harga Satuan</th>
                                                    <th style="width: 22%;">Jml Dibeli</th>
                                                    <th style="width: 22%;">Total (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($normalShortages as $sh)
                                                    <tr>
                                                        <td>{{ $sh->laporanKekurangan->templateItem->nama_bahan ?? '-' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ rtrim(rtrim(number_format($sh->laporanKekurangan->jumlah_kurang ?? 0, 4, ',', '.'), '0'), ',') }}
                                                            {{ $sh->laporanKekurangan->satuan ?? '' }}</td>
                                                        <td class="text-end">Rp
                                                            {{ number_format($sh->harga_satuan, 0, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            {{ rtrim(rtrim(number_format($sh->qty_dibeli ?? 0, 4, ',', '.'), '0'), ',') }}
                                                            {{ $sh->laporanKekurangan->satuan ?? '' }}</td>
                                                        <td class="text-end">Rp
                                                            {{ number_format($sh->nominal, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="4" class="text-end align-middle">Total Nominal Utama
                                                    </th>
                                                    <th class="text-end fw-bold">Rp
                                                        {{ number_format($normalShortages->sum('nominal'), 0, ',', '.') }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif

                                {{-- Table 2: Handler Shortages --}}
                                @if ($hasHandlerShortages)
                                    <div class="fw-bold mb-2 text-secondary small">
                                        <i class="bx bx-transfer me-1"></i>Detail Handler Kekurangan Stok
                                    </div>
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered table-sm align-middle">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th style="width: 20%;">Bahan Baku</th>
                                                    <th style="width: 14%;">Kekurangan</th>
                                                    <th style="width: 22%;">Harga Satuan</th>
                                                    <th style="width: 22%;">Jml Dibeli</th>
                                                    <th style="width: 22%;">Total (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($handlerShortages as $sh)
                                                    <tr>
                                                        <td>{{ $sh->laporanKekurangan->templateItem->nama_bahan ?? '-' }}
                                                            - Handler</td>
                                                        <td class="text-center">
                                                            {{ rtrim(rtrim(number_format($sh->laporanKekurangan->jumlah_kurang ?? 0, 4, ',', '.'), '0'), ',') }}
                                                            {{ $sh->laporanKekurangan->satuan ?? '' }}</td>
                                                        <td class="text-end">Rp
                                                            {{ number_format($sh->harga_satuan, 0, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            {{ rtrim(rtrim(number_format($sh->qty_dibeli ?? 0, 4, ',', '.'), '0'), ',') }}
                                                            {{ $sh->laporanKekurangan->satuan ?? '' }}</td>
                                                        <td class="text-end">Rp
                                                            {{ number_format($sh->nominal, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="4" class="text-end align-middle">Total Nominal Handler</th>
                                                    <th class="text-end fw-bold">Rp {{ number_format($handlerShortages->sum('nominal'), 0, ',', '.') }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif

                                {{-- Combined Total --}}
                                @if ($hasHandlerShortages)
                                @endif

                                <div class="alert alert-info py-2 small">
                                    <i class="bx bx-info-circle me-1"></i> Detail pembelian kekurangan stok tidak dapat
                                    diubah. Untuk perubahan, hapus transaksi ini dan buat ulang.
                                </div>
                            </div>
                        @endif

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
                                        <span class="badge bg-label-primary font-size-xs" id="preview-direction">Update
                                            Otomatis</span>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-4 border-end">
                                            <div class="text-muted small mb-1">Saldo Lainnya</div>
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
                                    value="{{ old('debit', number_format($transaksi->debit, 0, '', '')) }}"{{ $isPembelianCategory ? ' readonly' : '' }}>
                            </div>
                        </div>
                        <div class="col-12 col-md-6" id="credit-container">
                            <label class="form-label fw-semibold">Kredit (Pengeluaran) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="credit" id="credit-field" class="form-control text-end"
                                    value="{{ old('credit', number_format($transaksi->credit, 0, '', '')) }}"{{ $isPembelianCategory ? ' readonly' : '' }}>
                            </div>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Simpan
                                Perubahan</button>
                            <a href="{{ route('akuntan.transaksi.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
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

        async function updateBalancePreview() {
            const periodId = document.getElementById('period_select').value;
            const cashAccountId = document.querySelector('select[name="cash_account_id"]').value;
            const container = document.getElementById('balance-preview-container');

            if (!periodId || !cashAccountId) {
                container.style.display = 'none';
                return;
            }

            try {
                // Pass exclude_id to get the balance WITHOUT this specific transaction
                const response = await fetch(
                    `{{ route('akuntan.transaksi.getBalance') }}?period_id=${periodId}&cash_account_id=${cashAccountId}&exclude_id={{ $transaksi->id }}`
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

        const totalShortageAmount = {{ $transaksi->shortages->sum('nominal') }};

        document.getElementById('category_select').addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (!opt) return;
            const type = opt.dataset.type;
            const isPembelian = opt.dataset.isPembelian === '1';
            const debitCont = document.getElementById('debit-container');
            const creditCont = document.getElementById('credit-container');
            const debitFld = document.getElementById('debit-field');
            const creditFld = document.getElementById('credit-field');

            if (type === 'income') {
                debitCont.style.display = 'block';
                creditCont.style.display = 'none';
                if (isPembelian) {
                    debitFld.value = new Intl.NumberFormat('id-ID').format(totalShortageAmount);
                    debitFld.readOnly = true;
                    creditFld.value = 0;
                    creditFld.readOnly = true;
                } else {
                    creditFld.value = 0;
                    debitFld.readOnly = false;
                }
            } else if (type === 'expense') {
                debitCont.style.display = 'none';
                creditCont.style.display = 'block';
                if (isPembelian) {
                    creditFld.value = new Intl.NumberFormat('id-ID').format(totalShortageAmount);
                    creditFld.readOnly = true;
                    debitFld.value = 0;
                    debitFld.readOnly = true;
                } else {
                    debitFld.value = 0;
                    creditFld.readOnly = false;
                }
            } else {
                debitCont.style.display = 'block';
                creditCont.style.display = 'block';
                debitFld.readOnly = false;
                creditFld.readOnly = false;
            }
            calculateAfter();
        });

        document.getElementById('period_select').addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const hint = document.getElementById('period-range-hint');
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
            formatInputThousands(debitField);
            formatInputThousands(creditField);
            document.getElementById('period_select').dispatchEvent(new Event('change'));
            document.getElementById('category_select').dispatchEvent(new Event('change'));
        });

        // Intercept form submit to remove dots
        document.getElementById('trx-form').addEventListener('submit', function(e) {
            debitField.value = debitField.value.replace(/\./g, "");
            creditField.value = creditField.value.replace(/\./g, "");
        });
    </script>
@endsection
