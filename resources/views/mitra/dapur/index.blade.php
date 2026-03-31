@extends('template_mitra.layout')
@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('mitra.dashboard') }}" class="text-muted me-2">
                            <i class="bx bx-home-alt me-1"></i>
                            Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <span class="text-dark">Manajemen Dapur</span>
                    </nav>
                    <h4 class="mb-1">Manajemen Dapur</h4>
                    <p class="mb-0 text-muted">Kelola daftar dapur yang Anda ikuti dan ajukan dapur baru.</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-dapur-saya" aria-controls="navs-top-dapur-saya" aria-selected="true">
                            <i class="bx bx-home-alt me-1"></i> Dapur Saya
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-tambah-dapur" aria-controls="navs-top-tambah-dapur" aria-selected="false">
                            <i class="bx bx-plus me-1"></i> Tambah Dapur
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    
                    {{-- TAB: Dapur Saya --}}
                    <div class="tab-pane fade show active" id="navs-top-dapur-saya" role="tabpanel">
                        <div class="table-responsive text-nowrap mt-3">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dapur</th>
                                        <th>Status Pengajuan</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse($mitraDapurList as $index => $md)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $md->dapur->nama_dapur }}</strong><br>
                                            <small class="text-muted">{{ $md->dapur->fullWilayah }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $md->status_badge_class }}">
                                                {{ $md->status_label }}
                                            </span>
                                            @if($md->catatan)
                                                <br><small class="text-danger mt-1 d-block"><i class="bx bx-info-circle"></i> {{ $md->catatan }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $md->created_at->format('d M Y, H:i') }}</td>
                                        <td>
                                            @if(!$md->isApproved())
                                                <form action="{{ route('mitra.dapur.destroy', $md) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan/menghapus pengajuan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bx bx-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Telah Disetujui</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 bg-light">
                                            <div class="text-muted">Belum ada dapur yang didaftarkan.</div>
                                            <button type="button" class="btn btn-sm btn-primary mt-2" onclick="$('.nav-tabs button[data-bs-target=\'#navs-top-tambah-dapur\']').tab('show')">
                                                Tambah Dapur Sekarang
                                            </button>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB: Tambah Dapur --}}
                    <div class="tab-pane fade" id="navs-top-tambah-dapur" role="tabpanel">
                        <h5 class="mb-4">Pengajuan Penambahan Dapur Baru</h5>
                        <p class="text-muted mb-4">Cari nama Dapur SPPG yang ingin Anda ikuti, kemudian klik "Ajukan Dapur" untuk mengirimkan permohonan ke Kepala Dapur terkait.</p>

                        <form action="{{ route('mitra.dapur.store') }}" method="POST" id="bulk_dapur_form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="dapur_search" class="form-label fw-semibold">Cari & Pilih Dapur SPPG (Bisa lebih dari satu)</label>
                                    <div class="position-relative">
                                        <div class="input-group input-group-merge shadow-sm border-primary">
                                            <span class="input-group-text bg-white border-end-0"><i class="bx bx-search fs-4 text-primary"></i></span>
                                            <input type="text" class="form-control form-control-lg border-start-0 ps-0" id="dapur_search" placeholder="Ketik nama dapur (contoh: Dapur Jakarta)..." autocomplete="off">
                                            <button class="btn btn-outline-secondary border-start-0" type="button" id="clear_dapur"><i class="bx bx-x"></i></button>
                                        </div>
                                        <div id="dapur_dropdown" class="list-group position-absolute w-100 shadow-lg mt-1 animate__animated animate__fadeInUp" style="z-index: 1100; display: none; max-height: 350px; overflow-y: auto; border-radius: 0.75rem; border: 1px solid #eee;"></div>
                                    </div>
                                    
                                    <div id="selected_dapurs_list" class="mt-3 d-flex flex-wrap gap-2">
                                        {{-- Chips will appear here --}}
                                    </div>

                                    <div id="hidden_inputs_container">
                                        {{-- Hidden inputs will appear here --}}
                                    </div>

                                    @error('id_dapur')
                                        <div class="text-danger small mt-2"><i class="bx bx-error-circle me-1"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" id="btn_submit" disabled>
                                        <i class="bx bx-paper-plane me-1"></i> Ajukan <span id="selected_count" class="badge bg-white text-primary ms-1" style="display: none;">0</span> Dapur
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <style>
        .selected-dapur-chip {
            transition: all 0.2s ease;
            cursor: default;
            border: 1px solid rgba(105, 108, 255, 0.2);
        }
        .selected-dapur-chip:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        #dapur_dropdown {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .dapur-option {
            transition: background-color 0.15s ease;
        }
        .dapur-option:hover {
            background-color: #f8f9ff !important;
        }
        .avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let dapurSearchTimeout;
        let selectedDapurs = new Set();

        function updateSubmitButton() {
            const count = selectedDapurs.size;
            if (count > 0) {
                $('#btn_submit').prop('disabled', false);
                $('#selected_count').text(count).show();
            } else {
                $('#btn_submit').prop('disabled', true);
                $('#selected_count').hide();
            }
        }

        $('#dapur_search').on('input', function () {
            clearTimeout(dapurSearchTimeout);
            const q = $(this).val().trim();
            if (q.length < 2) {
                $('#dapur_dropdown').hide();
                return;
            }

            // Loading state
            $('#dapur_dropdown').html('<div class="list-group-item text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div><span class="ms-2 text-muted">Mencari...</span></div>').show();

            dapurSearchTimeout = setTimeout(function () {
                $.ajax({
                    url: '{{ route("api.dapur.options") }}',
                    data: { search: q },
                    success: function (data) {
                        const dropdown = $('#dapur_dropdown');
                        dropdown.empty();
                        if (data.length === 0) {
                            dropdown.append('<div class="list-group-item disabled text-center py-4 text-muted"><i class="bx bx-search-alt fs-2 d-block mb-2 opacity-50"></i>Dapur tidak ditemukan. Pastikan nama yang Anda ketik benar.</div>');
                        } else {
                            data.forEach(function (d) {
                                const isAlreadySelected = selectedDapurs.has(d.id_dapur.toString());
                                dropdown.append(`
                                    <a href="javascript:void(0);" class="list-group-item list-group-item-action dapur-option py-3 border-bottom ${isAlreadySelected ? 'bg-light opacity-75' : ''}" data-id="${d.id_dapur}" data-nama="${d.nama_dapur}">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar shadow-sm">
                                                    <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-buildings"></i></span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 fw-bold text-dark">${d.nama_dapur}</h6>
                                                    ${isAlreadySelected ? '<span class="badge bg-label-success small">Sudah dipilih</span>' : ''}
                                                </div>
                                                <small class="text-muted"><i class="bx bx-map-pin me-1"></i>${d.province_name || 'Lokasi tidak tersedia'}</small>
                                            </div>
                                            ${!isAlreadySelected ? '<i class="bx bx-plus-circle fs-4 text-primary ms-2"></i>' : ''}
                                        </div>
                                    </a>
                                `);
                            });
                        }
                    },
                    error: function() {
                        $('#dapur_dropdown').hide();
                    }
                });
            }, 300);
        });

        $(document).on('click', '.dapur-option', function (e) {
            e.preventDefault();
            const id   = $(this).data('id').toString();
            const nama = $(this).data('nama');

            if (selectedDapurs.has(id)) {
                return;
            }

            selectedDapurs.add(id);

            // Add Chip
            $('#selected_dapurs_list').append(`
                <div class="badge bg-label-primary p-2 d-flex align-items-center selected-dapur-chip shadow-xs" id="chip_${id}" style="font-size: 0.9rem;">
                    <i class="bx bx-buildings me-2"></i>
                    <span class="me-2">${nama}</span>
                    <button type="button" class="btn-close remove-dapur" data-id="${id}" style="font-size: 0.65rem; filter: grayscale(1) invert(1) brightness(2);"></button>
                </div>
            `);

            // Add Hidden Input
            $('#hidden_inputs_container').append(`<input type="hidden" name="id_dapur[]" value="${id}" id="input_${id}">`);

            // Reset Search
            $('#dapur_search').val('').focus();
            $('#dapur_dropdown').hide();
            
            updateSubmitButton();
        });

        $(document).on('click', '.remove-dapur', function () {
            const id = $(this).data('id').toString();
            selectedDapurs.delete(id);
            $(`#chip_${id}`).remove();
            $(`#input_${id}`).remove();
            updateSubmitButton();
        });

        $('#clear_dapur').on('click', function () {
            $('#dapur_search').val('').focus();
            $('#dapur_dropdown').hide();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#dapur_search, #dapur_dropdown').length) {
                $('#dapur_dropdown').hide();
            }
        });
    });
</script>
@endpush
