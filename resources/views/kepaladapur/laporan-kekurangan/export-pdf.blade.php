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
            .text-muted {
                color: #6c757d;
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
                    <th>Nama Bahan</th>
                    <th>Jumlah Dibutuhkan</th>
                    <th>Jumlah Tersedia</th>
                    <th>Jumlah Kurang</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporan as $item)
                    <tr>
                        <td>{{ $item->templateItem->nama_bahan }}</td>
                        <td>{{ $item->jumlah_dibutuhkan }}</td>
                        <td>{{ $item->jumlah_tersedia }}</td>
                        <td>{{ $item->jumlah_kurang }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->keterangan_resolve ?? "-" }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            Tidak ada data kekurangan stok ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
