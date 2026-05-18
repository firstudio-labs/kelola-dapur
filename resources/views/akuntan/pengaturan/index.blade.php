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
                <span class="text-dark small">Pengaturan</span>
            </nav>
            <h4 class="fw-bold mb-1">Pengaturan</h4>
            <p class="mb-0 text-muted small">Kelola identitas lembaga, periode, kategori, dan akun kas</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-settings" role="tab" aria-selected="true">
                        <i class="bx bx-building me-1"></i> Identitas Lembaga
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-periode" role="tab" aria-selected="false">
                        <i class="bx bx-calendar me-1"></i> Periode Akuntansi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-kategori" role="tab" aria-selected="false">
                        <i class="bx bx-category me-1"></i> Kategori Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-kas" role="tab" aria-selected="false">
                        <i class="bx bx-wallet me-1"></i> Akun Kas
                    </a>
                </li>
            </ul>
            
            <div class="tab-content bg-transparent shadow-none p-0">

                {{-- ── Identitas Lembaga ── --}}
                <div class="tab-pane fade show active" id="tab-settings" role="tabpanel">
                    <div class="card mb-4">
                        <h5 class="card-header border-bottom mb-3">Informasi Identitas Lembaga</h5>
                        <div class="card-body">
                            @php
                                $dapurAddress = collect([
                                    $dapur->alamat,
                                    $dapur->village_name ? "Ds/Kel. " . $dapur->village_name : null,
                                    $dapur->district_name ? "Kec. " . $dapur->district_name : null,
                                    $dapur->regency_name,
                                    $dapur->province_name ? "Prov. " . $dapur->province_name : null
                                ])->filter()->implode(', ');
                            @endphp
                            <div class="alert alert-info d-flex mb-4">
                                <i class="bx bx-info-circle me-2 mt-1"></i>
                                <span>Informasi <strong>Nama Lembaga</strong> dan <strong>Alamat</strong> disinkronisasi dari profil Dapur Utama. Lengkapi informasi pendukung lainnya di bawah.</span>
                            </div>
                            <form action="{{ route('akuntan.pengaturan.settings.update') }}" method="POST">
                                @csrf @method('PUT')
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nama Lembaga <span class="badge bg-label-primary px-2 py-1 ms-1" style="font-size:0.65rem;">Data Terpusat</span></label>
                                        <input type="text" class="form-control bg-lighter text-muted cursor-not-allowed" value="{{ $dapur->nama_dapur }}" readonly>
                                        <input type="hidden" name="institution_name" value="{{ $dapur->nama_dapur }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nama Yayasan / Mitra</label>
                                        <input type="text" name="foundation_name" class="form-control" value="{{ old('foundation_name', $setting->foundation_name) }}" placeholder="Contoh: Yayasan Al-Ihsan">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Alamat Lengkap <span class="badge bg-label-primary px-2 py-1 ms-1" style="font-size:0.65rem;">Data Terpusat</span></label>
                                        <textarea class="form-control bg-lighter text-muted cursor-not-allowed" rows="2" readonly>{{ $dapurAddress }}</textarea>
                                        <input type="hidden" name="address" value="{{ $dapurAddress }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nama Kepala Lembaga</label>
                                        <input type="text" name="head_name" class="form-control" value="{{ old('head_name', $setting->head_name) }}" placeholder="Nama beserta gelar">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nama Ketua Yayasan</label>
                                        <input type="text" name="foundation_head" class="form-control" value="{{ old('foundation_head', $setting->foundation_head) }}" placeholder="Nama beserta gelar">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nama Bendahara</label>
                                        <input type="text" name="treasurer_name" class="form-control" value="{{ old('treasurer_name', $setting->treasurer_name) }}" placeholder="Nama bendahara">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">No. Rekening Bank</label>
                                        <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account', $setting->bank_account) }}" placeholder="BSI 123456789 a.n. Lembaga">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Lokasi Penandatanganan Laporan</label>
                                        <input type="text" name="report_location" class="form-control" value="{{ old('report_location', $setting->report_location) }}" placeholder="Contoh: Jakarta">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Tanggal Default Laporan</label>
                                        <input type="date" name="report_date" class="form-control" value="{{ old('report_date', $setting->report_date ? $setting->report_date->format('Y-m-d') : '') }}">
                                    </div>
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan Perubahan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ── Periode Akuntansi ── --}}
                <div class="tab-pane fade" id="tab-periode" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-header border-bottom mb-3">
                            <h5 class="mb-0">Daftar Periode Akuntansi</h5>
                        </div>

                        <div class="card-body">

                            {{-- Banner: ada periode open --}}
                            @if(!$canCreateNewPeriod && $openPeriod)
                            <div class="alert alert-warning border-warning mb-4">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bx bx-info-circle fs-5 mt-1 text-warning"></i>
                                    <div>
                                        <div class="fw-semibold">Periode Aktif Sedang Berjalan</div>
                                        <div class="small">Periode <strong>"{{ $openPeriod->name }}"</strong> masih berstatus <span class="badge bg-label-success">Buka Transaksi</span>. Tutup periode ini terlebih dahulu untuk dapat membuat periode akuntansi baru.</div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Form Tambah Periode (hanya muncul jika tidak ada periode open) --}}
                            @if($canCreateNewPeriod)
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="bx bx-calendar-plus me-1"></i> Form Tambah Periode Baru</h6>

                                    @if($lastClosedPeriod)
                                    <div class="alert alert-info py-2 px-3 mb-3 small">
                                        <i class="bx bx-transfer me-1"></i>
                                        Saldo awal otomatis diisi dari <strong>saldo akhir periode "{{ $lastClosedPeriod->name }}"</strong>. Anda dapat mengubah nilai ini jika diperlukan.
                                    </div>
                                    @endif

                                    <form action="{{ route('akuntan.pengaturan.periode.store') }}" method="POST">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-12 col-md-4">
                                                <label class="form-label fw-semibold">Nama Periode <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control form-control-sm" required placeholder="Contoh: Triwulan I 2025" value="{{ old('name') }}">
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                                                <input type="number" name="year" class="form-control form-control-sm" required value="{{ old('year', date('Y')) }}" min="2020">
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <label class="form-label fw-semibold">Mulai <span class="text-danger">*</span></label>
                                                <input type="date" name="start_date" class="form-control form-control-sm" required
                                                    value="{{ old('start_date', $lastClosedPeriod ? $lastClosedPeriod->end_date->addDay()->format('Y-m-d') : '') }}">
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <label class="form-label fw-semibold">Selesai <span class="text-danger">*</span></label>
                                                <input type="date" name="end_date" class="form-control form-control-sm" required value="{{ old('end_date') }}">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold mb-2">
                                                    Saldo Awal per Akun Kas
                                                    @if($lastClosedPeriod)
                                                        <span class="text-muted fw-normal small ms-1">— dari penutupan periode sebelumnya, dapat diubah</span>
                                                    @else
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <div class="row g-2">
                                                    @foreach($cashAccounts as $ca)
                                                    @php
                                                        $suggested = $suggestedOpeningBalances[$ca->id] ?? 0;
                                                        $inputVal  = old("opening_balances.{$ca->id}", $suggested);
                                                    @endphp
                                                    <div class="col-md-4">
                                                        <div class="card border shadow-none">
                                                            <div class="card-body py-2 px-3">
                                                                <div class="d-flex align-items-center mb-1 gap-2">
                                                                    <i class="bx {{ $ca->type === 'tunai' ? 'bx-money text-success' : 'bx-credit-card text-info' }}"></i>
                                                                    <span class="fw-semibold small text-truncate" title="{{ $ca->name }}">{{ $ca->name }}</span>
                                                                    <span class="badge bg-label-{{ $ca->type === 'tunai' ? 'success' : 'info' }} ms-auto">{{ $ca->type_label }}</span>
                                                                </div>
                                                                @if($lastClosedPeriod)
                                                                <div class="text-muted small mb-1">
                                                                    Saldo akhir periode lalu: <strong>Rp {{ number_format($suggested, 0, ',', '.') }}</strong>
                                                                </div>
                                                                @endif
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">Rp</span>
                                                                    <input type="number" name="opening_balances[{{ $ca->id }}]"
                                                                           class="form-control form-control-sm opening-balance-input-create"
                                                                           value="{{ (int) $inputVal }}" min="{{ (int)$suggested }}" step="1"
                                                                           onfocus="if(this.value == '0') this.value='';"
                                                                           onblur="if(this.value == '') { this.value='0'; calculateTotalOpening(); }"
                                                                           oninvalid="this.setCustomValidity('Saldo tidak boleh kurang dari saldo akhir sebelumnya (Rp {{ number_format($suggested, 0, ',', '.') }})')"
                                                                           oninput="this.setCustomValidity(''); calculateTotalOpening()">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                    @if($cashAccounts->isEmpty())
                                                    <div class="col-12 text-muted small">
                                                        <i class="bx bx-info-circle me-1"></i> Belum ada akun kas. Tambahkan akun kas di tab <strong>Akun Kas</strong> terlebih dahulu.
                                                    </div>
                                                    @endif
                                                </div>
                                                
                                                @if($cashAccounts->isNotEmpty())
                                                <div class="mt-3 p-2 bg-white border rounded d-flex justify-content-between align-items-center">
                                                    <div class="d-flex flex-column">
                                                        <strong class="text-secondary small">Total Saldo Awal (Estimasi)</strong>
                                                        <small class="text-muted" style="font-size: 0.7rem;">Akumulasi dari input per akun kas di atas</small>
                                                    </div>
                                                    <strong class="text-primary" id="total_opening_display_create">Rp 0</strong>
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-save me-1"></i> Simpan Periode</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif

                            {{-- Tabel Daftar Periode --}}
                            <div class="table-responsive">
                                <table class="table table-hover border align-middle small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Periode</th>
                                            <th>Tahun</th>
                                            <th>Rentang Waktu</th>
                                            <th>Total Saldo Awal</th>
                                            <th>Saldo per Akun Kas</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($periods as $p)
                                        @php
                                            $pBalances = $p->balances->keyBy('cash_account_id');
                                            $totalOpening = $pBalances->sum('opening_balance');
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold">{{ $p->name }}</td>
                                            <td>{{ $p->year }}</td>
                                            <td><span class="text-muted small"><i class="bx bx-calendar-event me-1"></i>{{ $p->start_date->format('d M Y') }} – {{ $p->end_date->format('d M Y') }}</span></td>
                                            <td><strong class="text-primary">Rp {{ number_format($totalOpening, 0, ',', '.') }}</strong></td>
                                            <td>
                                                @if($p->status === 'closed')
                                                    @foreach($cashAccounts as $ca)
                                                    @php $bal = $pBalances->get($ca->id); @endphp
                                                    <div class="small mb-1 border-bottom pb-1">
                                                        <span class="text-dark fw-semibold">{{ $ca->name }}</span><br>
                                                        <span class="text-muted" style="font-size: 0.75rem;">Awal: Rp {{ number_format($bal?->opening_balance ?? 0, 0, ',', '.') }}</span><br>
                                                        <span class="text-muted" style="font-size: 0.75rem;">Akhir: </span><strong>Rp {{ number_format($bal?->closing_balance ?? 0, 0, ',', '.') }}</strong>
                                                    </div>
                                                    @endforeach
                                                @else
                                                    @foreach($cashAccounts as $ca)
                                                    @php
                                                        $bal     = $pBalances->get($ca->id);
                                                        $opening = (float)($bal?->opening_balance ?? 0);
                                                        $debit   = $p->transactions()->where('cash_account_id', $ca->id)->sum('debit');
                                                        $credit  = $p->transactions()->where('cash_account_id', $ca->id)->sum('credit');
                                                        $running = $opening + $debit - $credit;
                                                    @endphp
                                                    <div class="small mb-1 border-bottom pb-1">
                                                        <span class="text-dark fw-semibold">{{ $ca->name }}</span><br>
                                                        <span class="text-muted" style="font-size: 0.75rem;">Awal: Rp {{ number_format($opening, 0, ',', '.') }}</span><br>
                                                        <span class="text-muted" style="font-size: 0.75rem;">Berjalan: </span><strong class="text-success">Rp {{ number_format($running, 0, ',', '.') }}</strong>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $p->status === 'open' ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                    {{ $p->status === 'open' ? 'Buka Transaksi' : 'Ditutup' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($p->status === 'open')
                                                <form action="{{ route('akuntan.pengaturan.periode.close', $p->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-icon btn-outline-warning"
                                                        onclick="return confirm('Tutup periode ini?\n\nSistem akan menghitung dan menyimpan saldo akhir setiap akun kas secara otomatis.\nSetelah ditutup, tidak bisa menambah transaksi baru pada periode ini.')"
                                                        title="Tutup Periode & Simpan Saldo Akhir">
                                                        <i class="bx bx-lock"></i>
                                                    </button>
                                                </form>
                                                @else
                                                <form action="{{ route('akuntan.pengaturan.periode.open', $p->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-icon btn-outline-success"
                                                        onclick="return confirm('Buka kembali periode ini?\n\nSaldo akhir akan di-reset.\nPastikan tidak ada periode penerus yang sudah berjalan.')"
                                                        title="Buka Kembali Periode">
                                                        <i class="bx bx-lock-open"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                <form action="{{ route('akuntan.pengaturan.periode.destroy', $p->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-icon btn-outline-danger"
                                                        onclick="return confirm('Yakin hapus periode ini?\nSemua data laporan terkait akan terhapus permanen.')"
                                                        title="Hapus">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bx bx-info-circle mb-1 fs-4 d-block"></i>Belum ada periode akuntansi terdaftar.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Kategori Transaksi ── --}}
                <div class="tab-pane fade" id="tab-kategori" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-header border-bottom mb-3">
                            <h5 class="mb-0">Daftar Kategori Transaksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="bx bx-layer-plus me-1"></i> Form Tambah Kategori</h6>
                                    <form action="{{ route('akuntan.pengaturan.kategori.store') }}" method="POST">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-12 col-md-4">
                                                <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control form-control-sm" required placeholder="Contoh: Belanja Bahan Baku">
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                                                <select name="type" class="form-select form-select-sm" required>
                                                    <option value="income">Penerimaan (Masuk)</option>
                                                    <option value="expense">Pengeluaran (Keluar)</option>
                                                </select>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <label class="form-label fw-semibold">Kelompok Laporan <span class="text-danger">*</span></label>
                                                <select name="group" class="form-select form-select-sm" required>
                                                    <option value="dana_bahan_baku">Dana Bahan Baku</option>
                                                    <option value="dana_operasional">Dana Operasional</option>
                                                    <option value="dana_insentif_fasilitas">Dana Insentif Fasilitas</option>
                                                    <option value="pungutan_ppn">Pungutan/Setoran PPN</option>
                                                    <option value="pungutan_pph21">Pungutan/Setoran PPh 21</option>
                                                    <option value="pungutan_pph22">Pungutan/Setoran PPh 22</option>
                                                    <option value="pungutan_pph23">Pungutan/Setoran PPh 23</option>
                                                    <option value="pungutan_pph4">Pungutan/Setoran PPh pasal 4 ayat (2)</option>
                                                    <option value="biaya_bahan_baku">Biaya Bahan Baku</option>
                                                    <option value="biaya_operasional">Biaya Operasional</option>
                                                    <option value="biaya_insentif_fasilitas">Biaya Insentif Fasilitas</option>
                                                </select>
                                            </div>
                                            <div class="col-6 col-md-2 d-flex align-items-end">
                                                <div class="form-check mb-2 ms-2">
                                                    <input type="checkbox" name="is_tax" class="form-check-input" id="is_tax_new" value="1">
                                                    <label class="form-check-label fw-semibold" for="is_tax_new">Akun Pajak?</label>
                                                </div>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-save me-1"></i> Simpan Kategori</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover border">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Kategori</th>
                                            <th>Jenis Arus Kas</th>
                                            <th>Kelompok Laporan</th>
                                            <th class="text-center">Status Pajak</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($categories as $c)
                                        <tr>
                                            <td class="fw-semibold">{{ $c->name }}</td>
                                            <td>
                                                <span class="badge bg-label-{{ $c->type === 'income' ? 'success' : 'danger' }}">
                                                    <i class="bx {{ $c->type === 'income' ? 'bx-down-arrow-circle' : 'bx-up-arrow-circle' }} me-1"></i>
                                                    {{ $c->type_label }}
                                                </span>
                                            </td>
                                            <td><span class="badge bg-label-info">{{ $c->group_label }}</span></td>
                                            <td class="text-center">
                                                @if($c->is_tax)
                                                    <span class="badge bg-label-warning"><i class="bx bx-check"></i> Ya</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editKategoriModal{{ $c->id }}" title="Edit">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                @if($c->is_protected)
                                                    <button class="btn btn-sm btn-icon btn-outline-secondary" disabled title="Kategori Bawaan Sistem (Tidak Dapat Dihapus)">
                                                        <i class="bx bx-trash text-muted"></i>
                                                    </button>
                                                @else
                                                    <form action="{{ route('akuntan.pengaturan.kategori.destroy', $c->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-icon btn-outline-danger" onclick="return confirm('Hapus kategori ini secara permanen?')" title="Hapus"><i class="bx bx-trash"></i></button>
                                                    </form>
                                                @endif

                                                {{-- Modal Edit --}}
                                                <div class="modal fade" id="editKategoriModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Kategori Transaksi</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('akuntan.pengaturan.kategori.update', $c->id) }}" method="POST">
                                                                @csrf @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="row g-3">
                                                                        <div class="col-12">
                                                                            <label class="form-label fw-semibold">Nama Kategori</label>
                                                                            <input type="text" name="name" class="form-control" value="{{ $c->name }}" required>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <label class="form-label fw-semibold">Jenis Arus Kas</label>
                                                                            <select name="type" class="form-select" required {{ $c->is_protected ? 'disabled' : '' }}>
                                                                                <option value="income" {{ $c->type == 'income' ? 'selected' : '' }}>Penerimaan (Masuk)</option>
                                                                                <option value="expense" {{ $c->type == 'expense' ? 'selected' : '' }}>Pengeluaran (Keluar)</option>
                                                                            </select>
                                                                            @if($c->is_protected)
                                                                                <input type="hidden" name="type" value="{{ $c->type }}">
                                                                            @endif
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <label class="form-label fw-semibold">Kelompok Laporan</label>
                                                                            <select name="group" class="form-select" required {{ $c->is_protected ? 'disabled' : '' }}>
                                                                                <option value="dana_bahan_baku" {{ $c->group == 'dana_bahan_baku' ? 'selected' : '' }}>Dana Bahan Baku</option>
                                                                                <option value="dana_operasional" {{ $c->group == 'dana_operasional' ? 'selected' : '' }}>Dana Operasional</option>
                                                                                <option value="dana_insentif_fasilitas" {{ $c->group == 'dana_insentif_fasilitas' ? 'selected' : '' }}>Dana Insentif Fasilitas</option>
                                                                                <option value="pungutan_ppn" {{ $c->group == 'pungutan_ppn' ? 'selected' : '' }}>Pungutan/Setoran PPN</option>
                                                                                <option value="pungutan_pph21" {{ $c->group == 'pungutan_pph21' ? 'selected' : '' }}>Pungutan/Setoran PPh 21</option>
                                                                                <option value="pungutan_pph22" {{ $c->group == 'pungutan_pph22' ? 'selected' : '' }}>Pungutan/Setoran PPh 22</option>
                                                                                <option value="pungutan_pph23" {{ $c->group == 'pungutan_pph23' ? 'selected' : '' }}>Pungutan/Setoran PPh 23</option>
                                                                                <option value="pungutan_pph4" {{ $c->group == 'pungutan_pph4' ? 'selected' : '' }}>Pungutan/Setoran PPh pasal 4 ayat (2)</option>
                                                                                <option value="biaya_bahan_baku" {{ $c->group == 'biaya_bahan_baku' ? 'selected' : '' }}>Biaya Bahan Baku</option>
                                                                                <option value="biaya_operasional" {{ $c->group == 'biaya_operasional' ? 'selected' : '' }}>Biaya Operasional</option>
                                                                                <option value="biaya_insentif_fasilitas" {{ $c->group == 'biaya_insentif_fasilitas' ? 'selected' : '' }}>Biaya Insentif Fasilitas</option>
                                                                            </select>
                                                                            @if($c->is_protected)
                                                                                <input type="hidden" name="group" value="{{ $c->group }}">
                                                                            @endif
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-check">
                                                                                <input type="checkbox" name="is_tax" class="form-check-input" id="is_tax_edit{{ $c->id }}" value="1" {{ $c->is_tax ? 'checked' : '' }} {{ $c->is_protected ? 'disabled' : '' }}>
                                                                                <label class="form-check-label fw-semibold" for="is_tax_edit{{ $c->id }}">Kategori Pajak?</label>
                                                                            </div>
                                                                            @if($c->is_protected && $c->is_tax)
                                                                                <input type="hidden" name="is_tax" value="1">
                                                                            @endif
                                                                        </div>
                                                                        @if($c->is_protected)
                                                                            <div class="col-12">
                                                                                <div class="alert alert-info py-2 px-3 mb-0 small">
                                                                                    <i class="bx bx-info-circle me-1"></i>
                                                                                    Kategori bawaan sistem. Hanya dapat mengubah Nama Kategori.
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bx bx-info-circle mb-1 fs-4 d-block"></i>Belum ada kategori yang ditambahkan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Akun Kas ── --}}
                <div class="tab-pane fade" id="tab-kas" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-header border-bottom mb-3">
                            <h5 class="mb-0">Daftar Akun Kas</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="bx bx-wallet-alt me-1"></i> Form Tambah Akun Kas</h6>
                                    <form action="{{ route('akuntan.pengaturan.kas.store') }}" method="POST">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-12 col-md-5">
                                                <label class="form-label fw-semibold">Nama Akun <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control form-control-sm" required placeholder="Contoh: Kas Tunai Utama">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <label class="form-label fw-semibold">Tipe Akun <span class="text-danger">*</span></label>
                                                <select name="type" class="form-select form-select-sm" required>
                                                    <option value="tunai">Kas Tunai</option>
                                                    <option value="bank">Kas Bank</option>
                                                </select>
                                            </div>
                                            <div class="col-12 mt-4 text-end">
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-save me-1"></i> Simpan Akun Kas</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover border">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Akun Kas</th>
                                            <th>Tipe Akun</th>
                                            <th class="text-end">Saldo Saat Ini</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($cashAccounts as $ca)
                                        <tr>
                                            <td class="fw-semibold">
                                                <i class="bx {{ $ca->type === 'tunai' ? 'bx-money text-success' : 'bx-credit-card text-info' }} me-2"></i>
                                                {{ $ca->name }}
                                            </td>
                                            <td><span class="badge bg-label-{{ $ca->type === 'tunai' ? 'success' : 'info' }}">{{ $ca->type_label }}</span></td>
                                            <td class="text-end fw-bold">Rp {{ number_format($ca->current_balance, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editKasModal{{ $ca->id }}" title="Edit">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                <form action="{{ route('akuntan.pengaturan.kas.destroy', $ca->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-icon btn-outline-danger" onclick="return confirm('Hapus akun kas ini secara permanen?')" title="Hapus"><i class="bx bx-trash"></i></button>
                                                </form>

                                                {{-- Modal Edit Kas --}}
                                                <div class="modal fade" id="editKasModal{{ $ca->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Akun Kas</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('akuntan.pengaturan.kas.update', $ca->id) }}" method="POST">
                                                                @csrf @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="row g-3">
                                                                        <div class="col-12">
                                                                            <label class="form-label fw-semibold">Nama Akun</label>
                                                                            <input type="text" name="name" class="form-control" value="{{ $ca->name }}" required>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <label class="form-label fw-semibold">Tipe Akun</label>
                                                                            <select name="type" class="form-select" required>
                                                                                <option value="tunai" {{ $ca->type == 'tunai' ? 'selected' : '' }}>Kas Tunai</option>
                                                                                <option value="bank" {{ $ca->type == 'bank' ? 'selected' : '' }}>Kas Bank</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="text-center py-4 text-muted"><i class="bx bx-info-circle mb-1 fs-4 d-block"></i>Belum ada akun kas yang didaftarkan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /tab-content --}}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // To preserve active tab on page reload / redirect
    document.addEventListener("DOMContentLoaded", function () {
        // Calculate initial total
        if(typeof calculateTotalOpening === 'function') {
            calculateTotalOpening();
        }
        // 1. Get from localStorage or URL Hash
        let savedTab = localStorage.getItem('akuntan_pengaturan_tab');
        let hash = window.location.hash;
        let activeTab = hash || savedTab;

        if (activeTab) {
            let tabElement = document.querySelector('.nav-link[href="' + activeTab + '"]');
            if (tabElement) {
                let tab = new bootstrap.Tab(tabElement);
                tab.show();
            }
        }

        // Store active tab to localStorage and Hash
        let tabLinks = document.querySelectorAll('a[data-bs-toggle="tab"]');
        tabLinks.forEach(function (tabLink) {
            tabLink.addEventListener('shown.bs.tab', function (e) {
                let target = e.target.getAttribute("href");
                window.location.hash = target;
                localStorage.setItem('akuntan_pengaturan_tab', target);
            });
        });
    });
</script>
<script>
    function calculateTotalOpening() {
        let inputs = document.querySelectorAll('.opening-balance-input-create');
        let total = 0;
        inputs.forEach(inp => total += parseInt(inp.value) || 0);
        let display = document.getElementById('total_opening_display_create');
        if(display) {
            display.innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
    }
</script>
@endpush
@endsection
