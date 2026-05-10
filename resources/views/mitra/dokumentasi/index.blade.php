@extends('template_mitra.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('mitra.dashboard') }}" class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i> Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Dokumentasi</span>
                        </nav>
                        <h4 class="mb-1">Dokumentasi Kunjungan & Kegiatan</h4>
                        <p class="mb-0 text-muted">
                            Kelola catatan dan dokumentasi foto untuk setiap dapur yang bermitra dengan Anda
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                            <i class="bx bx-plus me-1"></i> Tambah Dokumentasi
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

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('mitra.dokumentasi.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="dapur-filter" class="form-label">Filter Dapur</label>
                        <select name="dapur" id="dapur-filter" class="form-select">
                            <option value="">Semua Dapur</option>
                            @foreach($dapurApproved as $dapur)
                                <option value="{{ $dapur->id_dapur }}" {{ request('dapur') == $dapur->id_dapur ? 'selected' : '' }}>
                                    {{ $dapur->nama_dapur }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($dokumentasis->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th width="150">Tanggal & Waktu</th>
                                    <th width="200">Dapur</th>
                                    <th>Keterangan</th>
                                    <th width="150">Foto</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dokumentasis as $index => $dok)
                                    <tr>
                                        <td>{{ $dokumentasis->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark">
                                                    {{ $dok->tanggal_waktu->format('d M Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $dok->tanggal_waktu->format('H:i') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-medium text-dark">
                                                {{ $dok->dapur->nama_dapur ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($dok->keterangan)
                                                <div class="text-wrap text-muted small" style="min-width: 150px;">
                                                    {{ Str::limit($dok->keterangan, 100) }}
                                                </div>
                                            @else
                                                <span class="text-light">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="bx bx-image text-primary"></i>
                                                <span class="small">{{ $dok->fotos->count() }} Foto</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @php
                                                    $fotoData = $dok->fotos->map(function($f) { 
                                                        return ["id" => $f->id_foto, "url" => Storage::url($f->url)]; 
                                                    })->values()->toArray();
                                                    
                                                    $modalData = [
                                                        "id" => $dok->id_dokumentasi,
                                                        "id_dapur" => $dok->id_dapur,
                                                        "tanggal_waktu" => $dok->tanggal_waktu->format("Y-m-d\TH:i"),
                                                        "keterangan" => $dok->keterangan,
                                                        "fotos" => $fotoData
                                                    ];
                                                @endphp
                                                <button type="button" class="btn btn-sm btn-outline-primary action-btn"
                                                    onclick='openEditModal(@json($modalData))'
                                                    data-bs-toggle="tooltip" 
                                                    title="Lihat / Edit Dokumentasi">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger action-btn"
                                                    onclick="confirmDelete({{ $dok->id_dokumentasi }})"
                                                    data-bs-toggle="tooltip" 
                                                    title="Hapus Dokumentasi">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($dokumentasis->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $dokumentasis->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-6">
                        <i class="bx bx-camera bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Belum ada dokumentasi</h5>
                        <p class="text-muted mb-3">
                            Anda belum menambahkan dokumentasi kunjungan atau kegiatan apapun.
                        </p>
                        <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                            Tambah Dokumentasi Baru
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Tambah/Edit Dokumentasi -->
        <div class="modal fade" id="dokumentasiModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title" id="modalTitle">
                            <i class="bx bx-camera me-2"></i> Form Dokumentasi
                        </h5>
                    </div>
                    <form id="dokumentasiForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" name="deleted_photo_ids[]" id="deletedPhotoIds" value="">
                        
                        <div class="modal-body pb-0">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="id_dapur" class="form-label fw-semibold">Pilih Dapur <span class="text-danger">*</span></label>
                                    <select name="id_dapur" id="id_dapur" class="form-select" required>
                                        <option value="">Pilih Dapur...</option>
                                        @foreach($dapurApproved as $dapur)
                                            <option value="{{ $dapur->id_dapur }}">{{ $dapur->nama_dapur }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_waktu" class="form-label fw-semibold">Tanggal & Waktu <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="tanggal_waktu" name="tanggal_waktu" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="keterangan" class="form-label fw-semibold">Informasi Keterangan</label>
                                <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Masukkan detail informasi, laporan kunjungan, atau catatan tambahan..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="fotos" class="form-label fw-semibold">Dokumentasi Foto</label>
                                <input class="form-control" type="file" id="fotos" name="fotos[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                                <small class="text-muted">Maksimal 5 foto. Format yang didukung: jpg, png, webp. Maksimal 5MB per file.</small>
                            </div>
                            
                            <div id="existing_fotos_container" class="mb-4" style="display: none;">
                                <label class="form-label fw-semibold d-block mb-3 border-top pt-3">Foto Tersimpan</label>
                                <div class="d-flex flex-wrap gap-3" id="existing_fotos_list">
                                    <!-- Photos will be injected here via JS -->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top pt-3">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Dokumentasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Form Hapus Dokumentasi -->
        <form id="deleteForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <style>
            .action-btn {
                min-width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.2s ease, opacity 0.2s ease;
            }
            .action-btn:hover:not(.disabled) {
                transform: scale(1.1);
                opacity: 0.9;
            }
            .table td {
                vertical-align: middle;
            }
            .foto-wrapper {
                position: relative;
                width: 100px;
                height: 100px;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .foto-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                cursor: pointer;
            }
            .btn-delete-foto {
                position: absolute;
                top: 4px;
                right: 4px;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background: rgba(255, 0, 0, 0.8);
                color: white;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                opacity: 0;
                transition: opacity 0.2s;
            }
            .foto-wrapper:hover .btn-delete-foto {
                opacity: 1;
            }
            
            /* Zoom Image Overlay Styles */
            #imageZoomOverlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.8);
                z-index: 1060;
                justify-content: center;
                align-items: center;
            }
            #imageZoomOverlay.show {
                display: flex;
            }
            #zoomedImage {
                max-width: 90%;
                max-height: 90%;
                box-shadow: 0 4px 15px rgba(0,0,0,0.5);
                border-radius: 8px;
                transform: scale(0.9);
                transition: transform 0.3s ease;
            }
            #imageZoomOverlay.show #zoomedImage {
                transform: scale(1);
            }
        </style>

        <!-- Image Zoom Overlay -->
        <div id="imageZoomOverlay" onclick="closeZoom()">
            <img id="zoomedImage" src="" alt="Zoomed Image">
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Styling SweetAlert di atas Bootstrap Modal
            document.head.insertAdjacentHTML('beforeend', '<style>.swal2-container { z-index: 1070 !important; }</style>');

            let deletedPhotoIdsArray = [];

            document.addEventListener('DOMContentLoaded', function() {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            });

            function getCurrentDateTimeLocal() {
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                return now.toISOString().slice(0, 16);
            }

            function openCreateModal() {
                document.getElementById('modalTitle').innerHTML = '<i class="bx bx-camera me-2"></i> Tambah Dokumentasi Baru';
                const form = document.getElementById('dokumentasiForm');
                form.action = "{{ route('mitra.dokumentasi.store') }}";
                document.getElementById('formMethod').value = 'POST';
                
                // Reset fields
                document.getElementById('id_dapur').value = '';
                document.getElementById('tanggal_waktu').value = getCurrentDateTimeLocal();
                document.getElementById('keterangan').value = '';
                document.getElementById('fotos').value = '';
                
                // Hide existing photos
                document.getElementById('existing_fotos_container').style.display = 'none';
                document.getElementById('existing_fotos_list').innerHTML = '';
                
                // Reset deleted photos tracker
                deletedPhotoIdsArray = [];
                document.getElementById('deletedPhotoIds').value = '';
                
                new bootstrap.Modal(document.getElementById('dokumentasiModal')).show();
            }

            function openEditModal(data) {
                document.getElementById('modalTitle').innerHTML = '<i class="bx bx-camera me-2"></i> Detail / Edit Dokumentasi';
                const form = document.getElementById('dokumentasiForm');
                form.action = `/mitra/dokumentasi/${data.id}`;
                document.getElementById('formMethod').value = 'PUT';
                
                // Populate fields
                document.getElementById('id_dapur').value = data.id_dapur;
                document.getElementById('tanggal_waktu').value = data.tanggal_waktu;
                document.getElementById('keterangan').value = data.keterangan || '';
                document.getElementById('fotos').value = '';
                
                // Reset deleted photos tracker
                deletedPhotoIdsArray = [];
                document.getElementById('deletedPhotoIds').value = '';
                
                // Populate existing photos
                const container = document.getElementById('existing_fotos_container');
                const list = document.getElementById('existing_fotos_list');
                list.innerHTML = '';
                
                if (data.fotos && data.fotos.length > 0) {
                    data.fotos.forEach(foto => {
                        list.innerHTML += `
                            <div class="foto-wrapper" id="foto-wrapper-${foto.id}">
                                <img src="${foto.url}" onclick="zoomImage('${foto.url}')" alt="Dokumentasi">
                                <button type="button" class="btn-delete-foto" onclick="markPhotoForDeletion(${foto.id})">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                        `;
                    });
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
                
                new bootstrap.Modal(document.getElementById('dokumentasiModal')).show();
            }

            function markPhotoForDeletion(fotoId) {
                // Konfirmasi penghapusan tidak perlu karena ini hanya disembunyikan sampai tombol simpan ditekan
                Swal.fire({
                    title: 'Hapus foto ini?',
                    text: "Foto akan dihapus secara permanen saat Anda menyimpan perubahan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Sembunyikan elemen gambar
                        const wrapper = document.getElementById(`foto-wrapper-${fotoId}`);
                        if (wrapper) {
                            wrapper.style.display = 'none';
                        }
                        
                        // Tambahkan ID ke array
                        deletedPhotoIdsArray.push(fotoId);
                        
                        // Update hidden input
                        document.getElementById('deletedPhotoIds').value = deletedPhotoIdsArray.join(',');
                        
                        // Cek apakah semua foto telah dihapus
                        const allWrappers = document.querySelectorAll('.foto-wrapper');
                        const hiddenWrappers = document.querySelectorAll('.foto-wrapper[style*="display: none"]');
                        if (allWrappers.length === hiddenWrappers.length) {
                            document.getElementById('existing_fotos_container').style.display = 'none';
                        }
                    }
                });
            }

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Hapus Dokumentasi?',
                    text: "Seluruh data dan foto dokumentasi ini akan dihapus secara permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteForm');
                        form.action = `/mitra/dokumentasi/${id}`;
                        form.submit();
                    }
                });
            }

            // Image Zoom Functions
            function zoomImage(url) {
                const overlay = document.getElementById('imageZoomOverlay');
                const img = document.getElementById('zoomedImage');
                img.src = url;
                overlay.classList.add('show');
            }

            function closeZoom() {
                const overlay = document.getElementById('imageZoomOverlay');
                overlay.classList.remove('show');
            }
        </script>
        @endpush
    </div>
@endsection
