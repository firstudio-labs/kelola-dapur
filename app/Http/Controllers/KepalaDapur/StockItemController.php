<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\TemplateItem;
use App\Models\Dapur;
use App\Models\ApprovalStockItem;
use App\Models\TransaksiDapur;
use App\Models\DetailTransaksiDapur;
use App\Models\StockSnapshot;
use App\Models\BahanMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class StockItemController extends Controller
{
    public function index(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'kepala_dapur' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        $query = StockItem::with(['templateItem', 'dapur'])
            ->where('id_dapur', $dapur->id_dapur);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('templateItem', function ($q) use ($searchTerm) {
                $q->where('nama_bahan', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'habis':
                    $query->where('jumlah', 0);
                    break;
                case 'rendah':
                    $query->where('jumlah', '>', 0)->where('jumlah', '<=', 10);
                    break;
                case 'normal':
                    $query->where('jumlah', '>', 10);
                    break;
            }
        }

        if ($request->filled('satuan')) {
            $query->whereHas('templateItem', function ($q) use ($request) {
                $q->where('satuan', $request->satuan);
            });
        }

        $sortBy = $request->get('sort', 'nama_bahan');
        $sortOrder = $request->get('order', 'asc');

        if ($sortBy === 'nama_bahan') {
            $query->join('template_items', 'stock_items.id_template_item', '=', 'template_items.id_template_item')
                ->orderBy('template_items.nama_bahan', $sortOrder)
                ->select('stock_items.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $stockItems = $query->paginate(15)->appends($request->query());

        $totalItems = StockItem::where('id_dapur', $dapur->id_dapur)->count();
        $habisStok = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', 0)->count();
        $rendahStok = StockItem::where('id_dapur', $dapur->id_dapur)
            ->where('jumlah', '>', 0)->where('jumlah', '<=', 10)->count();
        $normalStok = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', '>', 10)->count();

        $availableSatuans = TemplateItem::whereHas('stockItems', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })
            ->distinct()
            ->pluck('satuan')
            ->filter()
            ->sort();

        $allStockItems = StockItem::with(['templateItem'])
            ->where('id_dapur', $dapur->id_dapur)
            ->join('template_items', 'stock_items.id_template_item', '=', 'template_items.id_template_item')
            ->orderBy('template_items.nama_bahan')
            ->select('stock_items.*')
            ->get();

        return view('kepaladapur.stock.index', compact(
            'stockItems',
            'dapur',
            'totalItems',
            'habisStok',
            'rendahStok',
            'normalStok',
            'availableSatuans',
            'allStockItems'
        ));
    }

    public function show(Dapur $dapur, StockItem $stockItem)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'kepala_dapur' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        if ($stockItem->id_dapur !== $dapur->id_dapur) {
            abort(404, 'Stock item not found for this kitchen.');
        }

        $stockItem->load(['templateItem', 'dapur']);

        $approvalHistory = ApprovalStockItem::with(['adminGudang.user', 'kepalaDapur.user'])
            ->where('id_stock_item', $stockItem->id_stock_item)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)->count();
        $approvedRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)
            ->where('status', 'approved')->count();
        $rejectedRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)
            ->where('status', 'rejected')->count();
        $pendingRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)
            ->where('status', 'pending')->count();

        $roleType = 'kepala_dapur';
        $routePrefix = 'kepala-dapur';
        $layoutTemplate = 'template_kepala_dapur.layout';
        $stockIndexLabel = 'Lihat Stok';

        return view('admingudang.stock.show', compact(
            'stockItem',
            'dapur',
            'approvalHistory',
            'totalRequests',
            'approvedRequests',
            'rejectedRequests',
            'pendingRequests',
            'roleType',
            'routePrefix',
            'layoutTemplate',
            'stockIndexLabel'
        ));
    }

    public function export(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'kepala_dapur' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        $pilihStok = $request->get('pilih_stok', 'semua');
        $format = $request->get('format', 'csv');
        $includeHistoryPermintaan = $request->has('include_history_permintaan');
        $includeHistoryTransaksi = $request->has('include_history_transaksi');
        $periode = $request->get('periode', 'semua');

        $query = StockItem::with(['templateItem'])
            ->where('id_dapur', $dapur->id_dapur)
            ->join('template_items', 'stock_items.id_template_item', '=', 'template_items.id_template_item')
            ->select('stock_items.*');

        if ($pilihStok === 'beberapa' && $request->has('stock_ids')) {
            $query->whereIn('stock_items.id_stock_item', $request->stock_ids);
        }

        if ($periode === 'tanggal' && $request->has('tanggal')) {
            $tanggal = Carbon::parse($request->tanggal);
            $query->whereDate('stock_items.tanggal_restok', $tanggal->format('Y-m-d'));
        } elseif ($periode === 'rentang' && $request->has('tanggal_awal') && $request->has('tanggal_akhir')) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('stock_items.tanggal_restok', [$tanggalAwal, $tanggalAkhir]);
        }

        $stockItems = $query->orderBy('template_items.nama_bahan')->get();

        $data = [];
        $no = 1;

        foreach ($stockItems as $item) {
            $status = $item->getStockStatus();
            $statusText = match ($status) {
                'habis' => 'Habis',
                'rendah' => 'Rendah',
                'normal' => 'Normal',
                default => ucfirst($status),
            };

            $stokDenganSatuan = rtrim(rtrim(number_format($item->jumlah, 3), '0'), '.') . ' ' . $item->templateItem->satuan;

            $historyPermintaan = '';
            if ($includeHistoryPermintaan) {
                $approvals = ApprovalStockItem::where('id_stock_item', $item->id_stock_item)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $historyItems = [];
                foreach ($approvals as $approval) {
                    $statusApproval = match($approval->status) {
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'pending' => 'Menunggu',
                        default => ucfirst($approval->status)
                    };
                    $tanggal = $approval->created_at->format('d/m/Y H:i');
                    $jumlah = rtrim(rtrim(number_format((float)$approval->jumlah, 3), '0'), '.') . ' ' . $item->templateItem->satuan;
                    $historyItems[] = "{$tanggal}: {$jumlah} ({$statusApproval})";
                }
                $historyPermintaan = implode(' | ', $historyItems);
            }

            $historyTransaksi = '';
            if ($includeHistoryTransaksi) {
                $transaksiList = TransaksiDapur::where('id_dapur', $dapur->id_dapur)
                    ->where('status', 'completed')
                    ->with(['detailTransaksiDapur.menuMakanan.bahanMenu.templateItem'])
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->get();

                $historyItems = [];
                foreach ($transaksiList as $transaksi) {
                    foreach ($transaksi->detailTransaksiDapur as $detail) {
                        $menu = $detail->menuMakanan;
                        if ($menu) {
                            $requiredIngredients = $menu->calculateRequiredIngredients($detail->jumlah_porsi);
                            foreach ($requiredIngredients as $ingredient) {
                                if ($ingredient['id_template_item'] == $item->id_template_item) {
                                    $kebutuhan = $ingredient['is_bahan_basah'] 
                                        ? $ingredient['total_berat_basah']
                                        : $ingredient['total_needed'];
                                    $kebutuhanFormatted = rtrim(rtrim(number_format($kebutuhan, 3), '0'), '.') . ' ' . $item->templateItem->satuan;
                                    $tanggal = $transaksi->tanggal_transaksi->format('d/m/Y H:i');
                                    $menuNama = $menu->nama_menu;
                                    $porsi = $detail->jumlah_porsi;
                                    $historyItems[] = "{$tanggal} - {$menuNama} ({$porsi} porsi): Kurang {$kebutuhanFormatted}";
                                }
                            }
                        }
                    }
                }
                $historyTransaksi = implode(' | ', $historyItems);
            }

            $row = [
                $no++,
                $item->templateItem->nama_bahan,
                $item->templateItem->keterangan ?: '-',
                $stokDenganSatuan,
                $statusText,
                $item->tanggal_restok ? $item->tanggal_restok->format('d/m/Y') : '-',
            ];

            if ($includeHistoryPermintaan) {
                $row[] = $historyPermintaan ?: '-';
            }

            if ($includeHistoryTransaksi) {
                $row[] = $historyTransaksi ?: '-';
            }

            $data[] = $row;
        }

        $headers = [];
        $headers[] = 'No';
        $headers[] = 'Nama Bahan';
        $headers[] = 'Keterangan';
        $headers[] = 'Stok';
        $headers[] = 'Status';
        $headers[] = 'Restok Terakhir';
        if ($includeHistoryPermintaan) {
            $headers[] = 'Riwayat Permintaan Stok';
        }
        if ($includeHistoryTransaksi) {
            $headers[] = 'History Penggunaan Transaksi';
        }

        $filename = 'stock_' . $dapur->nama_dapur . '_';
        if ($periode === 'tanggal' && $request->has('tanggal')) {
            $filename .= Carbon::parse($request->tanggal)->format('Y-m-d');
        } elseif ($periode === 'rentang' && $request->has('tanggal_awal') && $request->has('tanggal_akhir')) {
            $filename .= Carbon::parse($request->tanggal_awal)->format('Y-m-d') . '_to_' . Carbon::parse($request->tanggal_akhir)->format('Y-m-d');
        } else {
            $filename .= 'all_' . now()->format('Y-m-d');
        }

        if ($format === 'xlsx') {
            $filename .= '.xlsx';
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $colIndex = 1;
            foreach ($headers as $header) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . '1', $header);
                $colIndex++;
            }
            
            $lastCol = Coordinate::stringFromColumnIndex(count($headers));
            $headerRange = 'A1:' . $lastCol . '1';
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row = 2;
            foreach ($data as $rowData) {
                $colIndex = 1;
                foreach ($rowData as $cell) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->setCellValue($colLetter . $row, $cell);
                    $colIndex++;
                }
                $row++;
            }
            
            foreach (range('A', $lastCol) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            return response()->streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } else {
            $filename .= '.csv';
            $csvHeaders = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($data, $headers) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fwrite($file, implode(',', $headers) . "\n");
                
                foreach ($data as $row) {
                    $formattedRow = array_map(function($cell) {
                        $cell = str_replace(["\n", "\r"], [' ', ' '], (string)$cell);
                        $cell = str_replace(',', ';', $cell);
                        return $cell;
                    }, $row);
                    fwrite($file, implode(',', $formattedRow) . "\n");
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $csvHeaders);
        }
    }

    public function updateKonversi(Request $request, Dapur $dapur, StockItem $stockItem)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'kepala_dapur' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        if ($stockItem->id_dapur !== $dapur->id_dapur) {
            abort(404, 'Stock item not found for this kitchen.');
        }

        $request->validate([
            'konversi_nilai' => 'nullable|numeric|min:0.01',
            'konversi_satuan' => 'nullable|string|max:50',
        ]);

        $stockItem->update([
            'konversi_nilai' => $request->konversi_nilai,
            'konversi_satuan' => $request->konversi_satuan,
        ]);

        return redirect()->back()->with('success', 'Konversi stok berhasil diperbarui.');
    }
}
