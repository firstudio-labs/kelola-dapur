<div class="col menu-card-item"
    data-search="{{ strtolower($menu->nama_menu . ' ' . $menu->deskripsi . ' ' . $menu->kategori) }}"
    data-status="{{ $menu->is_active ? '1' : '0' }}"
    data-dapur="{{ $menu->created_by_dapur_id }}"
    data-kategori="{{ $menu->kategori }}"
>
    <div class="card menu-card h-100 d-flex flex-column">
        <div class="card-img-top-wrapper">
            <img
                src="{{ $menu->gambar_url }}"
                alt="{{ $menu->nama_menu }}"
                class="card-img-top menu-image"
            />
        </div>
        <div class="card-body d-flex flex-column p-3">

            <div class="d-flex justify-content-end mb-2">
                <span class="badge bg-label-{{ $menu->is_active ? 'success' : 'danger' }} me-1">
                    {{ $menu->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <h6 class="card-title mb-2 text-truncate-2-lines">
                {{ $menu->nama_menu }}
            </h6>

            <div class="mb-2">
                <span class="badge {{ $menu->getKategoriBadgeClass() }} me-1">
                    {{ $menu->kategori }}
                </span>
            </div>

            <div class="mb-2">
                <small class="text-muted">
                    <i class="bx bx-package me-1"></i>
                    {{ $menu->bahanMenu->count() }} bahan
                </small>
            </div>

            @if ($menu->created_at)
                <div class="mb-1">
                    <small class="text-muted">
                        <i class="bx bx-calendar me-1"></i>
                        {{ $menu->created_at->format('d M Y') }}
                    </small>
                </div>
            @endif

            <div class="mb-3">
                <small class="text-muted">
                    <i class="bx bx-home-alt me-1"></i>
                    Dapur: {{ $menu->createdByDapur->nama_dapur ?? 'Semua Dapur' }}
                </small>
            </div>

            <div class="mt-auto">
                <div class="d-flex flex-wrap gap-1">
                    <a
                        href="{{ route('ahli-gizi.menu-makanan.show', $menu) }}"
                        class="btn btn-primary btn-sm flex-grow-1"
                    >
                        <i class="bx bx-show me-1"></i>
                        Lihat Detail
                    </a>
                    @if ($menu->created_by_dapur_id == auth()->user()->userRole->id_dapur)
                        <a
                            href="{{ route('ahli-gizi.menu-makanan.edit', $menu) }}"
                            class="btn btn-info btn-sm flex-grow-1"
                        >
                            <i class="bx bx-edit me-1"></i>
                            Edit
                        </a>
                        <form
                            action="{{ route('ahli-gizi.menu-makanan.toggle-status', $menu) }}"
                            method="POST"
                            class="d-inline flex-grow-1"
                        >
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="btn btn-sm btn-{{ $menu->is_active ? 'danger' : 'success' }} w-100"
                            >
                                <i class="bx {{ $menu->is_active ? 'bx-block' : 'bx-check-circle' }} me-1"></i>
                                {{ $menu->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
