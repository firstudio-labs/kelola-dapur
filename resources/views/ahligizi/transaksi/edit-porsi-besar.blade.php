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
                                        class="avatar-initial rounded-circle bg-label-primary"
                                    >
                                        <i class="bx bx-food-menu"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-1">Input Porsi Besar</h4>
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
                                    <span class="badge bg-primary me-2">2</span>
                                    <span class="badge bg-light text-dark me-2">
                                        3
                                    </span>
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

        <!-- Form Input Porsi Besar -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">Menu Porsi Besar</h5>
                        <button
                            type="button"
                            class="btn btn-primary"
                            id="addMenuBtn"
                        >
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
                            <div id="menuContainer">
                                @if ($porsiBesar->count() > 0)
                                    @foreach ($porsiBesar as $index => $detail)
                                        <div
                                            class="menu-row border rounded p-3 mb-3"
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
                                                            class="btn btn-outline-primary"
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
                                                                        {{ $bahan->templateItem->nama_bahan }}
                                                                    </td>
                                                                    <td>
                                                                        {{ $bahan->jumlah_per_porsi }}
                                                                    </td>
                                                                    <td
                                                                        class="total-kebutuhan"
                                                                    >
                                                                        {{ $bahan->jumlah_per_porsi * $detail->jumlah_porsi }}
                                                                    </td>
                                                                    <td>
                                                                        {{ $bahan->templateItem->satuan }}
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
                                                class="avatar-initial rounded bg-label-primary"
                                            >
                                                <i
                                                    class="bx bx-food-menu"
                                                    style="font-size: 2rem"
                                                ></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-2">Belum Ada Menu</h5>
                                        <p class="text-muted">
                                            Klik "Tambah Menu" untuk mulai
                                            menambahkan menu porsi besar.
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <!-- Actions -->
                            <div class="d-flex justify-content-between mt-4">
                                <a
                                    href="{{ route("ahli-gizi.transaksi.index") }}"
                                    class="btn btn-outline-secondary"
                                >
                                    <i class="bx bx-arrow-back me-1"></i>
                                    Kembali
                                </a>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    id="submitBtn"
                                    @if($porsiBesar->count() == 0) style="display: none;" @endif
                                >
                                    <i class="bx bx-check me-1"></i>
                                    Lanjutkan ke Porsi Kecil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Selection Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Menu Makanan</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>
                </div>
                <div class="modal-body">
                    <!-- Search -->
                    <div class="mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="menuSearch"
                            placeholder="Cari menu..."
                        />
                    </div>
                    <!-- Menu List -->
                    <div class="row" id="menuList"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
        });

        let currentMenuIndex = {{ $porsiBesar->count() }};
        let selectedMenus = @json($porsiBesar->pluck("id_menu")->toArray());

        $(document).ready(function () {
            updateSubmitButton();

            // Add menu button click
            $('#addMenuBtn').on('click', function () {
                openMenuModal(currentMenuIndex);
            });

            // Menu search
            $('#menuSearch').on('keyup', function () {
                const searchTerm = $(this).val().toLowerCase();
                fetchMenus(searchTerm);
            });

            // Update total kebutuhan when porsi changes
            $(document).on('input', '.porsi-input', function () {
                const row = $(this).closest('.menu-row');
                const index = row.data('index');
                updateTotalKebutuhan(row, index);
            });
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
                    alert(
                        'Gagal memuat menu: ' + xhr.responseJSON?.message ||
                            'Unknown error',
                    );
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
                        <div class="card menu-card h-100 ${isSelected ? 'border-primary' : ''}" style="cursor: pointer">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    ${
                                        menu.gambar_url
                                            ? `
                                        <img src="${menu.gambar_url}" alt="${menu.nama_menu}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;" />
                                    `
                                            : `
                                        <div class="avatar avatar-lg me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="bx bx-food-menu"></i>
                                            </span>
                                        </div>
                                    `
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
                url: '{{ route("ahli-gizi.menu-makanan.api.menu.ingredients", ":id") }}'.replace(
                    ':id',
                    menuId,
                ),
                method: 'GET',
                success: function (response) {
                    console.log(
                        'Ingredients response for menu ' + menuId + ':',
                        response,
                    ); // Debug log
                    const bahanContainer = $(`#bahan-${menuId}`);
                    bahanContainer.empty(); // Clear existing content
                    if (
                        response.success &&
                        response.bahan_menu &&
                        response.bahan_menu.length > 0
                    ) {
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
                    console.error(
                        'Error fetching ingredients for menu ' + menuId + ':',
                        xhr.responseJSON || xhr.statusText,
                    );
                    const bahanContainer = $(`#bahan-${menuId}`);
                    bahanContainer.empty();
                    bahanContainer.append(`
                <span class="badge bg-danger text-white">Gagal memuat bahan</span>
            `);
                },
            });
        }

        function selectMenu(index, menuId, menuName) {
            if (
                selectedMenus.includes(parseInt(menuId)) &&
                index >= {{ $porsiBesar->count() }}
            ) {
                alert('Menu sudah dipilih!');
                return;
            }

            $.ajax({
                url: '{{ route("ahli-gizi.menu-makanan.menu.detail", ":id") }}'.replace(
                    ':id',
                    menuId,
                ),
                success: function (response) {
                    if (!response.success) {
                        alert(response.message);
                        return;
                    }

                    const menu = response.menu;
                    const menuRowHtml = `
                        <div class="menu-row border rounded p-3 mb-3" data-index="${index}">
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Menu Makanan</label>
                                    <div class="input-group">
                                        <input type="hidden" name="menus[${index}][id_menu]" value="${menu.id_menu}" />
                                        <input type="text" class="form-control menu-display" value="${menu.nama_menu}" readonly />
                                        <button type="button" class="btn btn-outline-primary" onclick="openMenuModal(${index})">
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
                                                .map(
                                                    (bahan) => `
                                                <tr data-jumlah-per-porsi="${bahan.jumlah_per_porsi}">
                                                    <td>${bahan.nama_bahan}</td>
                                                    <td>${bahan.jumlah_per_porsi}</td>
                                                    <td class="total-kebutuhan">${bahan.jumlah_per_porsi}</td>
                                                    <td>${bahan.satuan}</td>
                                                </tr>
                                            `,
                                                )
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
                            (id) =>
                                id !==
                                parseInt(
                                    existingRow
                                        .find('input[name$="[id_menu]"]')
                                        .val(),
                                ),
                        );
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
                    alert(
                        'Gagal memuat detail menu: ' +
                            xhr.responseJSON?.message || 'Unknown error',
                    );
                },
            });
        }

        function removeMenuRow(button) {
            const row = $(button).closest('.menu-row');
            const menuId = parseInt(row.find('input[name$="[id_menu]"]').val());
            selectedMenus = selectedMenus.filter((id) => id !== menuId);
            row.remove();
            updateSubmitButton();
            if ($('.menu-row').length === 0) {
                $('#emptyState').show();
            }
        }

        function updateTotalKebutuhan(row, index) {
            const porsi = parseInt(row.find('.porsi-input').val()) || 0;
            row.find('tbody tr').each(function () {
                const jumlahPerPorsi =
                    parseFloat($(this).data('jumlah-per-porsi')) || 0;
                const total = jumlahPerPorsi * porsi;
                $(this).find('.total-kebutuhan').text(total.toFixed(4));
            });
        }

        function updateSubmitButton() {
            $('#submitBtn').toggle($('.menu-row').length > 0);
        }

        $(document).on('click', '.menu-card', function () {
            const menuItem = $(this).closest('.menu-item');
            selectMenu(
                currentMenuIndex,
                menuItem.data('id'),
                menuItem.data('name'),
            );
        });
    </script>
@endpush
