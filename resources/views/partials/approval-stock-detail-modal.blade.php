<!-- Approval Stock Detail Modal -->
<div
    class="modal fade"
    id="approvalStockDetailModal"
    tabindex="-1"
    aria-labelledby="approvalStockDetailModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalStockDetailModalLabel">
                    Detail Permintaan Stok
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <!-- Basic Information -->
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Permintaan</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-calendar"></i>
                            </span>
                            <input
                                type="text"
                                id="detailTanggalPermintaan"
                                class="form-control"
                                readonly
                            />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jumlah Diminta</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-calculator"></i>
                            </span>
                            <input
                                type="text"
                                id="detailJumlahDiminta"
                                class="form-control"
                                readonly
                            />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-info-circle"></i>
                            </span>
                            <input
                                type="text"
                                id="detailStatus"
                                class="form-control"
                                readonly
                            />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Diproses</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-time"></i>
                            </span>
                            <input
                                type="text"
                                id="detailTanggalDiproses"
                                class="form-control"
                                readonly
                            />
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Diminta Oleh</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-user"></i>
                            </span>
                            <input
                                type="text"
                                id="detailNamaPemohon"
                                class="form-control"
                                readonly
                            />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Diproses Oleh</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-user-check"></i>
                            </span>
                            <input
                                type="text"
                                id="detailNamaPemroses"
                                class="form-control"
                                readonly
                                value="-"
                            />
                        </div>
                    </div>
                </div>

                <!-- Material Details -->
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Kedatangan</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-time"></i>
                            </span>
                            <input
                                type="text"
                                id="detailJamKedatangan"
                                class="form-control"
                                readonly
                                value="-"
                            />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Produksi</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-calendar"></i>
                            </span>
                            <input
                                type="text"
                                id="detailTanggalProduksi"
                                class="form-control"
                                readonly
                                value="-"
                            />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Expired</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-calendar-check"></i>
                            </span>
                            <input
                                type="text"
                                id="detailTanggalExpired"
                                class="form-control"
                                readonly
                                value="-"
                            />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Suhu Bahan Makanan (°C)</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-thermometer"></i>
                            </span>
                            <input
                                type="text"
                                id="detailSuhu"
                                class="form-control"
                                readonly
                                value="-"
                            />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Warna Bahan Makanan</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-palette"></i>
                            </span>
                            <input
                                type="text"
                                id="detailWarna"
                                class="form-control"
                                readonly
                                value="-"
                            />
                        </div>
                    </div>
                    <div class="col-md-12 mb-3" id="detailFotoContainer" style="display: none;">
                        <label class="form-label">Foto Bahan</label>
                        <div class="text-center">
                            <img
                                id="detailFoto"
                                src=""
                                alt="Foto Bahan"
                                class="img-thumbnail"
                                style="max-width: 100%; max-height: 400px; cursor: pointer; display: block;"
                                onclick="if(this.src && this.src !== '') window.open(this.src, '_blank')"
                                onerror="this.style.display='none'; this.parentElement.parentElement.style.display='none';"
                                onload="this.parentElement.parentElement.style.display='block';"
                            />
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea
                        id="detailKeterangan"
                        class="form-control"
                        rows="3"
                        readonly
                    >-</textarea>
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

<style>
    /* Ensure button close is visible in modal - completely static, no hover effects */
    #approvalStockDetailModal .modal-header .btn-close,
    #approvalStockDetailModal .modal-header .btn-close:hover,
    #approvalStockDetailModal .modal-header .btn-close:focus,
    #approvalStockDetailModal .modal-header .btn-close:active {
        opacity: 1 !important;
        filter: none !important;
        background-size: 1em !important;
        padding: 0.5em !important;
        background-color: #ffffff !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        transition: none !important;
        transform: none !important;
        box-shadow: none !important;
        outline: none !important;
    }
</style>

<script>
    (function() {
        'use strict';
        
        // Cache DOM elements
        const elements = {
            tanggalPermintaan: document.getElementById('detailTanggalPermintaan'),
            jumlahDiminta: document.getElementById('detailJumlahDiminta'),
            status: document.getElementById('detailStatus'),
            tanggalDiproses: document.getElementById('detailTanggalDiproses'),
            namaPemohon: document.getElementById('detailNamaPemohon'),
            namaPemroses: document.getElementById('detailNamaPemroses'),
            jamKedatangan: document.getElementById('detailJamKedatangan'),
            tanggalProduksi: document.getElementById('detailTanggalProduksi'),
            tanggalExpired: document.getElementById('detailTanggalExpired'),
            suhu: document.getElementById('detailSuhu'),
            warna: document.getElementById('detailWarna'),
            fotoContainer: document.getElementById('detailFotoContainer'),
            foto: document.getElementById('detailFoto'),
            keterangan: document.getElementById('detailKeterangan')
        };

        // Status mapping
        const statusConfig = {
            'approved': { class: 'bg-label-success', text: 'Disetujui' },
            'rejected': { class: 'bg-label-danger', text: 'Ditolak' },
            'pending': { class: 'bg-label-warning', text: 'Menunggu' }
        };

        // Security: Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Security: Validate and sanitize data
        function sanitizeData(data) {
            if (!data || typeof data !== 'object') return {};
            
            return {
                tanggal_permintaan: escapeHtml(String(data.tanggal_permintaan || '-')),
                jumlah_diminta: escapeHtml(String(data.jumlah_diminta || '-')),
                status: String(data.status || '').toLowerCase(),
                tanggal_diproses: escapeHtml(String(data.tanggal_diproses || '-')),
                nama_pemohon: escapeHtml(String(data.nama_pemohon || 'Admin Gudang')),
                nama_pemroses: data.nama_pemroses ? escapeHtml(String(data.nama_pemroses)) : null,
                jam_kedatangan: data.jam_kedatangan ? escapeHtml(String(data.jam_kedatangan)) : null,
                tanggal_produksi: data.tanggal_produksi ? escapeHtml(String(data.tanggal_produksi)) : null,
                tanggal_expired: data.tanggal_expired ? escapeHtml(String(data.tanggal_expired)) : null,
                suhu: data.suhu !== null && data.suhu !== undefined ? parseFloat(data.suhu) : null,
                warna: data.warna ? escapeHtml(String(data.warna)) : null,
                foto_bahan: data.foto_bahan ? String(data.foto_bahan) : null,
                keterangan: data.keterangan ? escapeHtml(String(data.keterangan)) : null
            };
        }

        // Update field value safely
        function updateField(element, value, defaultValue = '-') {
            if (!element) return;
            element.value = value || defaultValue;
        }

        // Update status with badge
        function updateStatus(status) {
            if (!elements.status) return;
            const statusLower = String(status || '').toLowerCase();
            const config = statusConfig[statusLower] || { class: 'bg-label-secondary', text: status || '-' };
            elements.status.value = config.text;
            elements.status.className = 'form-control ' + config.class;
        }

        // Show/hide field container
        function toggleField(containerId, show, value = '-') {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.style.display = show ? 'block' : 'none';
            if (!show && container.querySelector('input')) {
                container.querySelector('input').value = '-';
            }
        }

        // Main function to show approval stock detail
        window.showApprovalStockDetail = function(data) {
            if (!data) {
                console.error('No data provided');
                return;
            }

            // Sanitize input data
            const safeData = sanitizeData(data);

            // Update basic information
            updateField(elements.tanggalPermintaan, safeData.tanggal_permintaan);
            updateField(elements.jumlahDiminta, safeData.jumlah_diminta);
            updateStatus(safeData.status);
            updateField(elements.tanggalDiproses, safeData.tanggal_diproses);

            // Update user information
            updateField(elements.namaPemohon, safeData.nama_pemohon);
            updateField(elements.namaPemroses, safeData.nama_pemroses);

            // Update material details
            toggleField('detailJamKedatanganContainer', !!safeData.jam_kedatangan);
            updateField(elements.jamKedatangan, safeData.jam_kedatangan);

            toggleField('detailTanggalProduksiContainer', !!safeData.tanggal_produksi);
            updateField(elements.tanggalProduksi, safeData.tanggal_produksi);

            toggleField('detailTanggalExpiredContainer', !!safeData.tanggal_expired);
            updateField(elements.tanggalExpired, safeData.tanggal_expired);

            if (safeData.suhu !== null) {
                updateField(elements.suhu, safeData.suhu.toFixed(2) + '°C');
            } else {
                updateField(elements.suhu, '-');
            }

            updateField(elements.warna, safeData.warna);

            // Handle photo
            if (safeData.foto_bahan && safeData.foto_bahan !== 'null' && safeData.foto_bahan !== '' && safeData.foto_bahan !== null) {
                // Security: Validate URL format
                try {
                    // Check if it's a relative path or absolute URL
                    let photoUrl = String(safeData.foto_bahan).trim();
                    
                    // If it's a relative path, make it absolute
                    if (photoUrl.startsWith('/')) {
                        photoUrl = window.location.origin + photoUrl;
                    } else if (!photoUrl.startsWith('http://') && !photoUrl.startsWith('https://')) {
                        // If it's a relative path without leading slash
                        photoUrl = window.location.origin + '/' + photoUrl;
                    }
                    
                    // Validate URL
                    const url = new URL(photoUrl);
                    // Allow same origin or HTTPS
                    if (url.origin === window.location.origin || url.protocol === 'https:') {
                        elements.foto.src = photoUrl;
                        elements.foto.style.display = 'block';
                        elements.fotoContainer.style.display = 'block';
                    } else {
                        console.warn('Photo URL from different origin blocked:', photoUrl);
                        elements.fotoContainer.style.display = 'none';
                    }
                } catch (e) {
                    console.error('Invalid photo URL:', e, safeData.foto_bahan);
                    elements.fotoContainer.style.display = 'none';
                }
            } else {
                elements.fotoContainer.style.display = 'none';
                if (elements.foto) {
                    elements.foto.style.display = 'none';
                }
            }

            // Update keterangan
            updateField(elements.keterangan, safeData.keterangan || 'Tidak ada keterangan');

            // Show modal
            const modalElement = document.getElementById('approvalStockDetailModal');
            if (modalElement) {
                // Clean up any existing modal instances and backdrops first
                const existingBackdrops = document.querySelectorAll('.modal-backdrop');
                existingBackdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
                
                // Clean up backdrop when modal is completely hidden
                modalElement.addEventListener('hidden.bs.modal', function() {
                    // Remove any lingering backdrop
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => {
                        backdrop.remove();
                    });
                    
                    // Remove modal-open class and styles from body
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, { once: true });
                
                // Also handle when modal starts to hide (before animation completes)
                modalElement.addEventListener('hide.bs.modal', function() {
                    // Prepare for cleanup
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        // Wait for transition to complete before removing
                        const removeBackdrop = function() {
                            if (backdrop && backdrop.parentNode) {
                                backdrop.remove();
                            }
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        };
                        
                        // Try to remove after transition
                        backdrop.addEventListener('transitionend', removeBackdrop, { once: true });
                        
                        // Fallback: remove after a short delay if transition doesn't fire
                        setTimeout(removeBackdrop, 300);
                    }
                }, { once: true });
                
                modal.show();
            }
        };
        
        // Additional cleanup on page load to remove any lingering backdrops
        document.addEventListener('DOMContentLoaded', function() {
            // Remove any existing backdrops that might be stuck
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
        
        // Global cleanup function for any stuck backdrops
        window.cleanupModalBackdrop = function() {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        };
    })();
</script>
