<?php

namespace App\Http\Controllers\AdminGudang;

use App\Http\Controllers\Controller;
use App\Models\LaporanKekuranganStock;
use App\Models\AdminGudang;
use App\Models\TransaksiDapur;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Dapur;

class LaporanKekuranganStockController extends Controller
{
    public function index(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Admin Gudang.');
        }

        $id_dapur = $adminGudang->id_dapur;

        $query = TransaksiDapur::where('id_dapur', $id_dapur)
            ->whereHas('laporanKekuranganStock')
            ->with([
                'laporanKekuranganStock.templateItem',
                'createdBy',
                'detailTransaksiDapur.menuMakanan'
            ]);

        if ($request->filled('status')) {
            $query->whereHas('laporanKekuranganStock', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('createdBy', function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('sort')) {
            $sort = $request->sort;
            if ($sort === 'created_by') {
                $query->join('users', 'transaksi_dapur.created_by', '=', 'users.id_user')
                    ->orderBy('users.nama', 'asc');
            } else {
                $query->orderBy($sort, 'asc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $transaksi = $query->paginate(10);

        $stats = [
            'total' => TransaksiDapur::where('id_dapur', $id_dapur)
                ->whereHas('laporanKekuranganStock')
                ->count(),
            'pending' => TransaksiDapur::where('id_dapur', $id_dapur)
                ->whereHas('laporanKekuranganStock', function ($q) {
                    $q->where('status', 'pending');
                })->count(),
            'resolved' => TransaksiDapur::where('id_dapur', $id_dapur)
                ->whereHas('laporanKekuranganStock', function ($q) {
                    $q->where('status', 'resolved');
                })->count(),
            'handler_stok' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($id_dapur) {
                $q->where('id_dapur', $id_dapur);
            })->where('status', 'handler_stok')->count(),
        ];

        $currentDapur = $adminGudang;

        return view('admingudang.laporan-kekurangan.index', compact('transaksi', 'stats', 'currentDapur', 'dapur'));
    }

    public function exportBulk(Request $request, Dapur $dapur)
    {
        set_time_limit(300); 
        ini_set('memory_limit', '512M'); 

        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Admin Gudang.');
        }

        $id_dapur = $adminGudang->id_dapur;

        $query = TransaksiDapur::where('id_dapur', $id_dapur)
            ->whereHas('laporanKekuranganStock', function ($q) use ($request) {
                if ($request->filled('status')) {
                    $q->where('status', $request->status);
                }
            })
            ->with(['laporanKekuranganStock' => function ($q) use ($request) {
                if ($request->filled('status')) {
                    $q->where('status', $request->status);
                }
                $q->with('templateItem');
            }, 'createdBy']);

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->date_to);
        }

        $transaksiList = $query->orderBy('tanggal_transaksi', 'desc')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'LAPORAN KEKURANGAN STOK BAHAN DIBAGI PER TRANSAKSI');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A3', 'Dapur:');
        $sheet->setCellValue('B3', $dapur->nama_dapur ?? '-');
        
        $periode = 'Semua Tanggal';
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $periode = ($request->date_from ? \Carbon\Carbon::parse($request->date_from)->format('d M Y') : 'Awal') . ' s/d ' . ($request->date_to ? \Carbon\Carbon::parse($request->date_to)->format('d M Y') : 'Sekarang');
        }
        $sheet->setCellValue('A4', 'Periode:');
        $sheet->setCellValue('B4', $periode);
        
        $statusText = 'Semua Status';
        if ($request->filled('status')) {
            $statusText = $request->status == 'pending' ? 'Menunggu' : 'Diselesaikan';
        }
        $sheet->setCellValue('A5', 'Status:');
        $sheet->setCellValue('B5', $statusText);
        
        $sheet->getStyle('A3:A5')->getFont()->setBold(true);

        $row = 7;
        foreach ($transaksiList as $transaksi) {
            $tanggal = $transaksi->tanggal_transaksi ? $transaksi->tanggal_transaksi->format('d M Y H:i') : '-';
            
            $sheet->getStyle("A$row:F" . ($row + 2))->getFill()
                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                  ->getStartColor()->setARGB('FFF2F2F2');

            $sheet->setCellValue('A' . $row, 'ID Transaksi: ' . $transaksi->id_transaksi . '   |   Tanggal: ' . $tanggal);
            $sheet->mergeCells("A$row:F$row");
            $sheet->getStyle("A$row")->getFont()->setBold(true)->setSize(12);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Nama Paket : ' . ($transaksi->nama_paket ?? '-'));
            $sheet->mergeCells("A$row:C$row");
            $sheet->setCellValue('D' . $row, 'Total Porsi : ' . ($transaksi->total_porsi ?? 0));
            $sheet->mergeCells("D$row:F$row");
            $sheet->getStyle("A$row:D$row")->getFont()->setBold(true);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Pembuat : ' . ($transaksi->createdBy->nama ?? 'Unknown'));
            $sheet->mergeCells("A$row:F$row");
            $sheet->getStyle("A$row")->getFont()->setBold(true);
            $row++;
            
            $headers = ['No', 'Nama Bahan', 'Kekurangan (Nominal)', 'Satuan', 'Kekurangan (Konversi)', 'Status', 'Catatan'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $row, $header);
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD9EAD3');
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            $headerRow = $row;
            $row++;
            
            $no = 1;
            $startItemRow = $row;
            foreach ($transaksi->laporanKekuranganStock as $item) {
                $stockItem = \App\Models\StockItem::where('id_dapur', $id_dapur)
                    ->where('id_template_item', $item->id_template_item)
                    ->first();
                
                $konversiLabel = '-';
                if ($stockItem && $stockItem->konversi_nilai > 0) {
                    $konversiVal = (float) ($item->jumlah_kurang / $stockItem->konversi_nilai);
                    $konversiLabel = rtrim(rtrim(number_format($konversiVal, 3, ',', '.'), '0'), ',') . ' ' . $stockItem->konversi_satuan;
                }

                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item->templateItem->nama_bahan ?? '-');
                $sheet->setCellValue('C' . $row, rtrim(rtrim(number_format((float) $item->jumlah_kurang, 3, ',', '.'), '0'), ','));
                $sheet->setCellValue('D' . $row, $item->satuan ?? '-');
                $sheet->setCellValue('E' . $row, $konversiLabel);
                $sheet->setCellValue('F' . $row, strtoupper($item->status == 'pending' ? 'Menunggu' : 'Diselesaikan'));
                $sheet->setCellValue('G' . $row, $item->keterangan_resolve ?? '-');
                
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                if ($item->status == 'pending') {
                    $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('FFD32F2F');
                    $sheet->getStyle('F' . $row)->getFont()->setBold(true);
                } else {
                    $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('FF388E3C');
                    $sheet->getStyle('F' . $row)->getFont()->setBold(true);
                }
                
                $row++;
                $no++;
            }

            $endRow = $row - 1;
            
            $sheet->getStyle("A" . ($headerRow - 3) . ":G$endRow")->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
            
            if ($endRow >= $headerRow) {
                $sheet->getStyle("A$headerRow:G$endRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
            
            $row += 2; 
        }
        
        $sheet->getColumnDimension('A')->setWidth(5);   
        $sheet->getColumnDimension('B')->setWidth(35);  
        $sheet->getColumnDimension('C')->setWidth(20);  
        $sheet->getColumnDimension('D')->setWidth(15);  
        $sheet->getColumnDimension('E')->setWidth(20);  
        $sheet->getColumnDimension('F')->setWidth(45);  

        $filename = 'laporan_kekurangan_stok_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function show(Dapur $dapur, TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang || $transaksi->id_dapur !== $adminGudang->id_dapur) {
            abort(403, 'Unauthorized');
        }

        $transaksi->load([
            'detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'laporanKekuranganStock.templateItem',
            'laporanKekuranganStock.approvalStockItem',
            'createdBy'
        ]);

        $laporan = $transaksi->laporanKekuranganStock;
        $suppliers = \App\Models\Supplier::where('id_dapur', $dapur->id_dapur)->orderBy('nama_supplier', 'asc')->get();

        return view('admingudang.laporan-kekurangan.show', compact('transaksi', 'laporan', 'dapur', 'suppliers'));
    }

    public function resolve(Request $request, Dapur $dapur, LaporanKekuranganStock $laporan)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Admin Gudang.');
        }

        if ($laporan->transaksiDapur->id_dapur !== $adminGudang->id_dapur) {
            return redirect()->back()->with('error', 'Transaksi ini bukan dari dapur Anda.');
        }

        if ($laporan->isResolved()) {
            return redirect()->back()->with('error', 'Laporan sudah diselesaikan sebelumnya.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $stockItem = StockItem::where('id_dapur', $dapur->id_dapur)
                ->where('id_template_item', $laporan->id_template_item)
                ->first();

            $order = null;
            if ($laporan->id_handler) {
                $handlerBahan = \App\Models\ProduksiHandlerBahan::find($laporan->id_handler);
                if ($handlerBahan) {
                    $order = $handlerBahan->orderProduksi;
                }
            }

            $isActiveProduction = false;
            if ($order && in_array($order->status, ['sedang_dibuat', 'selesai'])) {
                $isActiveProduction = true;
            }

            if ($stockItem && !$isActiveProduction) {
                $stockItem->addStock((float) $laporan->jumlah_kurang);
            }

            $laporan->status = 'resolved';
            if ($request->filled('catatan')) {
                $laporan->keterangan_resolve = $request->catatan;
            }
            $laporan->save();

            DB::commit();
            return redirect()->back()->with('success', 'Laporan kekurangan berhasil diselesaikan dan stok telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to resolve laporan kekurangan', ['id_laporan' => $laporan->id_laporan, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menyelesaikan laporan kekurangan.');
        }
    }

    public function bulkResolve(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'laporan_ids' => 'required|array|min:1',
            'laporan_ids.*' => 'exists:laporan_kekurangan_stock,id_laporan',
            'catatan' => 'nullable|string|max:500'
        ]);

        $laporans = LaporanKekuranganStock::whereIn('id_laporan', $request->laporan_ids)
            ->whereHas('transaksiDapur', function ($q) use ($adminGudang) {
                $q->where('id_dapur', $adminGudang->id_dapur);
            })
            ->where('status', 'pending')
            ->get();

        if ($laporans->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada laporan yang valid untuk diselesaikan.');
        }

        $successCount = 0;
        foreach ($laporans as $laporan) {
            if ($laporan->resolve()) {
                if ($request->filled('catatan')) {
                    $laporan->update(['keterangan_resolve' => $request->catatan]);
                }
                $successCount++;
            }
        }

        return redirect()->back()
            ->with('success', "Laporan berhasil diselesaikan.");
    }

    public function summary(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang) {
            abort(403, 'Unauthorized');
        }

        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));

        $reports = LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($adminGudang) {
            $q->where('id_dapur', $adminGudang->id_dapur);
        })
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['templateItem', 'transaksiDapur'])
            ->get();

        $summary = $reports->groupBy('id_template_item')->map(function ($group) {
            $first = $group->first();
            return [
                'nama_bahan' => $first->templateItem->nama_bahan,
                'satuan' => $first->satuan,
                'total_kekurangan' => $group->sum('jumlah_kurang'),
                'total_dibutuhkan' => $group->sum('jumlah_dibutuhkan'),
                'jumlah_kejadian' => $group->count(),
                'status_terakhir' => $group->sortByDesc('created_at')->first()->status
            ];
        });

        return view('admingudang.laporan-kekurangan.summary', compact('summary', 'dateFrom', 'dateTo', 'dapur'));
    }

    public function export(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang) {
            abort(403, 'Unauthorized');
        }

        $query = LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($adminGudang) {
            $q->where('id_dapur', $adminGudang->id_dapur);
        })
            ->with(['transaksiDapur.createdBy', 'templateItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        $filename = 'laporan-kekurangan-stock-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function () use ($reports, $adminGudang) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Tanggal',
                'ID Transaksi',
                'Nama Bahan',
                'Jumlah Dibutuhkan',
                'Jumlah Tersedia',
                'Nominal (Kekurangan)',
                'Satuan',
                'Konversi (Kekurangan)',
                'Status',
                'Dibuat Oleh'
            ]);

            $formatNumber = function($val) {
                return rtrim(rtrim(number_format((float) $val, 3, ',', '.'), '0'), ',');
            };

            foreach ($reports as $report) {
                $stockItem = \App\Models\StockItem::where('id_dapur', $adminGudang->id_dapur)
                    ->where('id_template_item', $report->id_template_item)
                    ->first();
                
                $konversiLabel = '-';
                if ($stockItem && $stockItem->konversi_nilai > 0) {
                    $konversiVal = (float) ($report->jumlah_kurang / $stockItem->konversi_nilai);
                    $konversiLabel = $formatNumber($konversiVal) . ' ' . $stockItem->konversi_satuan;
                }

                fputcsv($file, [
                    $report->created_at->format('Y-m-d H:i:s'),
                    $report->id_transaksi,
                    $report->templateItem->nama_bahan,
                    $formatNumber($report->jumlah_dibutuhkan),
                    $formatNumber($report->jumlah_tersedia),
                    $formatNumber($report->jumlah_kurang),
                    $report->satuan,
                    $konversiLabel,
                    $report->status,
                    $report->transaksiDapur->createdBy->nama
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportKekuranganPdf(Dapur $dapur, TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang || $transaksi->id_dapur !== $adminGudang->id_dapur) {
            abort(403, 'Unauthorized');
        }

        $laporan = $transaksi->laporanKekuranganStock->load('templateItem');

        $pdf = Pdf::loadView('admingudang.laporan-kekurangan.export-pdf', compact('transaksi', 'laporan', 'dapur'));
        return $pdf->download('laporan-kekurangan-' . $transaksi->id_transaksi . '.pdf');
    }

    public function exportKekuranganCsv(Dapur $dapur, TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Admin Gudang.');
        }

        if ($transaksi->id_dapur !== $adminGudang->id_dapur) {
            return redirect()->back()->with('error', 'Transaksi ini bukan dari dapur Anda.');
        }

        $laporan = $transaksi->laporanKekuranganStock->load('templateItem');

        $filename = 'kekurangan-stok-' . $transaksi->id_transaksi . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function () use ($laporan, $dapur) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Nama Bahan',
                'Dibutuhkan',
                'Tersedia',
                'Kekurangan (Nominal)',
                'Satuan',
                'Kekurangan (Konversi)',
                'Status'
            ]);

            $formatNumber = function($val) {
                return rtrim(rtrim(number_format((float) $val, 3, ',', '.'), '0'), ',');
            };

            foreach ($laporan as $item) {
                $stockItem = \App\Models\StockItem::where('id_dapur', $dapur->id_dapur)
                    ->where('id_template_item', $item->id_template_item)
                    ->first();
                
                $konversiLabel = '-';
                if ($stockItem && $stockItem->konversi_nilai > 0) {
                    $konversiVal = (float) ($item->jumlah_kurang / $stockItem->konversi_nilai);
                    $konversiLabel = $formatNumber($konversiVal) . ' ' . $stockItem->konversi_satuan;
                }

                fputcsv($file, [
                    $item->templateItem->nama_bahan,
                    $formatNumber($item->jumlah_dibutuhkan),
                    $formatNumber($item->jumlah_tersedia),
                    $formatNumber($item->jumlah_kurang),
                    $item->satuan,
                    $konversiLabel,
                    $item->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
