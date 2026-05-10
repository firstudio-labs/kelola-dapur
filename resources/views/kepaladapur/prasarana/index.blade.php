@extends('template_kepala_dapur.layout')

@push('styles')
    <style>
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('kepala-dapur.dashboard', $dapur->id_dapur) }}" class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Fasilitas & Prasarana</span>
                        </nav>
                        <h4 class="mb-1">
                            Kelengkapan Fasilitas & Prasarana Dapur
                        </h4>
                        <p class="mb-0 text-muted">
                            Pusat pengelolaan kelengkapan alat masak, bangunan, dan fasilitas dapur
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#tambahKelompokModal">
                            <i class="bx bx-plus me-1"></i> Tambah Kelompok
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('kepala-dapur.prasarana.update', $dapur) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @php
                        $checkedPrasarana = old('prasarana', $dapur->prasarana->pluck('id_item')->toArray());
                    @endphp

                    <div class="row">
                        @foreach($kategoriPrasarana as $kategori)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-none border">
                                    <div class="card-header bg-lighter py-2 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <h6 class="mb-0 me-2">{{ $kategori->nama_kategori }}</h6>
                                            @if(!$kategori->is_default)
                                                <button type="button" class="btn btn-sm btn-icon btn-text-danger" 
                                                    onclick="confirmDelete('kategori', {{ $kategori->id_kategori }}, '{{ $kategori->nama_kategori }}')"
                                                    title="Hapus Kelompok">
                                                    <i class="bx bx-trash" style="font-size: 14px;"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-icon btn-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#tambahItemModal"
                                            data-kategori-id="{{ $kategori->id_kategori }}"
                                            data-kategori-nama="{{ $kategori->nama_kategori }}"
                                            title="Tambah Item">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>
                                    <div class="card-body p-3">
                                        @foreach($kategori->items as $item)
                                            @php
                                                $dp = $dapur->prasarana->where('id_item', $item->id_item)->first();
                                                $isChecked = in_array($item->id_item, $checkedPrasarana);
                                            @endphp
                                            <div class="form-check mb-2 d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <input class="form-check-input me-2" type="checkbox" 
                                                        name="prasarana[]" 
                                                        value="{{ $item->id_item }}" 
                                                        id="item_{{ $item->id_item }}"
                                                        {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="item_{{ $item->id_item }}">
                                                        {{ $item->nama_item }}
                                                    </label>
                                                    @if($dp)
                                                        @php
                                                            $hasData = !empty($dp->keterangan) || $dp->fotos->count() > 0;
                                                        @endphp
                                                        <button type="button" class="btn btn-sm btn-icon {{ $hasData ? 'text-primary' : 'text-secondary' }} ms-2"
                                                            onclick="openDetailModal({{ $dp->id_dapur_prasarana }}, '{{ addslashes($item->nama_item) }}', `{{ $dp->keterangan }}`, {{ json_encode($dp->fotos) }})"
                                                            title="{{ $hasData ? 'Edit Detail' : 'Tambah Detail' }}">
                                                            <i class="bx {{ $hasData ? 'bx-pencil' : 'bx-plus' }}" style="font-size: 16px;"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                @if(!$item->is_default)
                                                    <button type="button" class="btn btn-sm btn-icon text-danger p-0 delete-item-btn" 
                                                        onclick="confirmDelete('item', {{ $item->id_item }}, '{{ addslashes($item->nama_item) }}')"
                                                        title="Hapus Item">
                                                        <i class="bx bx-trash" style="font-size: 14px;"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end mt-3 p-3 border-top">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Prasarana
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahKelompokModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelompok Prasarana Baru</h5>
                </div>
                <form action="{{ route('kepala-dapur.prasarana.kategori.store', $dapur->id_dapur) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama_kategori" class="form-label">Nama Kelompok <span class="text-danger">*</span></label>
                                <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" placeholder="Contoh: Alat Masak Tambahan" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Kelompok</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Item Prasarana Baru</h5>
                </div>
                <form action="{{ route('kepala-dapur.prasarana.item.store', $dapur->id_dapur) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Kelompok Prasarana</label>
                                <input type="hidden" id="modal_id_kategori" name="id_kategori" required>
                                <input type="text" id="modal_nama_kategori" class="form-control" readonly disabled>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="nama_item" class="form-label">Nama Item <span class="text-danger">*</span></label>
                                <input type="text" id="nama_item" name="nama_item" class="form-control" placeholder="Contoh: Kipas Angin Dinding" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="deleteKategoriForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    <form id="deleteItemForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Modal Detail Prasarana -->
    <div class="modal fade" id="detailPrasaranaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">
                        <i class="bx bx-info-circle me-2"></i> Detail <span id="detail_item_name" class="fw-bold"></span>
                    </h5>
                </div>
                <form id="detailPrasaranaForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body pb-0">
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-semibold">Informasi Keterangan</label>
                            <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Masukkan detail informasi tambahan terkait item prasarana ini..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="fotos" class="form-label fw-semibold">Tambah Dokumentasi Foto</label>
                            <input class="form-control" type="file" id="fotos" name="fotos[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted">Maksimal 5 foto. Format yang didukung: jpg, png, webp. Ukuran maks 5MB per file.</small>
                        </div>
                        <div id="existing_fotos_container" class="mb-4" style="display: none;">
                            <label class="form-label fw-semibold d-block mb-3 border-top pt-3">Foto Tersimpan</label>
                            <div class="d-flex flex-wrap gap-3" id="existing_fotos_list">
                                <!-- Fotos will be injected here -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="deleteFotoForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tambahItemModal = document.getElementById('tambahItemModal');
            if (tambahItemModal) {
                tambahItemModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;
                    
                    const kategoriId = button.getAttribute('data-kategori-id');
                    const kategoriNama = button.getAttribute('data-kategori-nama');
                    
                    const modalIdInput = tambahItemModal.querySelector('#modal_id_kategori');
                    const modalNamaInput = tambahItemModal.querySelector('#modal_nama_kategori');
                    const modalItemInput = tambahItemModal.querySelector('#nama_item');
                    
                    if (modalIdInput && kategoriId) modalIdInput.value = kategoriId;
                    if (modalNamaInput && kategoriNama) modalNamaInput.value = kategoriNama;
                    if (modalItemInput) modalItemInput.value = ''; 
                    
                    setTimeout(() => {
                        if (modalItemInput) modalItemInput.focus();
                    }, 500);
                });
            }
        });

        window.openDetailModal = function(idDp, itemName, keterangan, fotos) {
            document.getElementById('detail_item_name').innerText = itemName;
            document.getElementById('keterangan').value = keterangan || '';
            document.getElementById('detailPrasaranaForm').action = `{{ route('kepala-dapur.prasarana.detail.update', ['dapur' => $dapur->id_dapur, 'dapurPrasarana' => ':id']) }}`.replace(':id', idDp);
            
            // Reset deleted photo inputs
            const oldDeletedInputs = document.querySelectorAll('.deleted-photo-input');
            oldDeletedInputs.forEach(el => el.remove());

            const existingFotosContainer = document.getElementById('existing_fotos_container');
            const existingFotosList = document.getElementById('existing_fotos_list');
            existingFotosList.innerHTML = '';
            
            if (fotos && fotos.length > 0) {
                existingFotosContainer.style.display = 'block';
                fotos.forEach(foto => {
                    existingFotosList.innerHTML += `
                        <div class="position-relative photo-item" id="photo_item_${foto.id_foto}" style="width: 120px; height: 120px;">
                            <a href="/${foto.foto_url}" target="_blank">
                                <img src="/${foto.foto_url}" class="rounded shadow-sm w-100 h-100" style="object-fit: cover; border: 1px solid #e1e3ea;">
                            </a>
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 rounded-circle shadow" 
                                style="line-height: 1; width: 24px; height: 24px;" 
                                onclick="markPhotoForDeletion(${foto.id_foto})" title="Hapus Foto">
                                <i class="bx bx-trash" style="font-size: 12px; margin-left: -2px;"></i>
                            </button>
                        </div>
                    `;
                });
            } else {
                existingFotosContainer.style.display = 'none';
            }
            
            new bootstrap.Modal(document.getElementById('detailPrasaranaModal')).show();
        };

        window.markPhotoForDeletion = function(photoId) {
            // Sembunyikan dari UI
            const item = document.getElementById(`photo_item_${photoId}`);
            if (item) {
                item.style.display = 'none';
            }
            
            // Tambahkan input hidden ke form
            const form = document.getElementById('detailPrasaranaForm');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deleted_photo_ids[]';
            input.value = photoId;
            input.className = 'deleted-photo-input';
            form.appendChild(input);
        };

        window.confirmDelete = function(type, id, name) {
            let title = '';
            let text = '';
            let formId = '';
            let actionUrl = '';

            if (type === 'kategori') {
                title = 'Hapus Kelompok?';
                text = `Anda yakin ingin menghapus kelompok "${name}" beserta seluruh item di dalamnya?`;
                formId = 'deleteKategoriForm';
                actionUrl = `{{ route('kepala-dapur.prasarana.kategori.destroy', ['dapur' => $dapur->id_dapur, 'id_kategori' => ':id']) }}`.replace(':id', id);
            } else if (type === 'item') {
                title = 'Hapus Item?';
                text = `Anda yakin ingin menghapus item "${name}"?`;
                formId = 'deleteItemForm';
                actionUrl = `{{ route('kepala-dapur.prasarana.item.destroy', ['dapur' => $dapur->id_dapur, 'id_item' => ':id']) }}`.replace(':id', id);
            }

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(formId);
                    form.action = actionUrl;
                    form.submit();
                }
            });
        };
    </script>
    @endpush
@endsection
