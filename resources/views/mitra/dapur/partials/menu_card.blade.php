<div class="col menu-item-card">
    <div class="card h-100 shadow-none border">
        <img class="card-img-top" src="{{ $menu->gambar_url }}" alt="Gambar Menu" style="height: 140px; object-fit: cover;">
        <div class="card-body d-flex flex-column p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="card-title mb-0 fw-bold text-truncate" style="max-width: 65%; font-size: 0.9rem;" title="{{ $menu->nama_menu }}">{{ $menu->nama_menu }}</h6>
                <span class="badge {{ $menu->getKategoriBadgeClass() }} px-1 py-1" style="font-size: 0.7rem;">{{ $menu->kategori }}</span>
            </div>
            <p class="card-text text-muted mb-3 flex-grow-1" style="font-size: 0.8rem; line-height: 1.2; white-space: normal;">{{ Str::limit($menu->deskripsi, 50) }}</p>
            <div>
                @if($menu->is_active)
                    <span class="badge bg-label-success px-2 py-1" style="font-size: 0.7rem;"><i class="bx bx-check-circle me-1"></i> Aktif</span>
                @else
                    <span class="badge bg-label-danger px-2 py-1" style="font-size: 0.7rem;"><i class="bx bx-x-circle me-1"></i> Tidak Aktif</span>
                @endif
            </div>
        </div>
    </div>
</div>
