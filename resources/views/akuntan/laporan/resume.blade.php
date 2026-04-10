@extends('template_akuntan.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">Resume Penerimaan & Pengeluaran</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('akuntan.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Laporan</li>
                        <li class="breadcrumb-item active">Resume</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Period Filter --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" class="d-flex gap-3 align-items-center flex-wrap">
                    <label class="fw-semibold mb-0">Periode:</label>
                    <select name="period_id" class="form-select form-select-sm" style="width:auto"
                        onchange="this.form.submit()">
                        <option value="">-- Pilih Periode --</option>
                        @foreach ($periods as $p)
                            <option value="{{ $p->id }}"
                                {{ $activePeriod && $activePeriod->id == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->year }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- Report Table HTML --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5" style="overflow-x: auto;">
                <div style="min-width: 800px; font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                    <h4 style="text-align: center; margin-bottom: 5px; font-weight: bold; color: #000;">LAPORAN / RESUME
                        PENERIMAAN DAN PENGELUARAN</h4>
                    <h5 style="text-align: center; margin-top: 0; margin-bottom: 30px; font-weight: normal; color: #000;">
                        Periode:
                        {{ $activePeriod ? $activePeriod->start_date->format('d/m/Y') . ' - ' . $activePeriod->end_date->format('d/m/Y') : '-' }}
                    </h5>

                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                        <tr>
                            <td style="padding: 4px; width: 150px;">Nama Lembaga</td>
                            <td style="padding: 4px;">: {{ $settings->institution_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px;">Alamat</td>
                            <td style="padding: 4px;">: {{ $settings->address ?? '-' }}</td>
                        </tr>
                    </table>

                    <style>
                        .report-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                            color: #000;
                        }

                        .report-table td,
                        .report-table th {
                            border: 1px solid black;
                            padding: 6px 8px;
                        }

                        .report-center {
                            text-align: center;
                        }

                        .report-right {
                            text-align: right;
                        }

                        .report-bold {
                            font-weight: bold;
                        }

                        .report-yellow {
                            background-color: #fff59d !important;
                        }

                        .report-gray {
                            background-color: #f2f2f2 !important;
                        }
                    </style>

                    <table class="report-table">
                        <tr class="report-gray report-bold report-center">
                            <td>URAIAN</td>
                            <td style="width: 150px;">AKUN KAS</td>
                            <td style="width: 150px;">Jumlah Periode Sebelumnya</td>
                            <td class="report-yellow" style="width: 130px;">On Going</td>
                            <td style="width: 130px;">Jumlah</td>
                        </tr>

                        <!-- PENERIMAAN -->
                        <tr class="report-bold">
                            <td colspan="5">PENERIMAAN</td>
                        </tr>
                        @forelse($incomes as $inc)
                            <tr>
                                <td>{{ $inc->category_name }}</td>
                                <td class="report-center small">{{ $inc->account_name }}</td>
                                <td class="report-center">-</td>
                                <td class="report-right report-yellow">{{ number_format($inc->total, 0, ',', '.') }}</td>
                                <td class="report-right">{{ number_format($inc->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="report-center text-muted text-uppercase small py-3">Belum ada penerimaan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                        <tr class="report-bold">
                            <td>TOTAL PENERIMAAN</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="report-right">{{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                        </tr>

                        <!-- PENGELUARAN -->
                        <tr class="report-bold">
                            <td colspan="5">PENGELUARAN</td>
                        </tr>
                        @forelse($expenses as $exp)
                            <tr>
                                <td>{{ $exp->category_name }}</td>
                                <td class="report-center small">{{ $exp->account_name }}</td>
                                <td class="report-center">-</td>
                                <td class="report-right report-yellow">{{ number_format($exp->total, 0, ',', '.') }}</td>
                                <td class="report-right">{{ number_format($exp->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="report-center text-muted text-uppercase small py-3">Belum ada pengeluaran pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                        <tr class="report-bold">
                            <td>TOTAL PENGELUARAN</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="report-right">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        </tr>

                        <!-- SALDO -->
                        <tr class="report-bold">
                            <td>BUKU KAS UMUM (SALDO)</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="report-right">{{ number_format($saldo, 0, ',', '.') }}</td>
                        </tr>
                        @foreach($cashAccountBalances as $acc)
                        <tr>
                            <td>Kas: {{ $acc->name }}</td>
                            <td class="report-center small">{{ $acc->type_label }}</td>
                            <td class="report-center">-</td>
                            <td class="report-right report-yellow">{{ number_format($acc->balance, 0, ',', '.') }}</td>
                            <td class="report-right">{{ number_format($acc->balance, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
