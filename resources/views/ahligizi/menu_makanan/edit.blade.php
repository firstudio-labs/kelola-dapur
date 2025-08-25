@extends("template_ahli_gizi.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("ahli-gizi.dashboard") }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a
                                href="{{ route("ahli-gizi.menu-makanan.index") }}"
                                class="text-muted me-2"
                            >
                                Kelola Menu Makanan
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">
                                Edit {{ $menuMakanan->nama_menu }}
                            </span>
                        </nav>
                        <h4 class="mb-1">Edit Menu Makanan</h4>
                        <p class="mb-0 text-muted">
                            Perbarui detail menu makanan dan bahan-bahannya
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
                    action="{{ route("ahli-gizi.menu-makanan.update", $menuMakanan) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="row g-4"
                >
                    @csrf
                    @method("PUT")

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
                                    class="form-control @error("nama_menu") is-invalid @enderror"
                                    placeholder="Contoh: Nasi Goreng Spesial"
                                    value="{{ old("nama_menu", $menuMakanan->nama_menu) }}"
                                />
                                @error("nama_menu")
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
                                    class="form-select @error("kategori") is-invalid @enderror"
                                >
                                    <option value="">Pilih Kategori</option>
                                    @foreach (App\Models\MenuMakanan::KATEGORI_OPTIONS as $value => $label)
                                        <option
                                            value="{{ $value }}"
                                            {{ old("kategori", $menuMakanan->kategori) == $value ? "selected" : "" }}
                                        >
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("kategori")
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
                                    class="form-select @error("is_active") is-invalid @enderror"
                                >
                                    <option value="">Pilih Status</option>
                                    <option
                                        value="1"
                                        {{ old("is_active", $menuMakanan->is_active) == "1" ? "selected" : "" }}
                                    >
                                        Active
                                    </option>
                                    <option
                                        value="0"
                                        {{ old("is_active", $menuMakanan->is_active) == "0" ? "selected" : "" }}
                                    >
                                        Inactive
                                    </option>
                                </select>
                                @error("is_active")
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
                                    class="form-control @error("deskripsi") is-invalid @enderror"
                                    placeholder="Deskripsi singkat tentang menu ini"
                                >
{{ old("deskripsi", $menuMakanan->deskripsi) }}</textarea
                                >
                                @error("deskripsi")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Gambar Menu -->
                            <div class="col-md-6">
                                <label for="gambar_menu" class="form-label">
                                    Gambar Menu
                                </label>
                                <input
                                    type="file"
                                    name="gambar_menu"
                                    id="gambar_menu"
                                    class="form-control @error("gambar_menu") is-invalid @enderror"
                                    accept="image/*"
                                />
                                <small class="form-text text-muted">
                                    Maksimal 2MB, format: jpg, png, gif. Biarkan
                                    kosong jika tidak ingin mengubah.
                                </small>
                                @if ($menuMakanan->hasGambar())
                                    <div class="mt-2">
                                        <img
                                            src="{{ $menuMakanan->gambar_url }}"
                                            alt="Current Gambar"
                                            width="150"
                                            class="rounded"
                                        />
                                    </div>
                                @endif

                                @error("gambar_menu")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bahan Menu -->
                    <div class="col-12 mt-4">
                        <h5 class="card-title mb-0">
                            Bahan Menu
                            <span class="text-danger">*</span>
                        </h5>
                        <p class="text-muted mb-3">
                            Update bahan yang diperlukan untuk menu ini (minimal
                            1 bahan)
                        </p>
                        <div id="bahan-container" class="row g-4">
                            @foreach ($menuMakanan->bahanMenu as $index => $bahan)
                                <div class="col-12 bahan-row">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-5">
                                                    <label class="form-label">
                                                        Template Bahan
                                                        <span
                                                            class="text-danger"
                                                        >
                                                            *
                                                        </span>
                                                    </label>
                                                    <select
                                                        name="bahan_menu[{{ $index }}][id_template_item]"
                                                        class="form-select template-select @error("bahan_menu.$index.id_template_item") is-invalid @enderror"
                                                        required
                                                    >
                                                        <option value="">
                                                            Pilih Bahan
                                                        </option>
                                                        @foreach ($templateItems as $item)
                                                            <option
                                                                value="{{ $item->id_template_item }}"
                                                                data-satuan="{{ $item->satuan }}"
                                                                {{ old("bahan_menu.$index.id_template_item", $bahan->id_template_item) == $item->id_template_item ? "selected" : "" }}
                                                            >
                                                                {{ $item->nama_bahan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error("bahan_menu.$index.id_template_item")
                                                        <div
                                                            class="invalid-feedback"
                                                        >
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label
                                                        class="form-label jumlah-label"
                                                    >
                                                        Jumlah per Porsi
                                                        <span
                                                            class="text-danger"
                                                        >
                                                            *
                                                        </span>
                                                    </label>
                                                    <input
                                                        type="number"
                                                        name="bahan_menu[{{ $index }}][jumlah_per_porsi]"
                                                        step="0.0001"
                                                        min="0.0001"
                                                        required
                                                        class="form-control jumlah-input @error("bahan_menu.$index.jumlah_per_porsi") is-invalid @enderror"
                                                        placeholder="Contoh: 0.5"
                                                        value="{{ old("bahan_menu.$index.jumlah_per_porsi", $bahan->jumlah_per_porsi) }}"
                                                        data-original-value="{{ $bahan->jumlah_per_porsi }}"
                                                    />
                                                    @error("bahan_menu.$index.jumlah_per_porsi")
                                                        <div
                                                            class="invalid-feedback"
                                                        >
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">
                                                        Bahan Basah
                                                    </label>
                                                    <div
                                                        class="form-check form-switch mt-2"
                                                    >
                                                        <input
                                                            class="form-check-input bahan-basah-checkbox"
                                                            type="checkbox"
                                                            name="bahan_menu[{{ $index }}][is_bahan_basah]"
                                                            value="1"
                                                            id="bahan_basah_{{ $index }}"
                                                            {{ old("bahan_menu.$index.is_bahan_basah", $bahan->is_bahan_basah) ? "checked" : "" }}
                                                        />
                                                        <label
                                                            class="form-check-label"
                                                            for="bahan_basah_{{ $index }}"
                                                        >
                                                            <small
                                                                class="text-muted"
                                                            >
                                                                +7%
                                                            </small>
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">
                                                        Berat matang +7%
                                                    </small>
                                                </div>
                                                <div
                                                    class="col-md-2 d-flex align-items-end"
                                                >
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger w-100 remove-bahan"
                                                    >
                                                        <i
                                                            class="bx bx-trash"
                                                        ></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button
                            type="button"
                            id="add-bahan"
                            class="btn btn-primary mt-3"
                        >
                            Tambah Bahan
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 d-flex justify-content-end mt-4">
                        <a
                            href="{{ route("ahli-gizi.menu-makanan.index") }}"
                            class="btn btn-outline-secondary me-2"
                        >
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Update Menu
                        </button>
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
                            id="preview-gambar"
                            src="{{ $menuMakanan->hasGambar() ? $menuMakanan->gambar_url : asset("images/menu/default-menu.jpg") }}"
                            alt="Preview Gambar"
                            class="img-fluid rounded mb-3"
                        />
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <h4 id="preview-nama" class="me-3">
                                {{ $menuMakanan->nama_menu }}
                            </h4>
                            <span
                                id="preview-kategori-badge"
                                class="badge {{ $menuMakanan->getKategoriBadgeClass() }}"
                            >
                                {{ $menuMakanan->kategori ?? "Kategori" }}
                            </span>
                        </div>
                        <p id="preview-deskripsi">
                            {{ $menuMakanan->deskripsi ?: "Deskripsi menu akan tampil di sini..." }}
                        </p>
                        <span
                            id="preview-status-badge"
                            class="badge {{ $menuMakanan->is_active ? "bg-label-success" : "bg-label-danger" }}"
                        >
                            {{ $menuMakanan->is_active ? "Active" : "Inactive" }}
                        </span>
                        <h6 class="mt-3">Bahan-bahan:</h6>
                        <ul id="preview-bahan-list" class="list-unstyled">
                            @foreach ($menuMakanan->bahanMenu as $bahan)
                                <li>
                                    {{ $bahan->templateItem->nama_bahan }} -
                                    {{ $bahan->getFormattedBeratBasah() }} per
                                    porsi
                                    @if ($bahan->is_bahan_basah)
                                        <span class="badge bg-label-info ms-2">
                                            Bahan Basah +7%
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions Alert -->
        <div class="alert alert-info alert-dismissible" role="alert">
            <h6 class="alert-heading mb-2">Instruksi Edit Menu</h6>
            <ul class="mb-0">
                <li>Nama menu harus unik</li>
                <li>
                    Pilih kategori yang sesuai (Karbohidrat, Lauk, Sayur,
                    Tambahan)
                </li>
                <li>Minimal satu bahan dengan jumlah > 0</li>
                <li>
                    Centang "Bahan Basah" untuk bahan yang perlu penambahan
                    berat +7%
                </li>
                <li>
                    Gambar opsional, biarkan kosong jika tidak ingin mengubah
                </li>
                <li>Status "Active" agar menu bisa digunakan</li>
                <li>
                    Informasi jumlah per porsi dalam satuan yang mudah dipahami
                </li>
                <li>
                    Isi nominal hanya berisi angka tanpa satuan, contoh 250
                    (tanpa: gram, ml)
                </li>
            </ul>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    </div>

    <!-- Choices.js CSS -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css"
    />

    <!-- Custom Choices.js Styling -->
    <style>
        .choices__inner {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.5rem;
            font-size: 0.875rem;
        }
        .choices__list--dropdown {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #f8f9fa;
        }
        .choices[data-type*='select-one'] .choices__inner {
            padding-bottom: 0;
        }
        .is-invalid .choices__inner {
            border-color: #dc3545;
        }
        .bahan-row .card {
            transition: all 0.3s ease;
        }
        .bahan-row .remove-bahan {
            height: 38px;
        }
        .bahan-basah-info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 8px 12px;
            margin-top: 5px;
            border-radius: 4px;
        }
    </style>

    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

    <!-- JavaScript for Dynamic Form, Unit Conversion, and Preview -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let bahanIndex = {{ count($menuMakanan->bahanMenu) }};

            const templateOptionsData =
                {!!
                    json_encode(
                        $templateItems->map(function ($item) {
                            return [
                                "value" => $item->id_template_item,
                                "label" => $item->nama_bahan,
                                "satuan" => $item->satuan ?? "",
                            ];
                        }),
                    )
                !!};

            const namaInput = document.getElementById('nama_menu');
            const kategoriSelect = document.getElementById('kategori');
            const deskripsiInput = document.getElementById('deskripsi');
            const gambarInput = document.getElementById('gambar_menu');
            const statusSelect = document.getElementById('is_active');

            const previewNama = document.getElementById('preview-nama');
            const previewKategoriBadge = document.getElementById(
                'preview-kategori-badge',
            );
            const previewDeskripsi =
                document.getElementById('preview-deskripsi');
            const previewGambar = document.getElementById('preview-gambar');
            const previewStatusBadge = document.getElementById(
                'preview-status-badge',
            );
            const previewBahanList =
                document.getElementById('preview-bahan-list');

            // Store Choices instances
            let choicesInstances = [];

            // Function to get display unit from original unit
            function getDisplayUnit(originalUnit) {
                if (!originalUnit) return '';
                const unit = originalUnit.toLowerCase();
                if (unit === 'kg') return 'gram';
                if (unit === 'liter' || unit === 'l') return 'ml';
                return originalUnit;
            }

            // Function to populate select options
            function populateSelectOptions(selectElement) {
                // Store currently selected value
                const currentValue = selectElement.value;

                // Clear existing options except the first placeholder
                while (selectElement.children.length > 1) {
                    selectElement.removeChild(selectElement.lastChild);
                }

                // Add all template options
                templateOptionsData.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = item.value;
                    option.textContent = item.label;
                    option.dataset.satuan = item.satuan;
                    // Restore selection if it matches
                    if (item.value == currentValue) {
                        option.selected = true;
                    }
                    selectElement.appendChild(option);
                });
            }

            // Function to initialize Choices.js
            function initializeChoices(selectElement) {
                // Initialize Choices.js
                const choices = new Choices(selectElement, {
                    searchEnabled: true,
                    placeholderValue: 'Pilih Bahan',
                    searchPlaceholderValue: 'Cari bahan...',
                    itemSelectText: '',
                    shouldSort: false,
                    searchResultLimit: 20,
                    searchFields: ['label'],
                    fuseOptions: {
                        threshold: 0.3,
                        keys: ['label'],
                    },
                });

                // Store instance for cleanup
                choicesInstances.push({
                    element: selectElement,
                    instance: choices,
                });

                return choices;
            }

            // Function to update input label and placeholder based on selected ingredient
            function updateInputUnit(
                selectElement,
                inputElement,
                labelElement,
                isEdit = false,
            ) {
                const selectedOption =
                    selectElement.options[selectElement.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    labelElement.textContent = 'Jumlah per Porsi *';
                    inputElement.placeholder = 'Contoh: 0.5';
                    inputElement.dataset.originalUnit = '';
                    inputElement.dataset.displayUnit = '';
                    return;
                }

                let originalSatuan = selectedOption.dataset.satuan || '';
                if (!originalSatuan) {
                    const foundItem = templateOptionsData.find(
                        (item) => item.value == selectedOption.value,
                    );
                    originalSatuan = foundItem ? foundItem.satuan : '';
                }

                const displayUnit = getDisplayUnit(originalSatuan);
                inputElement.dataset.originalUnit = originalSatuan;
                inputElement.dataset.displayUnit = displayUnit;

                // Convert value for display if in edit mode
                if (isEdit && inputElement.dataset.originalValue) {
                    let originalValue = parseFloat(
                        inputElement.dataset.originalValue,
                    );
                    if (!isNaN(originalValue)) {
                        if (
                            originalSatuan.toLowerCase() === 'kg' &&
                            displayUnit === 'gram'
                        ) {
                            inputElement.value = originalValue * 1000;
                        } else if (
                            (originalSatuan.toLowerCase() === 'liter' ||
                                originalSatuan.toLowerCase() === 'l') &&
                            displayUnit === 'ml'
                        ) {
                            inputElement.value = originalValue * 1000;
                        } else {
                            inputElement.value =
                                originalValue % 1 === 0
                                    ? originalValue.toString()
                                    : originalValue
                                          .toString()
                                          .replace(/\.?0+$/, '');
                        }
                    }
                }

                if (displayUnit) {
                    labelElement.textContent = `Jumlah per Porsi (${displayUnit}) *`;
                    if (displayUnit === 'gram') {
                        inputElement.placeholder = 'Contoh: 500';
                    } else if (displayUnit === 'ml') {
                        inputElement.placeholder = 'Contoh: 250';
                    } else {
                        inputElement.placeholder = 'Contoh: 1';
                    }
                } else {
                    labelElement.textContent = 'Jumlah per Porsi *';
                    inputElement.placeholder = 'Contoh: 0.5';
                }
            }

            // Initialize for existing rows (edit mode)
            document
                .querySelectorAll('.template-select')
                .forEach((select, index) => {
                    const row = select.closest('.bahan-row');
                    const input = row.querySelector('.jumlah-input');
                    const label = row.querySelector('.jumlah-label');
                    const checkbox = row.querySelector('.bahan-basah-checkbox');

                    // Populate options first to ensure all data is available
                    populateSelectOptions(select);

                    // Initialize Choices.js for existing selects
                    const choices = initializeChoices(select);

                    updateInputUnit(select, input, label, true);
                    formatNumberInput(input);

                    select.addEventListener('change', () => {
                        updateInputUnit(select, input, label);
                        updatePreview();
                    });
                    input.addEventListener('input', updatePreview);
                    if (checkbox) {
                        checkbox.addEventListener('change', updatePreview);
                    }
                });

            // Add new bahan row
            document
                .getElementById('add-bahan')
                .addEventListener('click', function () {
                    const container =
                        document.getElementById('bahan-container');
                    const row = document.createElement('div');
                    row.className = 'col-12 bahan-row';
                    row.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Template Bahan <span class="text-danger">*</span></label>
                            <select name="bahan_menu[${bahanIndex}][id_template_item]" class="form-select template-select" required>
                                <option value="">Pilih Bahan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label jumlah-label">Jumlah per Porsi <span class="text-danger">*</span></label>
                            <input type="number" name="bahan_menu[${bahanIndex}][jumlah_per_porsi]" step="0.0001" min="0.0001" required class="form-control jumlah-input" placeholder="Contoh: 0.5">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Bahan Basah</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input bahan-basah-checkbox" type="checkbox" name="bahan_menu[${bahanIndex}][is_bahan_basah]" value="1" id="bahan_basah_${bahanIndex}">
                                <label class="form-check-label" for="bahan_basah_${bahanIndex}">
                                    <small class="text-muted">+7%</small>
                                </label>
                            </div>
                            <small class="text-muted">Berat matang +7%</small>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger w-100 remove-bahan"><i class="bx bx-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
                    container.appendChild(row);

                    // Get new elements
                    const newSelect = row.querySelector('.template-select');
                    const newInput = row.querySelector('.jumlah-input');
                    const newLabel = row.querySelector('.jumlah-label');
                    const newCheckbox = row.querySelector(
                        '.bahan-basah-checkbox',
                    );

                    // Populate options for new select
                    populateSelectOptions(newSelect);

                    // Initialize Choices.js for new select
                    const newChoices = initializeChoices(newSelect);

                    // Add event listeners
                    newSelect.addEventListener('change', () => {
                        updateInputUnit(newSelect, newInput, newLabel);
                        updatePreview();
                    });
                    newInput.addEventListener('input', updatePreview);
                    if (newCheckbox) {
                        newCheckbox.addEventListener('change', updatePreview);
                    }

                    bahanIndex++;
                    updatePreview();
                });

            // Remove bahan row
            document.addEventListener('click', function (e) {
                if (e.target.closest('.remove-bahan')) {
                    const row = e.target.closest('.bahan-row');
                    if (document.querySelectorAll('.bahan-row').length > 1) {
                        // Find and destroy Choices instance for this row
                        const selectElement =
                            row.querySelector('.template-select');
                        const choicesInstance = choicesInstances.find(
                            (item) => item.element === selectElement,
                        );
                        if (choicesInstance) {
                            choicesInstance.instance.destroy();
                            // Remove from array
                            const index =
                                choicesInstances.indexOf(choicesInstance);
                            if (index > -1) {
                                choicesInstances.splice(index, 1);
                            }
                        }

                        row.remove();
                        updatePreview();
                    }
                }
            });

            // Handle form submit for conversion
            document
                .getElementById('menu-form')
                .addEventListener('submit', function (e) {
                    e.preventDefault();

                    const inputs = document.querySelectorAll('.jumlah-input');
                    inputs.forEach((input) => {
                        const originalUnit = input.dataset.originalUnit;
                        const displayUnit = input.dataset.displayUnit;
                        let value = parseFloat(input.value);

                        if (isNaN(value)) return;

                        if (originalUnit && displayUnit !== originalUnit) {
                            if (
                                originalUnit.toLowerCase() === 'kg' &&
                                displayUnit === 'gram'
                            ) {
                                input.value = value / 1000;
                            } else if (
                                (originalUnit.toLowerCase() === 'liter' ||
                                    originalUnit.toLowerCase() === 'l') &&
                                displayUnit === 'ml'
                            ) {
                                input.value = value / 1000;
                            }
                        }
                    });

                    this.submit();
                });

            // Handle input changes for preview
            namaInput.addEventListener('input', function () {
                previewNama.textContent =
                    this.value || '{{ $menuMakanan->nama_menu }}';
            });

            kategoriSelect.addEventListener('change', function () {
                const badge = previewKategoriBadge;
                const kategoriClasses = {
                    Karbohidrat: 'bg-label-primary',
                    Lauk: 'bg-label-success',
                    Sayur: 'bg-label-info',
                    Tambahan: 'bg-label-warning',
                };

                if (this.value && kategoriClasses[this.value]) {
                    badge.textContent = this.value;
                    badge.className = 'badge ' + kategoriClasses[this.value];
                } else {
                    badge.textContent =
                        '{{ $menuMakanan->kategori ?? "Kategori" }}';
                    badge.className =
                        'badge {{ $menuMakanan->getKategoriBadgeClass() }}';
                }
            });

            deskripsiInput.addEventListener('input', function () {
                previewDeskripsi.textContent =
                    this.value ||
                    '{{ $menuMakanan->deskripsi ?: "Deskripsi menu akan tampil di sini..." }}';
            });

            statusSelect.addEventListener('change', function () {
                const badge = previewStatusBadge;
                if (this.value === '1') {
                    badge.textContent = 'Active';
                    badge.className = 'badge bg-label-success';
                } else if (this.value === '0') {
                    badge.textContent = 'Inactive';
                    badge.className = 'badge bg-label-danger';
                } else {
                    badge.textContent =
                        '{{ $menuMakanan->is_active ? "Active" : "Inactive" }}';
                    badge.className =
                        'badge {{ $menuMakanan->is_active ? "bg-label-success" : "bg-label-danger" }}';
                }
            });

            gambarInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewGambar.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Update bahan preview with proper unit display and bahan basah calculation
            function updatePreview() {
                previewBahanList.innerHTML = '';
                const rows = document.querySelectorAll('.bahan-row');

                if (rows.length === 0) {
                    previewBahanList.innerHTML =
                        '<li class="text-muted">Tambahkan bahan untuk melihat preview</li>';
                    return;
                }

                rows.forEach((row) => {
                    const select = row.querySelector('select');
                    const input = row.querySelector('input[type="number"]');
                    const checkbox = row.querySelector('.bahan-basah-checkbox');

                    if (select.value && input.value) {
                        const selectedOption =
                            select.options[select.selectedIndex];
                        const ingredientName = selectedOption.textContent;
                        const isBasah = checkbox && checkbox.checked;

                        let displayUnit = input.dataset.displayUnit;
                        if (!displayUnit) {
                            const originalSatuan =
                                selectedOption.dataset.satuan || '';
                            if (!originalSatuan) {
                                const foundItem = templateOptionsData.find(
                                    (item) =>
                                        item.value == selectedOption.value,
                                );
                                displayUnit = getDisplayUnit(
                                    foundItem ? foundItem.satuan : '',
                                );
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
                            // Format untuk bahan basah: Nama - Berat Mentah unit Bahan Matang - Berat Matang unit per porsi **Bahan Basah +7%**
                            li.innerHTML = `${ingredientName} - ${formattedValue} ${displayUnit} Bahan Matang - ${finalValue} ${displayUnit} per porsi <span class="badge bg-label-info ms-2">Bahan Basah +7%</span>`;
                        } else {
                            // Format untuk bahan biasa: Nama - Berat unit per porsi
                            li.innerHTML = `${ingredientName} - ${formattedValue} ${displayUnit} per porsi`;
                        }

                        previewBahanList.appendChild(li);
                    }
                });
            }

            // Function to format number input (remove unnecessary decimals)
            function formatNumberInput(input) {
                let value = parseFloat(input.value);
                if (!isNaN(value)) {
                    if (value % 1 === 0) {
                        input.value = value.toString();
                    } else {
                        input.value = value.toString().replace(/\.?0+$/, '');
                    }
                }
            }

            // Add input formatting for number inputs
            document.addEventListener('input', function (e) {
                if (e.target.classList.contains('jumlah-input')) {
                    setTimeout(() => formatNumberInput(e.target), 500);
                }
            });

            // Initial preview update
            updatePreview();
        });
    </script>
@endsection
