@extends("template_ahli_gizi.layout")

@section("content")
    @php
        if (!function_exists('formatIndonesianNumber')) {
            function formatIndonesianNumber($value) {
                if ($value === null || $value === '' || $value === 0 || $value === 0.0) return '0';
                $num = (float)$value;
                $parts = explode('.', (string)$num);
                $formattedInt = number_format((float)$parts[0], 0, '', '.');
                if (isset($parts[1])) {
                    $decimals = rtrim($parts[1], '0');
                    if (strlen($decimals) > 0) {
                        return $formattedInt . ',' . $decimals;
                    }
                }
                return $formattedInt;
            }
        }
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <i class="bx bx-food-menu"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-1">Input Porsi Besar</h4>
                                    <p class="mb-0 text-muted">
                                        Paket: {{ $transaksi->nama_paket }} |
                                        Tanggal: {{ $transaksi->tanggal_transaksi->format("d M Y") }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="step-indicator">
                                    <span class="badge bg-success me-2">1</span>
                                    <span class="badge bg-primary me-2">2</span>
                                    <span class="badge bg-light text-dark me-2">3</span>
                                    <span class="badge bg-light text-dark">4</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($totalPorsiPenerima > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bx bx-group me-2 fs-5"></i>
                    <div>
                        <strong>Total Porsi Penerima MBG:</strong>
                        <span class="badge bg-info ms-2">{{ formatIndonesianNumber($totalPorsiPenerima) }} Porsi</span>
                        <small class="text-muted ms-2">(dari penerima MBG yang sudah disetujui di dapur ini)</small>
                        &mdash; Jumlah porsi di bawah akan otomatis diisi dengan nilai ini.
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Menu Porsi Besar</h5>
                        <button type="button" class="btn btn-primary" id="addMenuBtn">
                            <i class="bx bx-plus me-1"></i>
                            Tambah Menu
                        </button>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ route("ahli-gizi.transaksi.update-porsi-besar", $transaksi) }}"
                            method="POST"
                            id="porsiBesarForm"
                        >
                            @csrf
                            @method("PUT")

                            <div class="mb-4 p-3 border rounded bg-light">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <label class="form-label fw-semibold mb-0">Jumlah Porsi Besar</label>
                                            @if($totalPorsiPenerima > 0)
                                                <button type="button" class="btn btn-link p-0 ms-2 text-info" data-bs-toggle="modal" data-bs-target="#detailPenerimaModal" title="Detail Penerima">
                                                    <i class="bx bx-info-circle fs-5"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="input-group">
                                            <input
                                                type="number"
                                                name="jumlah_porsi"
                                                id="jumlahPorsiInput"
                                                class="form-control"
                                                min="1"
                                                max="1000000"
                                                value="{{ $porsiBesar->first()?->jumlah_porsi ?? ($totalPorsiPenerima > 0 ? $totalPorsiPenerima : 1) }}"
                                                required
                                            />
                                        </div>
                                        <small class="text-muted">Berlaku untuk semua menu dalam porsi besar</small>
                                    </div>
                                </div>
                            </div>

                            <div id="menuContainer">
                                @if ($porsiBesar->count() > 0)
                                    @foreach ($porsiBesar as $index => $detail)
                                        <div class="menu-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                            <div class="row align-items-end">
                                                <div class="col-md-11">
                                                    <label class="form-label">Menu Makanan</label>
                                                    <div class="input-group">
                                                        <input
                                                            type="hidden"
                                                            name="menus[]"
                                                            value="{{ $detail->id_menu }}"
                                                        />
                                                        <input
                                                            type="text"
                                                            class="form-control menu-display"
                                                            value="{{ $detail->menuMakanan->nama_menu }}"
                                                            readonly
                                                        />
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-primary"
                                                            onclick="openMenuModal({{ $index }})"
                                                        >
                                                            <i class="bx bx-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="removeMenuRow(this)"
                                                    >
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mt-3 menu-details">
                                                @if ($detail->menuMakanan->gambar_url)
                                                    <div class="text-center mb-3">
                                                        <img
                                                            src="{{ $detail->menuMakanan->gambar_url }}"
                                                            alt="{{ $detail->menuMakanan->nama_menu }}"
                                                            class="img-fluid rounded"
                                                            style="max-width: 200px; max-height: 200px; object-fit: cover;"
                                                        />
                                                    </div>
                                                @else
                                                    <div class="text-center mb-3">
                                                        <div class="avatar avatar-lg mx-auto">
                                                            <span class="avatar-initial rounded bg-label-primary">
                                                                <i class="bx bx-food-menu"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                                <h6 class="text-muted">Bahan yang Dibutuhkan:</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Nama Bahan</th>
                                                                <th>Per Porsi</th>
                                                                <th>Total Kebutuhan</th>
                                                                <th>Satuan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($detail->menuMakanan->bahanMenu as $bahan)
                                                                @php
                                                                    $stockItem = $bahan->templateItem->stockItems->first();
                                                                    $konversiNilai = $stockItem?->konversi_nilai;
                                                                    $konversiSatuan = $stockItem?->konversi_satuan;
                                                                    $satuanAsli = strtolower($bahan->templateItem->satuan ?? '');
                                                                    $jumlah = (float) ($bahan->jumlah_per_porsi ?? 0);

                                                                    if ($konversiNilai) {
                                                                        $displayJumlah = $jumlah / $konversiNilai;
                                                                        $displayUnit = $konversiSatuan;
                                                                    } elseif ($satuanAsli === 'kg') {
                                                                        $displayJumlah = $jumlah * 1000;
                                                                        $displayUnit = 'gram';
                                                                    } elseif ($satuanAsli === 'liter' || $satuanAsli === 'l') {
                                                                        $displayJumlah = $jumlah * 1000;
                                                                        $displayUnit = 'ml';
                                                                    } else {
                                                                        $displayJumlah = $jumlah;
                                                                        $displayUnit = $satuanAsli;
                                                                    }
                                                                    $formattedJumlah = formatIndonesianNumber($displayJumlah);

                                                                    $jumlahPorsiShared = $detail->jumlah_porsi;
                                                                    if ($konversiNilai) {
                                                                        $totalDisplay = ($jumlah * $jumlahPorsiShared) / $konversiNilai;
                                                                        $totalUnit = $konversiSatuan;
                                                                    } elseif ($satuanAsli === 'kg') {
                                                                        $totalDisplay = $jumlah * $jumlahPorsiShared * 1000;
                                                                        $totalUnit = 'gram';
                                                                    } elseif ($satuanAsli === 'liter' || $satuanAsli === 'l') {
                                                                        $totalDisplay = $jumlah * $jumlahPorsiShared * 1000;
                                                                        $totalUnit = 'ml';
                                                                    } else {
                                                                        $totalDisplay = $jumlah * $jumlahPorsiShared;
                                                                        $totalUnit = $satuanAsli;
                                                                    }
                                                                    $formattedTotal = formatIndonesianNumber($totalDisplay);

                                                                    $konversiNilaiAttr = $konversiNilai ?? 0;
                                                                    $konversiSatuanAttr = $konversiSatuan ?? '';
                                                                @endphp
                                                                <tr
                                                                    data-jumlah-per-porsi="{{ $bahan->jumlah_per_porsi }}"
                                                                    data-konversi-nilai="{{ $konversiNilaiAttr }}"
                                                                    data-konversi-satuan="{{ $konversiSatuanAttr }}"
                                                                    data-satuan-asli="{{ $satuanAsli }}"
                                                                    data-is-bahan-basah="{{ $bahan->is_bahan_basah ? 1 : 0 }}"
                                                                >
                                                                    <td>{{ $bahan->templateItem->nama_bahan ?? "Bahan Tidak Diketahui" }}</td>
                                                                    <td>
                                                                        @if ($bahan->is_bahan_basah)
                                                                            @php $finalJ = $displayJumlah * 1.07; $formattedFinalJ = formatIndonesianNumber($finalJ); @endphp
                                                                            {{ $formattedJumlah }} {{ $displayUnit }} ({{ $formattedFinalJ }} {{ $displayUnit }} Bahan Basah)
                                                                        @else
                                                                            {{ $formattedJumlah }} {{ $displayUnit }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="total-kebutuhan">
                                                                        @if ($bahan->is_bahan_basah)
                                                                            @php $finalT = $totalDisplay * 1.07; $formattedFinalT = formatIndonesianNumber($finalT); @endphp
                                                                            {{ $formattedTotal }} {{ $totalUnit }} ({{ $formattedFinalT }} {{ $totalUnit }} Bahan Basah)
                                                                        @else
                                                                            {{ $formattedTotal }} {{ $totalUnit }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $displayUnit ?? "-" }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4" id="emptyState">
                                        <div class="avatar avatar-lg mx-auto mb-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="bx bx-food-menu"></i>
                                            </span>
                                        </div>
                                        <h6 class="mt-2 text-muted">Belum ada menu yang ditambahkan</h6>
                                        <p class="text-muted">Klik "Tambah Menu" untuk menambahkan menu porsi besar</p>
                                    </div>
                                @endif
                            </div>
                            <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="menuModalLabel">Pilih Menu Makanan</h5>
                                            <button
                                                type="button"
                                                class="btn btn-sm"
                                                style="background: white; color: black; border: 1px solid #ddd; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                                data-bs-dismiss="modal"
                                                aria-label="Close"
                                            >
                                                <span style="font-size: 18px; font-weight: bold;">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <input type="text" class="form-control" id="menuSearch" placeholder="Cari menu..." />
                                                </div>
                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <select class="form-control" id="kategoriFilter">
                                                        <option value="all">Semua Kategori</option>
                                                        <option value="Karbohidrat">Karbohidrat</option>
                                                        <option value="Lauk">Lauk</option>
                                                        <option value="Sayur">Sayur</option>
                                                        <option value="Tambahan">Tambahan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-control" id="dapurFilter">
                                                        <option value="all">Semua Dapur</option>
                                                        @foreach($dapurs as $d)
                                                            <option value="{{ $d->id_dapur }}" {{ $d->id_dapur == $ahliGizi->id_dapur ? 'selected' : '' }}>
                                                                Dapur: {{ $d->nama_dapur }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row" id="menuList"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route("ahli-gizi.transaksi.index") }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-1"></i>
                                    Kembali
                                </a>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    id="submitBtn"
                                    style="{{ $porsiBesar->count() > 0 ? "" : "display:none" }}"
                                >
                                    <i class="bx bx-check me-1"></i>
                                    Simpan dan Lanjutkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="duplicateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Peringatan Duplikat Menu</h5>
                        <button
                            type="button"
                            class="btn btn-sm"
                            style="background: white; color: black; border: 1px solid #ddd; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <span style="font-size: 18px; font-weight: bold;">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Menu ini sudah dipilih dalam porsi ini. Silakan pilih menu lain atau edit menu yang sudah ada.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        @if($totalPorsiPenerima > 0 && isset($detailPenerima) && $detailPenerima->count() > 0)
        <div class="modal fade" id="detailPenerimaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Kebutuhan Porsi Dapur</h5>
                        <button
                            type="button"
                            class="btn btn-sm"
                            style="background: white; color: black; border: 1px solid #ddd; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <span style="font-size: 18px; font-weight: bold;">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Penerima</th>
                                        <th>Wilayah/Desa</th>
                                        <th>Kebutuhan Porsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detailPenerima as $penerima)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">{{ $penerima->userRole->user->nama ?? $penerima->penanggung_jawab }}</span>
                                            </td>
                                            <td>{{ $penerima->village_name ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-label-info">{{ formatIndonesianNumber($penerima->jumlah_porsi) }} Porsi</span>
                                            </td>
                                            <td>
                                                @if($penerima->link_gmaps)
                                                    <a href="{{ $penerima->link_gmaps }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-map me-1"></i> Google Maps
                                                    </a>
                                                @else
                                                    <span class="text-muted small">Tidak ada link</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $detailPenerima->links() }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push("scripts")
    <script>
        let currentMenuIndex = {{ $porsiBesar->count() }};
        let selectedMenus = [
            @foreach ($porsiBesar as $detail)
                {{ $detail->id_menu }},
            @endforeach
        ];

        $(document).ready(function () {
            $('#addMenuBtn').on('click', function () {
                openMenuModal(currentMenuIndex);
            });

            $('#menuSearch').on('input', function () {
                fetchMenus($(this).val(), $('#kategoriFilter').val());
            });

            $('#kategoriFilter').on('change', function () {
                fetchMenus($('#menuSearch').val(), $(this).val());
            });

            $('#dapurFilter').on('change', function () {
                fetchMenus($('#menuSearch').val(), $('#kategoriFilter').val());
            });

            $('#jumlahPorsiInput').on('input', function () {
                updateAllTotalKebutuhan();
            });

            updateSubmitButton();
        });

        function openMenuModal(index) {
            currentMenuIndex = index;
            $('#menuSearch').val('');
            $('#kategoriFilter').val('all');
            $('#dapurFilter').val('{{ $ahliGizi->id_dapur }}');
            fetchMenus('', 'all', '{{ $ahliGizi->id_dapur }}');
            $('#menuModal').modal('show');
        }

        function fetchMenus(searchTerm, kategori, dapurId) {
            const currentDapurId = dapurId !== undefined ? dapurId : ($('#dapurFilter').val() || 'all');
            $.ajax({
                url: '{{ route("ahli-gizi.menu-makanan.active-menus") }}',
                method: 'GET',
                data: {
                    search: searchTerm || '',
                    kategori: kategori || 'all',
                    dapur_id: currentDapurId
                },
                success: function (response) {
                    renderMenuList(response);
                },
                error: function (xhr) {
                    alert('Gagal memuat menu: ' + (xhr.responseJSON?.message || 'Unknown error'));
                },
            });
        }

        function renderMenuList(menus) {
            const menuList = $('#menuList');
            menuList.empty();
            menus.forEach((menu) => {
                const isSelected = selectedMenus.includes(menu.id_menu);
                const kategoriBadgeClass = {
                    'Karbohidrat': 'bg-label-primary',
                    'Lauk': 'bg-label-success',
                    'Sayur': 'bg-label-info',
                    'Tambahan': 'bg-label-warning',
                }[menu.kategori] || 'bg-label-secondary';

                const isMyDapur = menu.created_by_dapur_id == '{{ $ahliGizi->id_dapur }}';
                const menuHtml = `
                    <div class="col-md-6 mb-3 menu-item" data-id="${menu.id_menu}" data-name="${menu.nama_menu}">
                        <div class="card menu-card h-100 ${isSelected ? 'border-primary' : ''}" style="cursor: pointer">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    ${menu.gambar_url
                                        ? `<img src="${menu.gambar_url}" alt="${menu.nama_menu}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;" />`
                                        : `<div class="avatar avatar-lg me-3"><span class="avatar-initial rounded bg-label-primary"><i class="bx bx-food-menu"></i></span></div>`}
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0">${menu.nama_menu}</h6>
                                            ${menu.kategori ? `<span class="badge ${kategoriBadgeClass}">${menu.kategori}</span>` : ''}
                                        </div>
                                        <p class="text-muted small mb-1">${menu.deskripsi ? menu.deskripsi.substring(0, 50) : ''}</p>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bx bx-store-alt me-1 text-muted" style="font-size:12px"></i>
                                            <small class="text-muted me-1">${menu.nama_dapur || '-'}</small>
                                            ${isMyDapur ? '<span class="badge bg-label-success" style="font-size:10px">Dapur Anda</span>' : ''}
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted">Bahan:</small>
                                            <div class="mt-1" id="bahan-${menu.id_menu}"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                menuList.append(menuHtml);
                fetchMenuBahan(menu.id_menu);
            });
            if (menus.length === 0) {
                menuList.html(`
                    <div class="col-12 text-center py-4">
                        <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-food-menu"></i></span>
                        </div>
                        <p class="text-muted mb-0">Tidak ada menu ditemukan.</p>
                        <small class="text-muted">Coba ubah kata kunci pencarian atau filter dapur.</small>
                    </div>`);
            }
        }

        function fetchMenuBahan(menuId) {
            $.ajax({
                url: '{{ route("ahli-gizi.menu-makanan.api.menu.ingredients", ":id") }}'.replace(':id', menuId),
                method: 'GET',
                success: function (response) {
                    const bahanContainer = $(`#bahan-${menuId}`);
                    bahanContainer.empty();
                    if (response.success && response.bahan_menu && response.bahan_menu.length > 0) {
                        response.bahan_menu.forEach((bahan, index) => {
                            if (index < 3) {
                                bahanContainer.append(`<span class="badge bg-light text-dark me-1">${bahan.nama_bahan}</span>`);
                            } else if (index === 3) {
                                bahanContainer.append(`<span class="badge bg-light text-dark">+${response.bahan_menu.length - 3} lainnya</span>`);
                            }
                        });
                    } else {
                        bahanContainer.append(`<span class="badge bg-warning text-dark">Tidak ada bahan</span>`);
                    }
                },
                error: function () {
                    $(`#bahan-${menuId}`).append(`<span class="badge bg-danger text-white">Gagal memuat bahan</span>`);
                },
            });
        }

        function formatJumlah(jumlah, konversiNilai, konversiSatuan, satuanAsli, isBahanBasah, porsi) {
            let displayJumlah, displayUnit, totalDisplay, totalUnit;

            if (konversiNilai) {
                displayJumlah = jumlah / konversiNilai;
                displayUnit = konversiSatuan;
                totalDisplay = (jumlah * porsi) / konversiNilai;
                totalUnit = konversiSatuan;
            } else if (satuanAsli === 'kg') {
                displayJumlah = jumlah * 1000;
                displayUnit = 'gram';
                totalDisplay = jumlah * porsi * 1000;
                totalUnit = 'gram';
            } else if (satuanAsli === 'liter' || satuanAsli === 'l') {
                displayJumlah = jumlah * 1000;
                displayUnit = 'ml';
                totalDisplay = jumlah * porsi * 1000;
                totalUnit = 'ml';
            } else {
                displayJumlah = jumlah;
                displayUnit = satuanAsli;
                totalDisplay = jumlah * porsi;
                totalUnit = satuanAsli;
            }

            const fmt = (value) => {
                if (value === null || value === undefined || value === '' || value === 0 || value === 0.0) return '0';
                let numStr = parseFloat(value.toFixed(4)).toString();
                let parts = numStr.split('.');
                let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                if (parts[1]) {
                    let decimals = parts[1].replace(/0+$/, '');
                    if (decimals.length > 0) {
                        return integerPart + ',' + decimals;
                    }
                }
                return integerPart;
            };

            let perPorsiText = `${fmt(displayJumlah)} ${displayUnit}`;
            let totalText = `${fmt(totalDisplay)} ${totalUnit}`;

            if (isBahanBasah) {
                perPorsiText += `<br><small class="text-info">(Bahan Matang - ${fmt(displayJumlah * 1.07)} ${displayUnit})</small>`;
                totalText += `<br><small class="text-info">(Bahan Matang - ${fmt(totalDisplay * 1.07)} ${totalUnit})</small>`;
            }

            return { perPorsiText, totalText, finalUnit: displayUnit };
        }

        function selectMenu(index, menuId, menuName) {
            if (selectedMenus.includes(parseInt(menuId))) {
                $('#duplicateModal').modal('show');
                return;
            }

            $.ajax({
                url: '{{ route("ahli-gizi.menu-makanan.menu.detail", ":id") }}'.replace(':id', menuId),
                success: function (response) {
                    if (!response.success) {
                        alert(response.message);
                        return;
                    }

                    const menu = response.menu;
                    const porsi = parseInt($('#jumlahPorsiInput').val()) || 1;

                    const bahanRows = menu.bahan_menu.map((bahan) => {
                        const jumlah = parseFloat(bahan.jumlah_per_porsi) || 0;
                        const satuanAsli = bahan.satuan ? bahan.satuan.toLowerCase() : '';
                        const konversiNilai = bahan.konversi_nilai ? parseFloat(bahan.konversi_nilai) : 0;
                        const konversiSatuan = bahan.konversi_satuan || '';
                        const isBahanBasah = bahan.is_bahan_basah ? 1 : 0;

                        const { perPorsiText, totalText, finalUnit } = formatJumlah(jumlah, konversiNilai, konversiSatuan, satuanAsli, isBahanBasah, porsi);

                        return `<tr data-jumlah-per-porsi="${bahan.jumlah_per_porsi}"
                                    data-konversi-nilai="${konversiNilai}"
                                    data-konversi-satuan="${konversiSatuan}"
                                    data-satuan-asli="${satuanAsli}"
                                    data-is-bahan-basah="${isBahanBasah}">
                                    <td>${bahan.nama_bahan}</td>
                                    <td>${perPorsiText}</td>
                                    <td class="total-kebutuhan">${totalText}</td>
                                    <td>${finalUnit}</td>
                                </tr>`;
                    }).join('');

                    const menuRowHtml = `
                        <div class="menu-row border rounded p-3 mb-3" data-index="${index}">
                            <div class="row align-items-end">
                                <div class="col-md-11">
                                    <label class="form-label">Menu Makanan</label>
                                    <div class="input-group">
                                        <input type="hidden" name="menus[]" value="${menu.id_menu}" />
                                        <input type="text" class="form-control menu-display" value="${menu.nama_menu}" readonly />
                                        <button type="button" class="btn btn-outline-primary" onclick="openMenuModal(${index})">
                                            <i class="bx bx-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMenuRow(this)">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3 menu-details">
                                ${menu.gambar
                                    ? `<div class="text-center mb-3"><img src="${menu.gambar}" alt="${menu.nama_menu}" class="img-fluid rounded" style="max-width: 200px; max-height: 200px; object-fit: cover;" /></div>`
                                    : `<div class="text-center mb-3"><div class="avatar avatar-lg mx-auto"><span class="avatar-initial rounded bg-label-primary"><i class="bx bx-food-menu"></i></span></div></div>`}
                                <h6 class="text-muted">Bahan yang Dibutuhkan:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nama Bahan</th>
                                                <th>Per Porsi</th>
                                                <th>Total Kebutuhan</th>
                                                <th>Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody>${bahanRows}</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>`;

                    const existingRow = $(`.menu-row[data-index="${index}"]`);
                    if (existingRow.length) {
                        const oldMenuId = parseInt(existingRow.find('input[name="menus[]"]').val());
                        selectedMenus = selectedMenus.filter((id) => id !== oldMenuId);
                        existingRow.replaceWith(menuRowHtml);
                    } else {
                        $('#menuContainer').append(menuRowHtml);
                        $('#emptyState').hide();
                        currentMenuIndex++;
                    }

                    selectedMenus.push(parseInt(menuId));
                    updateSubmitButton();
                    $('#menuModal').modal('hide');
                },
                error: function (xhr) {
                    alert('Gagal memuat detail menu: ' + (xhr.responseJSON?.message || 'Unknown error'));
                },
            });
        }

        function removeMenuRow(button) {
            const row = $(button).closest('.menu-row');
            const menuId = parseInt(row.find('input[name="menus[]"]').val());
            selectedMenus = selectedMenus.filter((id) => id !== menuId);
            row.remove();
            updateSubmitButton();
            if ($('.menu-row').length === 0) {
                $('#emptyState').show();
            }
        }

        function updateAllTotalKebutuhan() {
            const porsi = parseInt($('#jumlahPorsiInput').val()) || 0;
            $('.menu-row').each(function () {
                const row = $(this);
                row.find('tbody tr').each(function () {
                    const jumlahPerPorsi = parseFloat($(this).data('jumlah-per-porsi')) || 0;
                    const konversiNilai = parseFloat($(this).data('konversi-nilai')) || 0;
                    const konversiSatuan = $(this).data('konversi-satuan') || '';
                    const satuanAsli = $(this).data('satuan-asli') || '';
                    const isBahanBasah = parseInt($(this).data('is-bahan-basah')) || 0;

                    const { totalText } = formatJumlah(jumlahPerPorsi, konversiNilai, konversiSatuan, satuanAsli, isBahanBasah, porsi);
                    $(this).find('.total-kebutuhan').text(totalText);
                });
            });
        }

        function updateSubmitButton() {
            $('#submitBtn').toggle($('.menu-row').length > 0);
        }

        $(document).on('click', '.menu-card', function () {
            const menuItem = $(this).closest('.menu-item');
            selectMenu(currentMenuIndex, menuItem.data('id'), menuItem.data('name'));
        });
    </script>
@endpush
