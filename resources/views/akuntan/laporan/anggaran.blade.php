@extends('template_akuntan.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">Penggunaan Anggaran</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('akuntan.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Laporan</li>
                        <li class="breadcrumb-item active">Penggunaan Anggaran</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Period Filter (Native Sneat Style) --}}
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

        {{-- Report Paper HTML --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5" style="overflow-x: auto;">
                <div style="min-width: 800px; font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                    
                    {{-- Formal Header --}}
                    <div style="text-align: center; font-weight: bold; margin-bottom: 20px; line-height: 1.5;">
                        SATUAN PELAYANAN PEMENUHAN GIZI (SPPG)<br>
                        YAYASAN KEMALA BHAYANGKARI CABANG {{ strtoupper($settings->report_location ?? 'TEMANGGUNG') }}
                    </div>

                    <h4 style="text-align: center; margin-bottom: 20px; font-weight: bold; color: #000;">
                        LAPORAN PENGGUNAAN ANGGARAN
                    </h4>

                    {{-- Nomor Surat (Yellow Highlight) --}}
                    <div style="text-align: center; font-weight: bold; background-color: #fff59d; width: 250px; margin: 0 auto 20px; padding: 5px; border: 1px solid #000;">
                        Nomor : {{ $nomor ?? '-' }}
                    </div>

                    <div style="margin-bottom: 20px;">
                        <strong>Periode:</strong> 
                        {{ $activePeriod ? $activePeriod->start_date->format('d/m/Y') . ' - ' . $activePeriod->end_date->format('d/m/Y') : '-' }}
                    </div>

                    <div style="margin-bottom: 20px;">
                        Yang bertanda tangan di bawah ini:
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <tr>
                            <td style="padding: 4px; width: 100px;">Nama</td>
                            <td style="padding: 4px;">: {{ $settings->head_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px;">Jabatan</td>
                            <td style="padding: 4px;">: Kepala SPPG</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px;">SPPG</td>
                            <td style="padding: 4px;">: {{ $settings->institution_name ?? '-' }}</td>
                        </tr>
                    </table>

                    <div style="margin-bottom: 20px;">
                        Dengan ini menyatakan bahwa laporan penggunaan dana sebagai berikut:
                    </div>

                    <style>
                        .anggaran-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                            color: #000;
                        }

                        .anggaran-table td,
                        .anggaran-table th {
                            border: 1px solid black;
                            padding: 6px 8px;
                        }

                        .a-center { text-align: center; }
                        .a-right { text-align: right; }
                        .a-bold { font-weight: bold; }
                    </style>

                    {{-- TABEL I. RINCIAN KEGIATAN --}}
                    <table class="anggaran-table">
                        <tr class="a-bold" style="border-bottom: 2px solid black;">
                            <td>I. RINCIAN KEGIATAN</td>
                            <td class="a-center" style="width: 25%;">Dana Diajukan (Rp)</td>
                            <td class="a-center" style="width: 25%;">Dana Terealisasi</td>
                            <td class="a-center" style="width: 25%;">Sisa Dana (Rp)</td>
                        </tr>

                        <tr>
                            <td>Bahan Baku</td>
                            <td class="a-right">{{ number_format($bahanBakuMasuk, 0, ',', '.') }}</td>
                            <td class="a-right">{{ number_format($bahanBakuKeluar, 0, ',', '.') }}</td>
                            <td class="a-right">{{ number_format($sisaBahanBaku, 0, ',', '.') }}</td>
                        </tr>

                        <tr>
                            <td>Operasional</td>
                            <td class="a-right">{{ number_format($operasionalMasuk, 0, ',', '.') }}</td>
                            <td class="a-right">{{ number_format($operasionalKeluar, 0, ',', '.') }}</td>
                            <td class="a-right">{{ number_format($sisaOperasional, 0, ',', '.') }}</td>
                        </tr>

                        <tr>
                            <td>Sewa</td>
                            <td class="a-right">{{ number_format($fasilitasMasuk, 0, ',', '.') }}</td>
                            <td class="a-right">{{ number_format($fasilitasKeluar, 0, ',', '.') }}</td>
                            <td class="a-right">{{ number_format($sisaFasilitas, 0, ',', '.') }}</td>
                        </tr>

                        <tr class="a-bold" style="border-top: 2px solid black; border-bottom: 2px solid black;">
                            <td>Total</td>
                            <td class="a-right">{{ number_format($totalMasuk, 0, ',', '.') }}</td>
                            <td class="a-right">{{ number_format($totalKeluar, 0, ',', '.') }}</td>
                            <td class="a-right">{{ number_format($totalSisa, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    {{-- II. KETERANGAN --}}
                    <div class="a-bold" style="margin-bottom: 10px;">II. KETERANGAN</div>

                    <div style="margin-bottom: 10px;">
                        Dana yang telah digunakan sesuai kebutuhan kegiatan:
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <tr>
                            <td style="padding: 4px; width: 150px;">Bahan Baku</td>
                            <td style="padding: 4px;">: Pengadaan bahan baku utama</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px;">Operasional</td>
                            <td style="padding: 4px;">: Transportasi, ATK, konsumsi</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px;">Insentif Fasilitas</td>
                            <td style="padding: 4px;">: Bangunan, mobil, dll</td>
                        </tr>
                    </table>

                    <div style="margin-bottom: 40px;">
                        Sisa dana sebesar <strong>Rp {{ number_format($totalSisa, 0, ',', '.') }}</strong> akan dialihkan ke periode selanjutnya.
                    </div>

                    {{-- SIGNATURES --}}
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
                        <tr>
                            <td class="a-center" style="width: 50%;">
                                Pihak Pertama<br><br><br><br><br>
                                <span style="text-decoration: underline; font-weight: bold;">{{ $settings->foundation_head ?? 'Ketua Yayasan' }}</span>
                            </td>
                            <td class="a-center" style="width: 50%;">
                                {{ $settings->report_location ?? 'Temanggung' }}, {{ \Carbon\Carbon::parse($settings->report_date)->translatedFormat('d F Y') }}<br>
                                Akuntan<br><br><br><br><br>
                                <span style="text-decoration: underline; font-weight: bold;">{{ $settings->treasurer_name ?? 'Nama Akuntan' }}</span>
                            </td>
                        </tr>
                    </table>

                    <div class="a-center">
                        Mengetahui,<br><br><br><br><br>
                        <span style="text-decoration: underline; font-weight: bold;">{{ $settings->head_name ?? 'Kepala SPPG' }}</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
