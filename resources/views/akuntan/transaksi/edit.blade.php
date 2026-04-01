@extends('template_akuntan.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <nav class="d-flex align-items-center mb-2">
                <a href="{{ route('akuntan.dashboard') }}" class="text-muted me-2 small">
                    <i class="bx bx-home-alt me-1"></i>Dashboard
                </a>
                <i class="bx bx-chevron-right me-2 text-muted small"></i>
                <a href="{{ route('akuntan.transaksi.index') }}" class="text-muted me-2 small">Transaksi</a>
                <i class="bx bx-chevron-right me-2 text-muted small"></i>
                <span class="text-dark small">Edit Transaksi</span>
            </nav>
            <h4 class="fw-bold mb-1">Edit Transaksi</h4>
            <p class="mb-0 text-muted small">Perbarui detail record transaksi keuangan</p>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('akuntan.transaksi.update', $transaksi->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Periode <span class="text-danger">*</span></label>
                        <select name="period_id" class="form-select" required id="period_select">
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periods as $p)
                                <option value="{{ $p->id }}"
                                    data-start="{{ $p->start_date->format('Y-m-d') }}"
                                    data-end="{{ $p->end_date->format('Y-m-d') }}"
                                    {{ old('period_id', $transaksi->period_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->year }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" id="period-range-hint"></small>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', $transaksi->date->format('Y-m-d')) }}" required id="trx-date">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">No. Bukti</label>
                        <input type="text" name="no_bukti" class="form-control" value="{{ old('no_bukti', $transaksi->no_bukti) }}" placeholder="e.g. BKU-001/2024">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required id="category_select">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories->groupBy('group_label') as $groupName => $cats)
                                <optgroup label="{{ $groupName }}">
                                    @foreach($cats as $c)
                                        <option value="{{ $c->id }}" 
                                            data-type="{{ $c->type }}"
                                            {{ old('category_id', $transaksi->category_id) == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }} — ({{ $c->type === 'income' ? 'Penerimaan' : 'Pengeluaran' }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Uraian <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" value="{{ old('description', $transaksi->description) }}" required placeholder="Deskripsi transaksi">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Akun Kas</label>
                        <select name="cash_account_id" class="form-select">
                            <option value="">-- Pilih Akun Kas --</option>
                            @foreach($cashAccounts as $ca)
                                <option value="{{ $ca->id }}" {{ old('cash_account_id', $transaksi->cash_account_id) == $ca->id ? 'selected' : '' }}>{{ $ca->name }} ({{ $ca->type_label }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <hr>
                        {{-- Preview Saldo --}}
                        <div class="card bg-light border-0 mb-3" id="balance-preview-container" style="display: none;">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-2 text-primary small"><i class="bx bx-stats me-1"></i> Preview Perubahan Saldo</h6>
                                <div class="row text-center">
                                    <div class="col-4 border-end">
                                        <div class="text-muted small mb-1">Saldo Sebelum</div>
                                        <div class="fw-bold text-dark" id="preview-before">Rp 0</div>
                                    </div>
                                    <div class="col-4 border-end">
                                        <div class="text-muted small mb-1">Efek Transaksi</div>
                                        <div class="fw-bold" id="preview-effect">Rp 0</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small mb-1">Saldo Sesudah</div>
                                        <div class="fw-bold text-primary" id="preview-after">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6" id="debit-container">
                        <label class="form-label fw-semibold">Debit (Pemasukan) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="debit" id="debit-field" class="form-control" value="{{ old('debit', $transaksi->debit) }}" min="0" step="1">
                        </div>
                    </div>
                    <div class="col-12 col-md-6" id="credit-container">
                        <label class="form-label fw-semibold">Kredit (Pengeluaran) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="credit" id="credit-field" class="form-control" value="{{ old('credit', $transaksi->credit) }}" min="0" step="1">
                        </div>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Simpan Perubahan</button>
                        <a href="{{ route('akuntan.transaksi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentBalance = 0;

async function updateBalancePreview() {
    const periodId = document.getElementById('period_select').value;
    const cashAccountId = document.querySelector('select[name="cash_account_id"]').value;
    const container = document.getElementById('balance-preview-container');

    if (!periodId) {
        container.style.display = 'none';
        return;
    }

    try {
        // Pass exclude_id to get the balance WITHOUT this specific transaction
        const response = await fetch(`{{ route('akuntan.transaksi.getBalance') }}?period_id=${periodId}&cash_account_id=${cashAccountId}&exclude_id={{ $transaksi->id }}`);
        const data = await response.json();
        currentBalance = data.current_balance;
        
        container.style.display = 'block';
        document.getElementById('preview-before').textContent = formatIDR(currentBalance);
        calculateAfter();
    } catch (error) {
        console.error('Error fetching balance:', error);
    }
}

function calculateAfter() {
    const debit = parseFloat(document.getElementById('debit-field').value) || 0;
    const credit = parseFloat(document.getElementById('credit-field').value) || 0;
    const effect = debit - credit;
    const after = currentBalance + effect;

    const effectEl = document.getElementById('preview-effect');
    effectEl.textContent = (effect >= 0 ? '+' : '-') + ' ' + formatIDR(Math.abs(effect));
    effectEl.className = 'fw-bold ' + (effect >= 0 ? 'text-success' : 'text-danger');
    
    document.getElementById('preview-after').textContent = formatIDR(after);
}

function formatIDR(amount) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
}

document.getElementById('category_select').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const type = opt.dataset.type;
    const debitCont = document.getElementById('debit-container');
    const creditCont = document.getElementById('credit-container');
    const debitFld = document.getElementById('debit-field');
    const creditFld = document.getElementById('credit-field');

    if (type === 'income') {
        debitCont.style.display = 'block';
        creditCont.style.display = 'none';
        creditFld.value = 0;
    } else if (type === 'expense') {
        debitCont.style.display = 'none';
        creditCont.style.display = 'block';
        debitFld.value = 0;
    } else {
        debitCont.style.display = 'block';
        creditCont.style.display = 'block';
    }
    calculateAfter();
});

document.getElementById('period_select').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const hint = document.getElementById('period-range-hint');
    if (opt && opt.dataset.start) {
        const fmt = d => new Date(d).toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'});
        hint.textContent = 'Rentang: ' + fmt(opt.dataset.start) + ' – ' + fmt(opt.dataset.end);
        document.getElementById('trx-date').min = opt.dataset.start;
        document.getElementById('trx-date').max = opt.dataset.end;
    } else {
        hint.textContent = '';
    }
    updateBalancePreview();
});

document.querySelector('select[name="cash_account_id"]').addEventListener('change', updateBalancePreview);
document.getElementById('debit-field').addEventListener('input', calculateAfter);
document.getElementById('credit-field').addEventListener('input', calculateAfter);

// Trigger on load
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('period_select').dispatchEvent(new Event('change'));
    document.getElementById('category_select').dispatchEvent(new Event('change'));
});
</script>
@endsection
