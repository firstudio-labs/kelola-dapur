@extends("template_admin.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route('superadmin.dashboard') }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a
                                href="{{ route('superadmin.menu-makanan.index') }}"
                                class="text-muted me-2"
                            >
                                Kelola Menu Makanan
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Tambah Menu Makanan</span>
                        </nav>
                        <h4 class="mb-1">Tambah Menu Makanan Baru</h4>
                        <p class="mb-0 text-muted">
                            Buat menu makanan baru beserta bahan dan detailnya
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form
                    id="menu-form"
                    action="{{ route('superadmin.menu-makanan.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="row g-4"
                >
                    @csrf

                    <!-- Menu Information -->
                    <div class="col-12">
                        <h5 class="card-title mb-0">Informasi Menu</h5>
                        <div class="row g-4 mt-2">
                            <!-- Nama Menu -->
                            <div class="col-md-4">
                                <label for="nama_menu" class="form-label">
                                    Nama Menu
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="nama_menu"
                                    id="nama_menu"
                                    required
                                    class="form-control @error('nama_menu') is-invalid @enderror"
                                    placeholder="Contoh: Nasi Goreng Spesial"
                                    value="{{ old('nama_menu') }}"
                                />
                                @error('nama_menu')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-4">
                                <label for="kategori" class="form-label">
                                    Kategori
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="kategori"
                                    id="kategori"
                                    required
                                    class="form-select @error('kategori') is-invalid @enderror"
                                >
                                    <option value="">Pilih Kategori</option>
                                    <option
                                        value="Karbohidrat"
                                        {{ old('kategori') == 'Karbohidrat' ? 'selected' : '' }}
                                    >
                                        Karbohidrat
                                    </option>
                                    <option
                                        value="Lauk"
                                        {{ old('kategori') == 'Lauk' ? 'selected' : '' }}
                                    >
                                        Lauk
                                    </option>
                                    <option
                                        value="Sayur"
                                        {{ old('kategori') == 'Sayur' ? 'selected' : '' }}
                                    >
                                        Sayur
                                    </option>
                                    <option
                                        value="Tambahan"
                                        {{ old('kategori') == 'Tambahan' ? 'selected' : '' }}
                                    >
                                        Tambahan
                                    </option>
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-4">
                                <label for="is_active" class="form-label">
                                    Status
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="is_active"
                                    id="is_active"
                                    required
                                    class="form-select @error('is_active') is-invalid @enderror"
                                >
                                    <option value="">Pilih Status</option>
                                    <option
                                        value="1"
                                        {{ old('is_active') == '1' ? 'selected' : '' }}
                                    >
                                        Active
                                    </option>
                                    <option
                                        value="0"
                                        {{ old('is_active') == '0' ? 'selected' : '' }}
                                    >
                                        Inactive
                                    </option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Gambar Menu -->
                            <div class="col-md-4">
                                <label for="gambar_menu" class="form-label">
                                    Gambar Menu
                                </label>
                                <input
                                    type="file"
                                    name="gambar_menu"
                                    id="gambar_menu"
                                    class="form-control @error('gambar_menu') is-invalid @enderror"
                                    accept="image/*"
                                />
                                <small class="text-muted"
                                    >Format: JPEG, PNG, GIF. Maks: 2MB</small
                                >
                                @error('gambar_menu')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label for="deskripsi" class="form-label">
                                    Deskripsi
                                </label>
                                <textarea
                                    name="deskripsi"
                                    id="deskripsi"
                                    rows="3"
                                    class="form-control @error('deskripsi') is-invalid @enderror"
                                    placeholder="Masukkan deskripsi menu"
                                >{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Ingredients Section -->
                    <div class="col-12 mt-4">
                        <h5 class="card-title mb-0">Daftar Bahan</h5>
                        <div class="row g-4 mt-2">
                            <div class="col-12">
                                <div id="bahan-container">
                                    @if (old('bahan_menu'))
                                        @foreach (old('bahan_menu') as $index => $bahan)
                                            <div class="bahan-row row g-3 mb-3 align-items-end">
                                                <div class="col-md-4">
                                                    <label
                                                        class="form-label"
                                                        for="bahan_menu_{{ $index }}_id_template_item"
                                                    >
                                                        Nama Bahan
                                                        <span
                                                            class="text-danger"
                                                            >*</span
                                                        >
                                                    </label>
                                                    <select
                                                        name="bahan_menu[{{ $index }}][id_template_item]"
                                                        class="form-select template-item-select @error('bahan_menu.' . $index . '.id_template_item') is-invalid @enderror"
                                                        data-satuan="{{ isset($bahan['id_template_item']) ? ($templateItems->find($bahan['id_template_item'])->satuan ?? '') : '' }}"
                                                        required
                                                    >
                                                        <option value="">
                                                            Pilih Bahan
                                                        </option>
                                                        @foreach ($templateItems as $item)
                                                            <option
                                                                value="{{ $item->id_template_item }}"
                                                                data-satuan="{{ $item->satuan }}"
                                                                {{ old('bahan_menu.' . $index . '.id_template_item') == $item->id_template_item ? 'selected' : '' }}
                                                            >
                                                                {{ $item->nama_bahan }}
                                                                ({{ $item->satuan }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('bahan_menu.' . $index . '.id_template_item')
                                                        <div
                                                            class="invalid-feedback"
                                                        >
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label
                                                        class="form-label"
                                                        for="bahan_menu_{{ $index }}_jumlah_per_porsi"
                                                    >
                                                        Jumlah per Porsi
                                                        <span
                                                            class="text-danger"
                                                            >*</span
                                                        >
                                                    </label>
                                                    <input
                                                        type="number"
                                                        name="bahan_menu[{{ $index }}][jumlah_per_porsi]"
                                                        class="form-control jumlah-input @error('bahan_menu.' . $index . '.jumlah_per_porsi') is-invalid @enderror"
                                                        step="0.0001"
                                                        min="0.0001"
                                                        value="{{ old('bahan_menu.' . $index . '.jumlah_per_porsi') }}"
                                                        required
                                                    />
                                                    @error('bahan_menu.' . $index . '.jumlah_per_porsi')
                                                        <div
                                                            class="invalid-feedback"
                                                        >
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-check">
                                                        <input
                                                            type="checkbox"
                                                            name="bahan_menu[{{ $index }}][is_bahan_basah]"
                                                            class="form-check-input is-bahan-basah"
                                                            {{ old('bahan_menu.' . $index . '.is_bahan_basah') ? 'checked' : '' }}
                                                        />
                                                        <label
                                                            class="form-check-label"
                                                            for="bahan_menu_{{ $index }}_is_bahan_basah"
                                                        >
                                                            Bahan Basah
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-danger remove-bahan-btn"
                                                        {{ $index == 0 && count(old('bahan_menu')) == 1 ? 'disabled' : '' }}
                                                    >
                                                        <i
                                                            class="bx bx-trash me-1"
                                                        ></i>
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div
                                            class="bahan-row row g-3 mb-3 align-items-end"
                                        >
                                            <div class="col-md-4">
                                                <label
                                                    class="form-label"
                                                    for="bahan_menu_0_id_template_item"
                                                >
                                                    Nama Bahan
                                                    <span
                                                        class="text-danger"
                                                        >*</span
                                                    >
                                                </label>
                                                <select
                                                    name="bahan_menu[0][id_template_item]"
                                                    class="form-select template-item-select"
                                                    required
                                                >
                                                    <option value="">
                                                        Pilih Bahan
                                                    </option>
                                                    @foreach ($templateItems as $item)
                                                        <option
                                                            value="{{ $item->id_template_item }}"
                                                            data-satuan="{{ $item->satuan }}"
                                                        >
                                                            {{ $item->nama_bahan }}
                                                            ({{ $item->satuan }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label
                                                    class="form-label"
                                                    for="bahan_menu_0_jumlah_per_porsi"
                                                >
                                                    Jumlah per Porsi
                                                    <span
                                                        class="text-danger"
                                                        >*</span
                                                    >
                                                </label>
                                                <input
                                                    type="number"
                                                    name="bahan_menu[0][jumlah_per_porsi]"
                                                    class="form-control jumlah-input"
                                                    step="0.0001"
                                                    min="0.0001"
                                                    required
                                                />
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        name="bahan_menu[0][is_bahan_basah]"
                                                        class="form-check-input is-bahan-basah"
                                                    />
                                                    <label
                                                        class="form-check-label"
                                                        for="bahan_menu_0_is_bahan_basah"
                                                    >
                                                        Bahan Basah
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger remove-bahan-btn"
                                                    disabled
                                                >
                                                    <i
                                                        class="bx bx-trash me-1"
                                                    ></i>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button
                                    type="button"
                                    id="add-bahan-btn"
                                    class="btn btn-outline-primary"
                                >
                                    <i class="bx bx-plus me-1"></i>
                                    Tambah Bahan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between">
                            <a
                                href="{{ route('superadmin.menu-makanan.index') }}"
                                class="btn btn-label-secondary"
                            >
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Simpan Menu
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Preview Menu</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <img
                            src="{{ asset('images/menu/default-menu.jpg') }}"
                            alt="Preview Gambar"
                            class="img-fluid rounded mb-3"
                            id="preview-gambar"
                            style="
                                max-height: 200px;
                                width: 100%;
                                object-fit: cover;
                            "
                        />
                    </div>
                    <div class="col-md-8">
                        <h4 id="preview-nama">Nama Menu</h4>
                        <p id="preview-kategori" class="text-muted">
                            Kategori Menu
                        </p>
                        <p id="preview-deskripsi" class="text-muted">
                            Deskripsi Menu
                        </p>
                        <ul id="preview-bahan" class="mb-0"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Choices.js CSS -->
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css"
        />

        <!-- JavaScript for Dynamic Ingredients and Preview -->
        <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize Choices.js for template item selects
                const templateSelects = document.querySelectorAll(
                    '.template-item-select',
                );
                templateSelects.forEach((select) => {
                    new Choices(select, {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: 'Pilih Bahan',
                        noResultsText: 'Bahan tidak ditemukan',
                    });
                });

                // Template options data for preview
                const templateOptionsData = [
                    @foreach ($templateItems as $item)
                        {
                            value: '{{ $item->id_template_item }}',
                            label: '{{ $item->nama_bahan }} ({{ $item->satuan }})',
                            satuan: '{{ $item->satuan }}',
                        },
                    @endforeach
                ];

                // Add new ingredient row
                let bahanIndex =
                    {{ old('bahan_menu') ? count(old('bahan_menu')) : 1 }};
                const bahanContainer =
                    document.getElementById('bahan-container');
                const addBahanBtn = document.getElementById('add-bahan-btn');

                addBahanBtn.addEventListener('click', function () {
                    const bahanRow = document.createElement('div');
                    bahanRow.className = 'bahan-row row g-3 mb-3 align-items-end';
                    bahanRow.innerHTML = `
                        <div class="col-md-4">
                            <label class="form-label" for="bahan_menu_${bahanIndex}_id_template_item">
                                Nama Bahan <span class="text-danger">*</span>
                            </label>
                            <select
                                name="bahan_menu[${bahanIndex}][id_template_item]"
                                class="form-select template-item-select"
                                required
                            >
                                <option value="">Pilih Bahan</option>
                                @foreach ($templateItems as $item)
                                    <option value="{{ $item->id_template_item }}" data-satuan="{{ $item->satuan }}">
                                        {{ $item->nama_bahan }} ({{ $item->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="bahan_menu_${bahanIndex}_jumlah_per_porsi">
                                Jumlah per Porsi <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                name="bahan_menu[${bahanIndex}][jumlah_per_porsi]"
                                class="form-control jumlah-input"
                                step="0.0001"
                                min="0.0001"
                                required
                            />
                        </div>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    name="bahan_menu[${bahanIndex}][is_bahan_basah]"
                                    class="form-check-input is-bahan-basah"
                                />
                                <label class="form-check-label" for="bahan_menu_${bahanIndex}_is_bahan_basah">
                                    Bahan Basah
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-danger remove-bahan-btn">
                                <i class="bx bx-trash me-1"></i> Hapus
                            </button>
                        </div>
                    `;
                    bahanContainer.appendChild(bahanRow);

                    // Initialize Choices.js for new select
                    const newSelect = bahanRow.querySelector(
                        '.template-item-select',
                    );
                    new Choices(newSelect, {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: 'Pilih Bahan',
                        noResultsText: 'Bahan tidak ditemukan',
                    });

                    bahanIndex++;
                    toggleRemoveButtons();
                    updatePreview();
                });

                // Remove ingredient row
                document.addEventListener('click', function (e) {
                    if (e.target.closest('.remove-bahan-btn')) {
                        e.target.closest('.bahan-row').remove();
                        toggleRemoveButtons();
                        updatePreview();
                    }
                });

                // Toggle remove buttons
                function toggleRemoveButtons() {
                    const bahanRows =
                        document.querySelectorAll('.bahan-row');
                    const removeButtons = document.querySelectorAll(
                        '.remove-bahan-btn',
                    );
                    removeButtons.forEach((btn, index) => {
                        btn.disabled = bahanRows.length === 1 && index === 0;
                    });
                }

                // Update preview
                function updatePreview() {
                    const namaMenuInput =
                        document.getElementById('nama_menu');
                    const kategoriInput = document.getElementById('kategori');
                    const deskripsiInput =
                        document.getElementById('deskripsi');
                    const gambarInput = document.getElementById('gambar_menu');
                    const previewNama =
                        document.getElementById('preview-nama');
                    const previewKategori = document.getElementById(
                        'preview-kategori',
                    );
                    const previewDeskripsi = document.getElementById(
                        'preview-deskripsi',
                    );
                    const previewGambar =
                        document.getElementById('preview-gambar');
                    const previewBahanList =
                        document.getElementById('preview-bahan');

                    // Update nama
                    previewNama.textContent = namaMenuInput.value
                        ? namaMenuInput.value
                        : 'Nama Menu';

                    // Update kategori
                    previewKategori.textContent = kategoriInput.value
                        ? kategoriInput.options[kategoriInput.selectedIndex]
                              .text
                        : 'Kategori Menu';

                    // Update deskripsi
                    previewDeskripsi.textContent = deskripsiInput.value
                        ? deskripsiInput.value
                        : 'Deskripsi Menu';

                    // Update gambar
                    if (gambarInput.files && gambarInput.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            previewGambar.src = e.target.result;
                        };
                        reader.readAsDataURL(gambarInput.files[0]);
                    } else {
                        previewGambar.src = '{{ asset("images/menu/default-menu.jpg") }}';
                    }

                    // Update bahan
                    previewBahanList.innerHTML = '';
                    const bahanRows =
                        document.querySelectorAll('.bahan-row');
                    bahanRows.forEach((row) => {
                        const select = row.querySelector(
                            '.template-item-select',
                        );
                        const input = row.querySelector('.jumlah-input');
                        const checkbox = row.querySelector('.is-bahan-basah');

                        if (select.value && input.value) {
                            const selectedOption =
                                select.options[select.selectedIndex];
                            const ingredientName = selectedOption.textContent;
                            const isBasah = checkbox && checkbox.checked;

                            let displayUnit = select.dataset.satuan;
                            if (!displayUnit) {
                                const originalSatuan =
                                    selectedOption.dataset.satuan || '';
                                if (!originalSatuan) {
                                    const foundItem = templateOptionsData.find(
                                        (item) =>
                                            item.value == selectedOption.value,
                                    );
                                    displayUnit = foundItem
                                        ? getDisplayUnit(foundItem.satuan)
                                        : '';
                                } else {
                                    displayUnit = getDisplayUnit(originalSatuan);
                                }
                            }

                            let value = parseFloat(input.value);
                            let formattedValue = value;
                            let finalValue = value;

                            // Calculate bahan basah if checked
                            if (isBasah) {
                                finalValue = value * 1.07;
                            }

                            // Format the numbers to remove trailing zeros
                            if (formattedValue % 1 === 0) {
                                formattedValue = formattedValue.toString();
                            } else {
                                formattedValue = formattedValue
                                    .toFixed(2)
                                    .replace(/\.?0+$/, '');
                            }

                            if (finalValue % 1 === 0) {
                                finalValue = finalValue.toString();
                            } else {
                                finalValue = finalValue
                                    .toFixed(2)
                                    .replace(/\.?0+$/, '');
                            }

                            const li = document.createElement('li');

                            if (isBasah) {
                                li.innerHTML = `${ingredientName} - ${formattedValue} ${displayUnit} Bahan Matang - ${finalValue} ${displayUnit} per porsi <span class="badge bg-label-info ms-2">Bahan Basah +7%</span>`;
                            } else {
                                li.innerHTML = `${ingredientName} - ${formattedValue} ${displayUnit} per porsi`;
                            }

                            previewBahanList.appendChild(li);
                        }
                    });
                }

                // Unit conversion function
                function getDisplayUnit(satuan) {
                    const unitMap = {
                        kg: 'kg',
                        g: 'gram',
                        l: 'liter',
                        ml: 'ml',
                    };
                    return unitMap[satuan.toLowerCase()] || satuan;
                }

                // Format number input
                function formatNumberInput(input) {
                    let value = parseFloat(input.value);
                    if (!isNaN(value)) {
                        if (value % 1 === 0) {
                            input.value = value.toString();
                        } else {
                            input.value = value
                                .toFixed(4)
                                .replace(/\.?0+$/, '');
                        }
                    }
                }

                // Event listeners
                document.addEventListener('input', function (e) {
                    if (
                        e.target.id === 'nama_menu' ||
                        e.target.id === 'deskripsi' ||
                        e.target.id === 'gambar_menu' ||
                        e.target.classList.contains('jumlah-input')
                    ) {
                        updatePreview();
                    }
                    if (e.target.classList.contains('jumlah-input')) {
                        setTimeout(() => formatNumberInput(e.target), 500);
                    }
                });

                document.addEventListener('change', function (e) {
                    if (
                        e.target.classList.contains('template-item-select') ||
                        e.target.classList.contains('is-bahan-basah') ||
                        e.target.id === 'kategori'
                    ) {
                        updatePreview();
                    }
                });

                // Initial preview update
                updatePreview();
            });
        </script>
    @endsection