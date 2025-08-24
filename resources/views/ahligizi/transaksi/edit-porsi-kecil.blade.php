@extends("template_ahli_gizi.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div
                            class="d-flex align-items-center justify-content-between"
                        >
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <span
                                        class="avatar-initial rounded-circle bg-label-warning"
                                    >
                                        <i class="bx bx-bowl-hot"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-1">Input Porsi Kecil</h4>
                                    <p class="mb-0 text-muted">
                                        Paket: {{ $transaksi->nama_paket }} |
                                        Tanggal:
                                        {{ $transaksi->tanggal_transaksi->format("d M Y") }}
                                    </p>
                                </div>
                            </div>
                            <!-- Progress Steps -->
                            <div class="d-flex align-items-center">
                                <div class="step-indicator">
                                    <span class="badge bg-success me-2">1</span>
                                    <span class="badge bg-success me-2">2</span>
                                    <span class="badge bg-primary me-2">3</span>
                                    <span class="badge bg-light text-dark">
                                        4
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Porsi Besar -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-info-circle me-2"></i>
                        <div>
                            <strong>Info Porsi Besar:</strong>
                            @php
                                $porsiBesar = $transaksi
                                    ->detailTransaksiDapur()
                                    ->where("tipe_porsi", "besar")
                                    ->with("menuMakanan")
                                    ->get();
                                $totalPorsiBesar = $porsiBesar->sum("jumlah_porsi");
                            @endphp

                            Total {{ $totalPorsiBesar }} porsi besar telah
                            ditambahkan (
                            @foreach ($porsiBesar as $index => $detail)
                                {{ $detail->menuMakanan->nama_menu }}
                                ({{ $detail->jumlah_porsi }}){{ ! $loop->last ? ", " : "" }}
                            @endforeach

                            )
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Input Porsi Kecil -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <div>
                            <h5 class="mb-0">Menu Porsi Kecil</h5>
                            <small class="text-muted">
                                Porsi kecil bersifat opsional. Anda dapat
                                melewati langkah ini jika tidak diperlukan.
                            </small>
                        </div>
                        <button
                            type="button"
                            class="btn btn-warning"
                            id="addMenuBtn"
                        >
                            <i class="bx bx-plus me-1"></i>
                            Tambah Menu
                        </button>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ route("ahli-gizi.transaksi.update-porsi-kecil", $transaksi) }}"
                            method="POST"
                            id="porsiKecilForm"
                        >
                            @csrf
                            @method("PUT")
                            <div id="menuContainer">
                                @if ($porsiKecil->count() > 0)
                                    @foreach ($porsiKecil as $index => $detail)
                                        <div
                                            class="menu-row border rounded p-3 mb-3 border-warning"
                                            data-index="{{ $index }}"
                                        >
                                            <div class="row align-items-end">
                                                <div class="col-md-8">
                                                    <label class="form-label">
                                                        Menu Makanan
                                                    </label>
                                                    <div class="input-group">
                                                        <input
                                                            type="hidden"
                                                            name="menus[{{ $index }}][id_menu]"
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
                                                            class="btn btn-outline-warning"
                                                            onclick="openMenuModal({{ $index }})"
                                                        >
                                                            <i
                                                                class="bx bx-search"
                                                            ></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">
                                                        Jumlah Porsi
                                                    </label>
                                                    <input
                                                        type="number"
                                                        name="menus[{{ $index }}][jumlah_porsi]"
                                                        class="form-control porsi-input"
                                                        min="1"
                                                        max="1000"
                                                        value="{{ $detail->jumlah_porsi }}"
                                                        required
                                                    />
                                                </div>
                                                <div class="col-md-1">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="removeMenuRow(this)"
                                                    >
                                                        <i
                                                            class="bx bx-trash"
                                                        ></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Menu Details -->
                                            <div class="mt-3 menu-details">
                                                @if ($detail->menuMakanan->gambar_url)
                                                    <div
                                                        class="text-center mb-3"
                                                    >
                                                        <img
                                                            src="{{ $detail->menuMakanan->gambar_url }}"
                                                            alt="{{ $detail->menuMakanan->nama_menu }}"
                                                            class="img-fluid rounded"
                                                            style="
                                                                max-width: 200px;
                                                                max-height: 200px;
                                                                object-fit: cover;
                                                            "
                                                        />
                                                    </div>
                                                @else
                                                    <div
                                                        class="text-center mb-3"
                                                    >
                                                        <div
                                                            class="avatar avatar-lg mx-auto"
                                                        >
                                                            <span
                                                                class="avatar-initial rounded bg-label-warning"
                                                            >
                                                                <i
                                                                    class="bx bx-bowl-hot"
                                                                ></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                                <h6 class="text-muted">
                                                    Bahan yang Dibutuhkan:
                                                </h6>
                                                <div class="table-responsive">
                                                    <table
                                                        class="table table-sm"
                                                    >
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    Nama Bahan
                                                                </th>
                                                                <th>
                                                                    Per Porsi
                                                                </th>
                                                                <th>
                                                                    Total
                                                                    Kebutuhan
                                                                </th>
                                                                <th>Satuan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($detail->menuMakanan->bahanMenu as $bahan)
                                                                <tr
                                                                    data-jumlah-per-porsi="{{ $bahan->jumlah_per_porsi }}"
                                                                >
                                                                    <td>
                                                                        {{ $bahan->templateItem->nama_bahan ?? "Bahan Tidak Diketahui" }}
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            $satuan = isset($bahan->templateItem->satuan) ? strtolower($bahan->templateItem->satuan) : "";
                                                                            $jumlah = $bahan->jumlah_per_porsi ?? 0;
                                                                            $displayUnit = $satuan;

                                                                            // Convert to display unit for per portion
                                                                            if ($satuan === "kg") {
                                                                                $jumlah = $jumlah * 1000;
                                                                                $displayUnit = "gram";
                                                                            } elseif ($satuan === "liter" || $satuan === "l") {
                                                                                $jumlah = $jumlah * 1000;
                                                                                $displayUnit = "ml";
                                                                            }

                                                                            // Format jumlah
                                                                            $formattedJumlah = rtrim(rtrim(number_format($jumlah, 4, ".", ""), "0"), ".");

                                                                            if ($bahan->is_bahan_basah) {
                                                                                // Calculate final weight with 7% increase
                                                                                $finalJumlah = $jumlah * 1.07;
                                                                                $formattedFinalJumlah = rtrim(rtrim(number_format($finalJumlah, 4, ".", ""), "0"), ".");
                                                                                echo $formattedJumlah . " " . $displayUnit . " (" . $formattedFinalJumlah . " " . $displayUnit . " Bahan Basah)";
                                                                            } else {
                                                                                echo $formattedJumlah . " " . $displayUnit;
                                                                            }
                                                                        @endphp
                                                                    </td>
                                                                    <td
                                                                        class="total-kebutuhan"
                                                                    >
                                                                        @php
                                                                            $jumlahTotal = ($bahan->jumlah_per_porsi ?? 0) * $detail->jumlah_porsi;
                                                                            $displayTotalUnit = $satuan;

                                                                            // Use original unit for total
                                                                            $formattedTotalJumlah = rtrim(rtrim(number_format($jumlahTotal, 4, ".", ""), "0"), ".");

                                                                            if ($bahan->is_bahan_basah) {
                                                                                // Calculate final total weight with 7% increase
                                                                                $finalTotalJumlah = $jumlahTotal * 1.07;
                                                                                $formattedFinalTotalJumlah = rtrim(rtrim(number_format($finalTotalJumlah, 4, ".", ""), "0"), ".");
                                                                                echo $formattedTotalJumlah . " " . $displayTotalUnit . " (" . $formattedFinalTotalJumlah . " " . $displayTotalUnit . " Bahan Basah)";
                                                                            } else {
                                                                                echo $formattedTotalJumlah . " " . $displayTotalUnit;
                                                                            }
                                                                        @endphp
                                                                    </td>
                                                                    <td>
                                                                        {{ $bahan->templateItem->satuan ?? "-" }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div
                                        class="text-center py-4"
                                        id="emptyState"
                                    >
                                        <div
                                            class="avatar avatar-lg mx-auto mb-3"
                                        >
                                            <span
                                                class="avatar-initial rounded bg-label-warning"
                                            >
                                                <i class="bx bx-bowl-hot"></i>
                                            </span>
                                        </div>
                                        <h6 class="mt-2 text-muted">
                                            Belum ada menu porsi kecil yang
                                            ditambahkan
                                        </h6>
                                        <p class="text-muted">
                                            Porsi kecil bersifat opsional. Klik
                                            "Tambah Menu" untuk menambahkan menu
                                            porsi kecil atau langsung lanjutkan
                                            ke preview.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Modal for Menu Selection -->
                            <div
                                class="modal fade"
                                id="menuModal"
                                tabindex="-1"
                                aria-labelledby="menuModalLabel"
                                aria-hidden="true"
                            >
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5
                                                class="modal-title"
                                                id="menuModalLabel"
                                            >
                                                Pilih Menu Makanan Porsi Kecil
                                            </h5>
                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="Close"
                                            ></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="menuSearch"
                                                    placeholder="Cari menu..."
                                                />
                                            </div>
                                            <div
                                                class="row"
                                                id="menuList"
                                            ></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal"
                                            >
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a
                                    href="{{ route("ahli-gizi.transaksi.edit-porsi-besar", $transaksi) }}"
                                    class="btn btn-outline-secondary"
                                >
                                    <i class="bx bx-arrow-back me-1"></i>
                                    Kembali ke Porsi Besar
                                </a>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    id="submitBtn"
                                >
                                    <i class="bx bx-check me-1"></i>
                                    Simpan dan Lanjutkan ke Preview
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="modal fade"
            id="duplicateModal"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Peringatan Duplikat Menu</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Menu ini sudah dipilih dalam porsi ini. Silakan
                            pilih menu lain atau edit menu yang sudah ada.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-primary"
                            data-bs-dismiss="modal"
                        >
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        let currentMenuIndex = {{ $porsiKecil->count() }};
        let selectedMenus = [
            @foreach ($porsiKecil as $detail)
                {{ $detail->id_menu }},
            @endforeach
        ];

        $(document).ready(function () {
            $('#addMenuBtn').on('click', function () {
                openMenuModal(currentMenuIndex);
            });

            $('#menuSearch').on('input', function () {
                fetchMenus($(this).val());
            });

            $(document).on('change', '.porsi-input', function () {
                const row = $(this).closest('.menu-row');
                const index = row.data('index');
                updateTotalKebutuhan(row, index);
            });

            // Update empty state visibility
            updateEmptyState();
        });

        function openMenuModal(index) {
            currentMenuIndex = index;
            $('#menuSearch').val('');
            fetchMenus('');
            $('#menuModal').modal('show');
        }

        function fetchMenus(searchTerm) {
            $.ajax({
                url: '{{ route("ahli-gizi.menu-makanan.active-menus") }}',
                data: { search: searchTerm },
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
                const menuHtml = `
                    <div class="col-md-6 mb-3 menu-item" data-id="${menu.id_menu}" data-name="${menu.nama_menu}">
                        <div class="card menu-card h-100 ${isSelected ? 'border-warning' : ''}" style="cursor: pointer">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    ${
                                        menu.gambar_url
                                            ? `<img src="${menu.gambar_url}" alt="${menu.nama_menu}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;" />`
                                            : `<div class="avatar avatar-lg me-3">
                                                 <span class="avatar-initial rounded bg-label-warning">
                                                     <i class="bx bx-bowl-hot"></i>
                                                 </span>
                                               </div>`
                                    }
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${menu.nama_menu}</h6>
                                        <p class="text-muted small mb-2">${menu.deskripsi ? menu.deskripsi.substring(0, 50) : ''}</p>
                                        <div class="mt-2">
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
                                bahanContainer.append(`
                                    <span class="badge bg-light text-dark me-1">${bahan.nama_bahan}</span>
                                `);
                            } else if (index === 3) {
                                bahanContainer.append(`
                                    <span class="badge bg-light text-dark">+${response.bahan_menu.length - 3} lainnya</span>
                                `);
                            }
                        });
                    } else {
                        bahanContainer.append(`
                            <span class="badge bg-warning text-dark">Tidak ada bahan</span>
                        `);
                    }
                },
                error: function (xhr) {
                    console.error('Error fetching ingredients for menu ' + menuId + ':', xhr.responseJSON || xhr.statusText);
                    const bahanContainer = $(`#bahan-${menuId}`);
                    bahanContainer.empty();
                    bahanContainer.append(`
                        <span class="badge bg-danger text-white">Gagal memuat bahan</span>
                    `);
                },
            });
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
                    const menuRowHtml = `
                        <div class="menu-row border rounded p-3 mb-3 border-warning" data-index="${index}">
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Menu Makanan</label>
                                    <div class="input-group">
                                        <input type="hidden" name="menus[${index}][id_menu]" value="${menu.id_menu}" />
                                        <input type="text" class="form-control menu-display" value="${menu.nama_menu}" readonly />
                                        <button type="button" class="btn btn-outline-warning" onclick="openMenuModal(${index})">
                                            <i class="bx bx-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jumlah Porsi</label>
                                    <input type="number" name="menus[${index}][jumlah_porsi]" class="form-control porsi-input" min="1" max="1000" value="1" required />
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMenuRow(this)">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3 menu-details">
                                ${
                                    menu.gambar
                                        ? `
                                    <div class="text-center mb-3">
                                        <img src="${menu.gambar}" alt="${menu.nama_menu}" class="img-fluid rounded" style="max-width: 200px; max-height: 200px; object-fit: cover;" />
                                    </div>`
                                        : `
                                    <div class="text-center mb-3">
                                        <div class="avatar avatar-lg mx-auto">
                                            <span class="avatar-initial rounded bg-label-warning">
                                                <i class="bx bx-bowl-hot"></i>
                                            </span>
                                        </div>
                                    </div>`
                                }
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
                                            ${menu.bahan_menu
                                                .map((bahan) => {
                                                    let satuan = bahan.satuan ? bahan.satuan.toLowerCase() : '';
                                                    let jumlah = parseFloat(bahan.jumlah_per_porsi) || 0;
                                                    let jumlahTotal = jumlah * 1; // Default to 1 portion
                                                    let displayUnit = satuan;

                                                    // Convert to display unit for per portion
                                                    if (satuan === 'kg') {
                                                        jumlah = jumlah * 1000;
                                                        displayUnit = 'gram';
                                                    } else if (satuan === 'liter' || satuan === 'l') {
                                                        jumlah = jumlah * 1000;
                                                        displayUnit = 'ml';
                                                    }

                                                    // Use original unit for total
                                                    let displayTotalUnit = satuan;

                                                    // Format jumlah
                                                    let formattedJumlah = jumlah.toFixed(4).replace(/\.?0+$/, '');
                                                    let formattedTotalJumlah = jumlahTotal.toFixed(4).replace(/\.?0+$/, '');

                                                    let bahanHtml = '';
                                                    if (bahan.is_bahan_basah) {
                                                        // Calculate final weight with 7% increase
                                                        let finalJumlah = jumlah * 1.07;
                                                        let finalTotalJumlah = jumlahTotal * 1.07;
                                                        let formattedFinalJumlah = finalJumlah.toFixed(4).replace(/\.?0+$/, '');
                                                        let formattedFinalTotalJumlah = finalTotalJumlah.toFixed(4).replace(/\.?0+$/, '');
                                                        bahanHtml = `
                                                            <tr data-jumlah-per-porsi="${bahan.jumlah_per_porsi}">
                                                                <td>${bahan.nama_bahan}</td>
                                                                <td>${formattedJumlah} ${displayUnit} (${formattedFinalJumlah} ${displayUnit} Bahan Basah)</td>
                                                                <td class="total-kebutuhan">${formattedTotalJumlah} ${displayTotalUnit} (${formattedFinalTotalJumlah} ${displayTotalUnit} Bahan Basah)</td>
                                                                <td>${bahan.satuan}</td>
                                                            </tr>`;
                                                    } else {
                                                        bahanHtml = `
                                                            <tr data-jumlah-per-porsi="${bahan.jumlah_per_porsi}">
                                                                <td>${bahan.nama_bahan}</td>
                                                                <td>${formattedJumlah} ${displayUnit}</td>
                                                                <td class="total-kebutuhan">${formattedTotalJumlah} ${displayTotalUnit}</td>
                                                                <td>${bahan.satuan}</td>
                                                            </tr>`;
                                                    }
                                                    return bahanHtml;
                                                })
                                                .join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>`;

                    const existingRow = $(`.menu-row[data-index="${index}"]`);
                    if (existingRow.length) {
                        existingRow.replaceWith(menuRowHtml);
                        selectedMenus = selectedMenus.filter(
                            (id) => id !== parseInt(existingRow.find('input[name$="[id_menu]"]').val())
                        );
                    } else {
                        $('#menuContainer').append(menuRowHtml);
                        updateEmptyState();
                        currentMenuIndex++;
                    }

                    selectedMenus.push(parseInt(menuId));
                    $('#menuModal').modal('hide');
                },
                error: function (xhr) {
                    alert('Gagal memuat detail menu: ' + (xhr.responseJSON?.message || 'Unknown error'));
                },
            });
        }

        function removeMenuRow(button) {
            const row = $(button).closest('.menu-row');
            const menuId = parseInt(row.find('input[name$="[id_menu]"]').val());
            selectedMenus = selectedMenus.filter((id) => id !== menuId);
            row.remove();
            updateEmptyState();
        }

        function updateTotalKebutuhan(row, index) {
            const porsi = parseInt(row.find('.porsi-input').val()) || 0;
            row.find('tbody tr').each(function () {
                const jumlahPerPorsi = parseFloat($(this).data('jumlah-per-porsi')) || 0;
                let satuan = $(this).find('td:last').text().toLowerCase();
                let jumlahTotal = jumlahPerPorsi * porsi;
                let displayTotalUnit = satuan;

                // Format jumlahTotal
                let formattedTotalJumlah = jumlahTotal.toFixed(4).replace(/\.?0+$/, '');

                if ($(this).find('td:eq(1)').text().includes('Bahan Basah')) {
                    // Calculate final total weight with 7% increase
                    let finalTotalJumlah = jumlahTotal * 1.07;
                    let formattedFinalTotalJumlah = finalTotalJumlah.toFixed(4).replace(/\.?0+$/, '');
                    $(this).find('.total-kebutuhan').text(
                        `${formattedTotalJumlah} ${displayTotalUnit} (${formattedFinalTotalJumlah} ${displayTotalUnit} Bahan Basah)`
                    );
                } else {
                    $(this).find('.total-kebutuhan').text(
                        `${formattedTotalJumlah} ${displayTotalUnit}`
                    );
                }
            });
        }

        function updateEmptyState() {
            if ($('.menu-row').length === 0) {
                $('#emptyState').show();
            } else {
                $('#emptyState').hide();
            }
        }

        $(document).on('click', '.menu-card', function () {
            const menuItem = $(this).closest('.menu-item');
            selectMenu(currentMenuIndex, menuItem.data('id'), menuItem.data('name'));
        });
    </script>
@endpush
