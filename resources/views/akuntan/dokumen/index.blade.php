@extends('template_akuntan.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Dokumen</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('akuntan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Dokumen</li>
        </ol></nav>
    </div>

    @if(!$setting || !$setting->institution_name)
    <div class="alert alert-warning mb-4">
        <i class="bx bx-info-circle me-2"></i>
        Lengkapi <strong>Identitas Lembaga</strong> di <a href="{{ route('akuntan.pengaturan.index') }}">Pengaturan</a> sebelum generate dokumen.
    </div>
    @endif

    {{-- Period Select --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex gap-3 align-items-center flex-wrap" id="period-selector">
                <label class="fw-semibold mb-0">Periode:</label>
                <select class="form-select form-select-sm" style="width:auto" id="global-period">
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->year }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Document Cards --}}
    <div class="row g-4">
        @foreach($dokumenTypes as $type => $label)
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-label-primary" style="font-size:1.5rem;">
                            <i class="bx bx-file"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold mb-2">{{ $label }}</h5>
                    <p class="text-muted small mb-4">Generate dokumen resmi dari data transaksi dan identitas lembaga.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button onclick="openDoc('preview/{{ $type }}')" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-show me-1"></i> Preview
                        </button>
                        <button onclick="openDoc('export-pdf/{{ $type }}')" class="btn btn-danger btn-sm">
                            <i class="bx bx-file-pdf me-1"></i> Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function openDoc(action) {
    const periodId = document.getElementById('global-period').value;
    const url = '{{ url("akuntan/dokumen") }}/' + action + (periodId ? '?period_id=' + periodId : '');
    window.open(url, '_blank');
}
</script>
@endsection
