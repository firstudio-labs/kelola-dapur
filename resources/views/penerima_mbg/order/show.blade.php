@extends('template_penerima_mbg.layout')
@section('title', 'Detail Kiriman')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('penerima-mbg.dashboard') }}" class="text-muted me-2 small">
                                <i class="bx bx-home-alt me-1"></i> Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2 text-muted small"></i>
                            <a href="{{ route('penerima-mbg.history.index') }}" class="text-muted me-2 small">History</a>
                            <i class="bx bx-chevron-right me-2 text-muted small"></i>
                            <span class="text-dark small">Detail Kiriman</span>
                        </nav>
                        <h4 class="mb-1 fw-bold">Detail Kiriman</h4>
                        <p class="mb-0 text-muted small">
                            Informasi lengkap mengenai kiriman menu dan konfirmasi penerimaan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                <i class="bx bx-x-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $distribusi = $detail->orderDistribusi;
            $transaksi = $distribusi?->orderProduksi?->transaksiDapur;
            $dapur = $distribusi?->dapur;
            $menus = $transaksi?->detailTransaksiDapur ?? collect();
            $statusPenerimaan = $detail->status_penerimaan ?? 'menunggu';
            $sudahKonfirmasi = $statusPenerimaan !== 'menunggu';
            $badgeColor = match ($statusPenerimaan) {
                'diterima' => 'success',
                'ditolak' => 'danger',
                default => 'warning',
            };
            $badgeLabel = match ($statusPenerimaan) {
                'diterima' => 'Diterima',
                'ditolak' => 'Tidak Diterima',
                default => 'Menunggu Konfirmasi',
            };
        @endphp

        <div class="row g-4">
            {{-- Informasi Kiriman --}}
            <div class="col-12 col-xl-7">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><i class="bx bx-info-circle me-2 text-primary"></i>Informasi Kiriman</h5>
                        <span class="badge bg-label-{{ $badgeColor }} fs-6">{{ $badgeLabel }}</span>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-muted ps-0" style="width:40%">Dapur SPPG</th>
                                    <td>{{ $dapur?->nama_dapur ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">Tanggal Kirim</th>
                                    <td>
                                        {{ $detail->tanggal_dikirim
                                            ? $detail->tanggal_dikirim->translatedFormat('l, d F Y H:i')
                                            : ($transaksi?->tanggal_transaksi ? \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('l, d F Y') : '—') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">Porsi Besar</th>
                                    <td><strong>{{ $detail->porsi_besar ?? 0 }}</strong> porsi</td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">Porsi Kecil</th>
                                    <td><strong>{{ $detail->porsi_kecil ?? 0 }}</strong> porsi</td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">Catatan Distributor</th>
                                    <td>{{ $detail->catatan ?: '—' }}</td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Menu Makanan --}}
                        @if ($menus->count())
                            <hr>
                            <h6 class="text-muted mb-3"><i class="bx bx-restaurant me-1"></i>Menu Makanan</h6>

                            @php
                                $porsiBesarMenus = $menus->where('tipe_porsi', 'besar');
                                $porsiKecilMenus = $menus->where('tipe_porsi', 'kecil');
                            @endphp

                            @if ($porsiBesarMenus->count() > 0)
                                <div class="mb-3">
                                    <small class="fw-bold text-success d-block mb-1">Porsi Besar :</small>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($porsiBesarMenus as $dtd)
                                            <span class="badge bg-label-success p-2" style="font-size:.85rem;">
                                                {{ $dtd->menuMakanan?->nama_menu ?? '—' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($porsiKecilMenus->count() > 0)
                                <div>
                                    <small class="fw-bold text-warning d-block mb-1">Porsi Kecil :</small>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($porsiKecilMenus as $dtd)
                                            <span class="badge bg-label-warning p-2" style="font-size:.85rem;">
                                                {{ $dtd->menuMakanan?->nama_menu ?? '—' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Dokumentasi dari Distributor --}}
                        @if ($detail->dokumentasi->count())
                            <hr>
                            <h6 class="text-muted mb-3"><i class="bx bx-image me-1"></i>Foto Bukti dari Distributor</h6>
                            <div class="row g-2">
                                @foreach ($detail->dokumentasi as $dok)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ asset('storage/' . $dok->path_gambar) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $dok->path_gambar) }}"
                                                class="img-fluid rounded border" alt="Bukti distributor"
                                                style="height:100px;width:100%;object-fit:cover;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Panel Konfirmasi / Hasil Konfirmasi --}}
            <div class="col-12 col-xl-5">
                @if ($sudahKonfirmasi)
                    {{-- Tampilkan hasil konfirmasi --}}
                    <div class="card border-{{ $badgeColor }} border-2 h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i
                                    class="bx bx-{{ $statusPenerimaan === 'diterima' ? 'check-circle text-success' : 'x-circle text-danger' }} me-2"></i>
                                Konfirmasi Penerimaan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-{{ $badgeColor }} mb-3">
                                <strong>Status:</strong> {{ $badgeLabel }}
                            </div>

                            <table class="table table-borderless table-sm mb-3">
                                <tbody>
                                    <tr>
                                        <th class="text-muted ps-0" style="width:55%">Porsi Besar Dikirim</th>
                                        <td>{{ $detail->porsi_besar ?? 0 }} porsi</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted ps-0">Porsi Kecil Dikirim</th>
                                        <td>{{ $detail->porsi_kecil ?? 0 }} porsi</td>
                                    </tr>
                                    <tr class="table-{{ $badgeColor }} fw-semibold">
                                        <th class="ps-0">Porsi Besar Diterima</th>
                                        <td>{{ $detail->porsi_besar_diterima ?? '—' }}
                                            {{ $detail->porsi_besar_diterima !== null ? 'porsi' : '' }}</td>
                                    </tr>
                                    <tr class="table-{{ $badgeColor }} fw-semibold">
                                        <th class="ps-0">Porsi Kecil Diterima</th>
                                        <td>{{ $detail->porsi_kecil_diterima ?? '—' }}
                                            {{ $detail->porsi_kecil_diterima !== null ? 'porsi' : '' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            @if ($detail->ulasan)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Ulasan</label>
                                    <p class="mb-0">{{ $detail->ulasan }}</p>
                                </div>
                            @endif

                            @if ($detail->penerimaanFoto->count())
                                <label class="form-label text-muted">Foto Bukti Penerimaan</label>
                                <div class="row g-2">
                                    @foreach ($detail->penerimaanFoto as $foto)
                                        <div class="col-6">
                                            <a href="{{ asset('storage/' . $foto->path_foto) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $foto->path_foto) }}"
                                                    class="img-fluid rounded border" alt="Foto penerimaan"
                                                    style="height:110px;width:100%;object-fit:cover;">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Form Konfirmasi --}}
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-check-double me-2 text-warning"></i>Konfirmasi Penerimaan
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Unggah foto bukti penerimaan dan pilih status apakah kiriman sudah diterima atau tidak.
                            </p>

                            <form action="{{ route('penerima-mbg.history.store', $detail->id_detail) }}" method="POST"
                                enctype="multipart/form-data" id="formKonfirmasi">
                                @csrf

                                {{-- Porsi Diterima --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Porsi yang Diterima</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label text-muted small mb-1">Porsi Besar</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="porsi_besar_diterima" id="porsiBesarDiterima"
                                                    class="form-control @error('porsi_besar_diterima') is-invalid @enderror"
                                                    value="{{ old('porsi_besar_diterima', $detail->porsi_besar ?? 0) }}"
                                                    min="0" max="{{ $detail->porsi_besar ?? 0 }}">
                                                <span class="input-group-text">porsi</span>
                                            </div>
                                            @error('porsi_besar_diterima')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text text-muted">Maks. {{ $detail->porsi_besar ?? 0 }}</div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-muted small mb-1">Porsi Kecil</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="porsi_kecil_diterima" id="porsiKecilDiterima"
                                                    class="form-control @error('porsi_kecil_diterima') is-invalid @enderror"
                                                    value="{{ old('porsi_kecil_diterima', $detail->porsi_kecil ?? 0) }}"
                                                    min="0" max="{{ $detail->porsi_kecil ?? 0 }}">
                                                <span class="input-group-text">porsi</span>
                                            </div>
                                            @error('porsi_kecil_diterima')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text text-muted">Maks. {{ $detail->porsi_kecil ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Upload Foto --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Foto Bukti <span class="text-danger">*</span>
                                        <small class="text-muted fw-normal">(min. 1, bisa lebih)</small>
                                    </label>
                                    <input type="file" name="foto[]" id="fotoInput"
                                        class="form-control @error('foto') is-invalid @elseif(old('foto')) is-valid @enderror"
                                        accept="image/jpeg,image/png,image/jpg" multiple required>
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('foto.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted">Format: JPEG, PNG, JPG. Maks. 5 MB per foto.</div>
                                </div>

                                {{-- Preview Foto --}}
                                <div id="fotoPreview" class="row g-2 mb-3" style="display:none!important;"></div>

                                {{-- Ulasan --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        Ulasan <small class="text-muted fw-normal">(opsional)</small>
                                    </label>
                                    <textarea name="ulasan" rows="3" class="form-control @error('ulasan') is-invalid @enderror"
                                        placeholder="Catatan tambahan mengenai penerimaan kiriman ini...">{{ old('ulasan') }}</textarea>
                                    @error('ulasan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="d-grid gap-2">
                                    <button type="submit" id="btnDiterima" class="btn btn-success">
                                        <i class="bx bx-check-circle me-1"></i>Sudah Diterima
                                    </button>
                                    <button type="submit" id="btnDitolak" class="btn btn-outline-danger">
                                        <i class="bx bx-x-circle me-1"></i>Belum / Tidak Diterima
                                    </button>
                                    <input type="hidden" name="status_penerimaan" id="hiddenStatus" value="diterima">
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($sudahKonfirmasi)
            <div class="row mt-4">
                <div class="col-12">
                    {{-- Kritik Section (Matched with Produksi Style) --}}
                    <div class="card mb-4 border shadow-none">
                        <div class="card-header border-bottom py-2">
                            <small class="text-muted fw-bold"><i class="bx bx-message-error me-1"></i>KRITIK &
                                MASUKAN</small>
                        </div>
                        <div class="card-body p-3">
                            @if ($detail->kritik)
                                <div class="row g-3">
                                    <div class="col-md-{{ $detail->kritik_foto ? '8' : '12' }}">
                                        <div class="p-3 bg-lighter rounded border" style="min-height: 80px;">
                                            <p class="mb-0 text-dark small fst-italic">"{{ $detail->kritik }}"</p>
                                        </div>
                                    </div>
                                    @if ($detail->kritik_foto)
                                        <div class="col-md-4">
                                            <a href="{{ asset('storage/' . $detail->kritik_foto) }}" target="_blank"
                                                class="shadow-sm d-block">
                                                <img src="{{ asset('storage/' . $detail->kritik_foto) }}"
                                                    alt="Foto Kritik" class="rounded border w-100"
                                                    style="height: 120px; object-fit: cover;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <form action="{{ route('penerima-mbg.history.submit-kritik', $detail->id_detail) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-uppercase">Berikan kritik atau
                                            masukan Anda</label>
                                        <textarea name="kritik" class="form-control" rows="3"
                                            placeholder="Tuliskan kritik atau masukan yang ingin disampaikan (Kualitas rasa, kemasan, atau pelayanan)..."
                                            required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-uppercase">Foto Pendukung
                                            (Opsional)</label>
                                        <input type="file" name="kritik_foto" class="form-control form-control-sm"
                                            accept="image/*">
                                        <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">Maksimal ukuran
                                            5MB. Foto akan otomatis dikompres.</small>
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-sm fw-bold"><i
                                            class="bx bx-send me-1"></i> Kirim Kritik & Masukan</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('penerima-mbg.history.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i>Kembali ke History
            </a>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const input = document.getElementById('fotoInput');
                const preview = document.getElementById('fotoPreview');

                if (!input || !preview) return;

                input.addEventListener('change', function() {
                    preview.innerHTML = '';

                    if (this.files && this.files.length > 0) {
                        preview.style.removeProperty('display');
                        Array.from(this.files).forEach(function(file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const col = document.createElement('div');
                                col.className = 'col-6 col-md-4';
                                col.innerHTML = `<img src="${e.target.result}"
                                          class="img-fluid rounded border"
                                          style="height:100px;width:100%;object-fit:cover;"
                                          alt="Preview">`;
                                preview.appendChild(col);
                            };
                            reader.readAsDataURL(file);
                        });
                    } else {
                        preview.style.display = 'none';
                    }
                });

                // Capture which button was clicked to set status_penerimaan
                const hidden = document.getElementById('hiddenStatus');
                const btnTerima = document.getElementById('btnDiterima');
                const btnTolak = document.getElementById('btnDitolak');
                if (btnTerima) btnTerima.addEventListener('click', function() {
                    hidden.value = 'diterima';
                });
                if (btnTolak) btnTolak.addEventListener('click', function() {
                    hidden.value = 'ditolak';
                });
            });
        </script>
    @endpush
@endsection
