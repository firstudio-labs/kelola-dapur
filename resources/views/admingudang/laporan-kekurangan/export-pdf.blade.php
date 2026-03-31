<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Laporan Kekurangan Stok - {{ $transaksi->id_transaksi }}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            h1 {
                text-align: center;
                font-size: 24px;
                margin-bottom: 10px;
            }
            h2 {
                font-size: 18px;
                margin-top: 20px;
                margin-bottom: 10px;
            }
            p {
                margin: 5px 0;
                font-size: 14px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
                font-size: 14px;
            }
            th {
                background-color: #f2f2f2;
            }
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
            .text-left {
                text-align: left;
            }
            .text-muted {
                color: #6c757d;
            }
            th.text-center, td.text-center {
                text-align: center;
            }
            th.text-right, td.text-right {
                text-align: right;
            }
        </style>
    </head>
    <body>
        <h1>Laporan Kekurangan Stok</h1>
        <p class="text-center text-muted">
            Transaksi ID: {{ $transaksi->id_transaksi }} | Dapur:
            {{ $transaksi->dapur->nama_dapur ?? "Dapur" }}
        </p>

        <h2>Informasi Transaksi</h2>
        <p>
            <strong>Tanggal Transaksi:</strong>
            {{ $transaksi->tanggal_transaksi->format("d M Y") }}
        </p>
        <p>
            <strong>Total Porsi:</strong>
            {{ $transaksi->total_porsi }}
        </p>
        <p>
            <strong>Dibuat Oleh:</strong>
            {{ $transaksi->createdBy->nama }}
        </p>
        <p>
            <strong>Status:</strong>
            {{ $laporan->contains("status", "pending") ? "Pending" : "Resolved" }}
        </p>

        <h2>Detail Kekurangan Stok</h2>
        <table>
            <thead>
                <tr>
                    <th class="text-left">Nama Bahan</th>
                    <th class="text-right">Dibutuhkan</th>
                    <th class="text-right">Tersedia</th>
                    <th class="text-right">Kekurangan (Nominal)</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-right">Kekurangan (Konversi)</th>
                    <th class="text-center">Status</th>
                    <th class="text-left">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporan as $item)
                    @php
                        $stockItem = \App\Models\StockItem::where('id_dapur', $transaksi->id_dapur)->where('id_template_item', $item->id_template_item)->first();
                    @endphp
                    <tr>
                        <td class="text-left">{{ $item->templateItem->nama_bahan }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format((float) $item->jumlah_dibutuhkan, 3, ',', '.'), '0'), ',') }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format((float) $item->jumlah_tersedia, 3, ',', '.'), '0'), ',') }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format((float) $item->jumlah_kurang, 3, ',', '.'), '0'), ',') }}</td>
                        <td class="text-center">{{ $item->satuan }}</td>
                        <td class="text-right">
                            @if($stockItem && $stockItem->konversi_nilai > 0)
                                {{ rtrim(rtrim(number_format((float) ($item->jumlah_kurang / $stockItem->konversi_nilai), 3, ',', '.'), '0'), ',') }} {{ $stockItem->konversi_satuan }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">{{ ucfirst($item->status) }}</td>
                        <td class="text-left">{{ $item->keterangan_resolve ?? "-" }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            Tidak ada data kekurangan stok ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
