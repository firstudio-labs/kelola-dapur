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
                    action="{{ route("ahli-gizi.menu-makanan.store") }}"
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
                            <div class="col-md-6">
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
                                    value="{{ old("nama_menu") }}"
                                />
                                @error("nama_menu")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
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
                                        {{ old("is_active") == "1" ? "selected" : "" }}
                                    >
                                        Active
                                    </option>
                                    <option
                                        value="0"
                                        {{ old("is_active") == "0" ? "selected" : "" }}
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
{{ old("deskripsi") }}</textarea
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
                                    accept="image/*"
                                    class="form-control @error("gambar_menu") is-invalid @enderror"
                                />
                                @error("gambar_menu")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <small class="text-muted">
                                    Format: JPG, PNG, GIF. Maksimal 2MB
                                </small>
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
                            Tambahkan minimal satu bahan untuk menu ini
                        </p>
                        <div id="bahan-container" class="row g-4">
                            <!-- Initial Bahan Field -->
                            <div class="col-12 bahan-row">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Template Bahan
                                                    <span class="text-danger">
                                                        *
                                                    </span>
                                                </label>
                                                <select
                                                    name="bahan_menu[0][id_template_item]"
                                                    class="form-select template-select @error("bahan_menu.0.id_template_item") is-invalid @enderror"
                                                    required
                                                >
                                                    <option value="">
                                                        Pilih Bahan
                                                    </option>
                                                    @foreach ($templateItems as $item)
                                                        <option
                                                            value="{{ $item->id_template_item }}"
                                                            data-satuan="{{ $item->satuan }}"
                                                            {{ old("bahan_menu.0.id_template_item") == $item->id_template_item ? "selected" : "" }}
                                                        >
                                                            {{ $item->nama_bahan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error("bahan_menu.0.id_template_item")
                                                    <div
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label
                                                    class="form-label jumlah-label"
                                                >
                                                    Jumlah per Porsi
                                                    <span class="text-danger">
                                                        *
                                                    </span>
                                                </label>
                                                <input
                                                    type="number"
                                                    name="bahan_menu[0][jumlah_per_porsi]"
                                                    step="0.0001"
                                                    min="0.0001"
                                                    required
                                                    class="form-control jumlah-input"
                                                    placeholder="Contoh: 0.5"
                                                    value="{{ old("bahan_menu.0.jumlah_per_porsi") }}"
                                                />
                                                @error("bahan_menu.0.jumlah_per_porsi")
                                                    <div
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div
                                                class="col-md-2 d-flex align-items-end"
                                            >
                                                <button
                                                    type="button"
                                                    class="btn btn-danger w-100 remove-bahan"
                                                >
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            Simpan Menu
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
                            src="{{ asset("images/menu/default-menu.jpg") }}"
                            alt="Preview Gambar"
                            class="img-fluid rounded mb-3"
                        />
                    </div>
                    <div class="col-md-8">
                        <h4 id="preview-nama">Nama Menu</h4>
                        <p id="preview-deskripsi">
                            Deskripsi menu akan tampil di sini...
                        </p>
                        <span
                            id="preview-status-badge"
                            class="badge bg-label-secondary"
                        >
                            Status
                        </span>
                        <h6 class="mt-3">Bahan-bahan:</h6>
                        <ul id="preview-bahan-list" class="list-unstyled">
                            <li class="text-muted">
                                Tambahkan bahan untuk melihat preview
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions Alert -->
        <div class="alert alert-info alert-dismissible" role="alert">
            <h6 class="alert-heading mb-2">Instruksi Tambah Menu</h6>
            <ul class="mb-0">
                <li>Nama menu harus unik</li>
                <li>Minimal satu bahan dengan jumlah > 0</li>
                <li>
                    Gambar opsional, default akan digunakan jika tidak diupload
                </li>
                <li>Status "Active" agar menu bisa digunakan</li>
                <li>
                    Informasi jumlah per porsi dalam satuan template (misal:
                    gram, ml)
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
    </style>

    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

    <!-- JavaScript for Dynamic Form, Unit Conversion, and Preview -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let bahanIndex = 1; // Start from 1 since initial is 0

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
            const deskripsiInput = document.getElementById('deskripsi');
            const gambarInput = document.getElementById('gambar_menu');
            const statusSelect = document.getElementById('is_active');

            const previewNama = document.getElementById('preview-nama');
            const previewDeskripsi =
                document.getElementById('preview-deskripsi');
            const previewGambar = document.getElementById('preview-gambar');
            const previewStatusBadge = document.getElementById(
                'preview-status-badge',
            );
            const previewBahanList =
                document.getElementById('preview-bahan-list');

            // Function to get display unit from original unit
            function getDisplayUnit(originalUnit) {
                if (!originalUnit) return '';

                const unit = originalUnit.toLowerCase();
                if (unit === 'kg') return 'gram';
                if (unit === 'liter' || unit === 'l') return 'ml';
                return originalUnit; // Return original for other units like pcs, ikat, etc.
            }

            // Function to update input label and placeholder based on selected ingredient
            function updateInputUnit(
                selectElement,
                inputElement,
                labelElement,
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

                // Get satuan from data attribute or find it in templateOptionsData
                let originalSatuan = selectedOption.dataset.satuan || '';

                // If not found in data attribute, search in templateOptionsData
                if (!originalSatuan) {
                    const foundItem = templateOptionsData.find(
                        (item) => item.value == selectedOption.value,
                    );
                    originalSatuan = foundItem ? foundItem.satuan : '';
                }

                const displayUnit = getDisplayUnit(originalSatuan);

                inputElement.dataset.originalUnit = originalSatuan;
                inputElement.dataset.displayUnit = displayUnit;

                console.log(
                    'Selected:',
                    selectedOption.textContent,
                    'Original Unit:',
                    originalSatuan,
                    'Display Unit:',
                    displayUnit,
                );

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

            // Initialize for initial row
            const initialSelect = document.querySelector('.template-select');
            const initialInput = document.querySelector('.jumlah-input');
            const initialLabel = document.querySelector('.jumlah-label');
            if (initialSelect) {
                updateInputUnit(initialSelect, initialInput, initialLabel);
                initialSelect.addEventListener('change', () => {
                    updateInputUnit(initialSelect, initialInput, initialLabel);
                    updatePreview();
                });
                initialInput.addEventListener('input', updatePreview);
            }

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
                                <div class="col-md-6">
                                    <label class="form-label">Template Bahan <span class="text-danger">*</span></label>
                                    <select name="bahan_menu[${bahanIndex}][id_template_item]" class="form-select template-select" required>
                                        <option value="">Pilih Bahan</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label jumlah-label">Jumlah per Porsi <span class="text-danger">*</span></label>
                                    <input type="number" name="bahan_menu[${bahanIndex}][jumlah_per_porsi]" step="0.0001" min="0.0001" required class="form-control jumlah-input" placeholder="Contoh: 0.5">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger w-100 remove-bahan"><i class="bx bx-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                    container.appendChild(row);

                    // Populate options for new select
                    const newSelect = row.querySelector('.template-select');
                    templateOptionsData.forEach((item) => {
                        const option = document.createElement('option');
                        option.value = item.value;
                        option.textContent = item.label;
                        option.dataset.satuan = item.satuan;
                        newSelect.appendChild(option);
                    });

                    // Initialize Choices.js for new select
                    const choices = new Choices(newSelect, {
                        searchEnabled: true,
                        placeholderValue: 'Pilih Bahan',
                        searchPlaceholderValue: 'Cari bahan...',
                        itemSelectText: '',
                    });

                    const newInput = row.querySelector('.jumlah-input');
                    const newLabel = row.querySelector('.jumlah-label');

                    newSelect.addEventListener('change', () => {
                        updateInputUnit(newSelect, newInput, newLabel);
                        updatePreview();
                    });
                    newInput.addEventListener('input', updatePreview);

                    bahanIndex++;
                    updatePreview();
                });

            // Remove bahan row
            document.addEventListener('click', function (e) {
                if (e.target.closest('.remove-bahan')) {
                    const row = e.target.closest('.bahan-row');
                    if (document.querySelectorAll('.bahan-row').length > 1) {
                        row.remove();
                        updatePreview();
                    }
                }
            });

            // Handle form submit for conversion
            document
                .getElementById('menu-form')
                .addEventListener('submit', function (e) {
                    e.preventDefault(); // Prevent immediate submit to convert values

                    const inputs = document.querySelectorAll('.jumlah-input');
                    inputs.forEach((input) => {
                        const originalUnit = input.dataset.originalUnit;
                        const displayUnit = input.dataset.displayUnit;
                        let value = parseFloat(input.value);

                        if (isNaN(value)) return;

                        // Convert from display unit back to original unit for storage
                        if (originalUnit && displayUnit !== originalUnit) {
                            if (
                                originalUnit.toLowerCase() === 'kg' &&
                                displayUnit === 'gram'
                            ) {
                                input.value = value / 1000; // Convert gram to kg
                            } else if (
                                (originalUnit.toLowerCase() === 'liter' ||
                                    originalUnit.toLowerCase() === 'l') &&
                                displayUnit === 'ml'
                            ) {
                                input.value = value / 1000; // Convert ml to liter
                            }
                        }
                    });

                    this.submit(); // Now submit the form
                });

            // Initialize first Choices.js
            const firstSelect = document.querySelector('.template-select');
            if (firstSelect) {
                const firstChoices = new Choices(firstSelect, {
                    searchEnabled: true,
                    placeholderValue: 'Pilih Bahan',
                    searchPlaceholderValue: 'Cari bahan...',
                    itemSelectText: '',
                });
                firstSelect.addEventListener('change', updatePreview);
                document
                    .querySelector(
                        'input[name="bahan_menu[0][jumlah_per_porsi]"]',
                    )
                    .addEventListener('input', updatePreview);
            }

            // Handle input changes for preview
            namaInput.addEventListener('input', function () {
                previewNama.textContent = this.value || 'Nama Menu';
            });

            deskripsiInput.addEventListener('input', function () {
                previewDeskripsi.textContent =
                    this.value || 'Deskripsi menu akan tampil di sini...';
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
                    badge.textContent = 'Status';
                    badge.className = 'badge bg-label-secondary';
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
                } else {
                    previewGambar.src =
                        '{{ asset("images/menu/default-menu.jpg") }}';
                }
            });

            // Update bahan preview with proper unit display
            function updatePreview() {
                previewBahanList.innerHTML = '';
                const rows = document.querySelectorAll('.bahan-row');

                if (
                    rows.length === 0 ||
                    (rows.length === 1 &&
                        !rows[0].querySelector('select').value)
                ) {
                    previewBahanList.innerHTML =
                        '<li class="text-muted">Tambahkan bahan untuk melihat preview</li>';
                    return;
                }

                rows.forEach((row) => {
                    const select = row.querySelector('select');
                    const input = row.querySelector('input[type="number"]');

                    if (select.value && input.value) {
                        const selectedOption =
                            select.options[select.selectedIndex];
                        const ingredientName = selectedOption.textContent;

                        // Get display unit from input dataset or find in templateOptionsData
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

                        // Format the value to remove unnecessary decimals
                        let value = parseFloat(input.value);
                        let formattedValue = value;

                        // Format the number to remove trailing zeros
                        if (value % 1 === 0) {
                            formattedValue = value.toString();
                        } else {
                            formattedValue = value
                                .toString()
                                .replace(/\.?0+$/, '');
                        }

                        const li = document.createElement('li');
                        li.textContent = `${ingredientName} - ${formattedValue} ${displayUnit} per porsi`;
                        previewBahanList.appendChild(li);
                    }
                });
            }

            updatePreview();
        });
    </script>
@endsection
