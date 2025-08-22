@extends("template_ahli_gizi.layout")

@section("content")
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <span
                                class="avatar-initial rounded-circle bg-label-primary"
                            >
                                <i class="bx bx-cart"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-1">Input Porsi Kecil</h4>
                            <p class="mb-0 text-muted">
                                Transaksi ID: {{ $transaksi->id_transaksi }}
                            </p>
                        </div>
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
                    <h5 class="mb-0">Pilih Menu Porsi Kecil</h5>
                    <button
                        class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#menuModal"
                    >
                        <i class="bx bx-plus me-1"></i>
                        Tambah Menu
                    </button>
                </div>
                <div class="card-body">
                    @if (session("success"))
                        <div
                            class="alert alert-success alert-dismissible"
                            role="alert"
                        >
                            {{ session("success") }}
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Close"
                            ></button>
                        </div>
                    @endif

                    @if (session("error"))
                        <div
                            class="alert alert-danger alert-dismissible"
                            role="alert"
                        >
                            {{ session("error") }}
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Close"
                            ></button>
                        </div>
                    @endif

                    <form
                        action="{{ route("ahli-gizi.transaksi.update-porsi-kecil", $transaksi) }}"
                        method="POST"
                    >
                        @csrf
                        @method("POST")
                        <div class="table-responsive">
                            <table class="table table-bordered" id="menuTable">
                                <thead>
                                    <tr>
                                        <th>Nama Menu</th>
                                        <th>Jumlah Porsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="menuRows">
                                    @foreach ($porsiKecil as $item)
                                        <tr
                                            data-menu-id="{{ $item->id_menu }}"
                                        >
                                            <td>
                                                {{ $item->menuMakanan->nama_menu }}
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    name="menus[{{ $item->id_menu }}][jumlah_porsi]"
                                                    value="{{ $item->jumlah_porsi }}"
                                                    min="1"
                                                    max="1000"
                                                    required
                                                />
                                                <input
                                                    type="hidden"
                                                    name="menus[{{ $item->id_menu }}][id_menu]"
                                                    value="{{ $item->id_menu }}"
                                                />
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-icon btn-outline-danger remove-menu"
                                                >
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <a
                                href="{{ route("ahli-gizi.transaksi.edit-porsi-besar", $transaksi) }}"
                                class="btn btn-secondary me-2"
                            >
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Simpan dan Lanjut ke Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Menu -->
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
                    <h5 class="modal-title" id="menuModalLabel">
                        Pilih Menu Makanan
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
                            placeholder="Cari menu makanan..."
                        />
                    </div>
                    <div class="row" id="menuList">
                        @foreach ($menus as $menu)
                            <div
                                class="col-md-4 mb-3 menu-item"
                                data-menu-id="{{ $menu->id_menu }}"
                            >
                                <div class="card">
                                    <img
                                        src="{{ $menu->gambar ? asset("storage/" . $menu->gambar) : asset("img/placeholder.jpg") }}"
                                        class="card-img-top"
                                        alt="{{ $menu->nama_menu }}"
                                    />
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            {{ $menu->nama_menu }}
                                        </h6>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                {{ Str::limit($menu->deskripsi, 50) }}
                                            </small>
                                        </p>
                                        <p class="card-text">
                                            <strong>Bahan:</strong>
                                        </p>
                                        <ul>
                                            @foreach ($menu->bahanMenu as $bahan)
                                                <li>
                                                    {{ $bahan->templateItem->nama_bahan }}
                                                    ({{ $bahan->jumlah }}
                                                    {{ $bahan->satuan }})
                                                </li>
                                            @endforeach
                                        </ul>
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm select-menu"
                                        >
                                            Pilih
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@section("scripts")
    <script>
        $(document).ready(function () {
            // Search menu
            $('#menuSearch').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#menuList .menu-item').filter(function () {
                    $(this).toggle(
                        $(this)
                            .find('.card-title')
                            .text()
                            .toLowerCase()
                            .indexOf(value) > -1,
                    );
                });
            });

            // Select menu
            $('.select-menu').on('click', function () {
                var card = $(this).closest('.card');
                var menuId = $(this).closest('.menu-item').data('menu-id');
                var menuName = card.find('.card-title').text();

                // Check if menu already exists
                if ($(`#menuRows tr[data-menu-id="${menuId}"]`).length > 0) {
                    alert('Menu ini sudah dipilih!');
                    return;
                }

                // Add menu to table
                var row = `
                <tr data-menu-id="${menuId}">
                    <td>${menuName}</td>
                    <td>
                        <input type="number" class="form-control" name="menus[${menuId}][jumlah_porsi]" value="1" min="1" max="1000" required>
                        <input type="hidden" name="menus[${menuId}][id_menu]" value="${menuId}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-menu">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                $('#menuRows').append(row);
                $('#menuModal').modal('hide');
            });

            // Remove menu
            $(document).on('click', '.remove-menu', function () {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endsection
