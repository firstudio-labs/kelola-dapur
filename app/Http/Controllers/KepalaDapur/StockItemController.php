<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\TemplateItem;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('kepaladapur.stock.index', compact(
            'stockItems',
            'dapur',
            'totalItems',
            'habisStok',
            'rendahStok',
            'normalStok',
            'availableSatuans'
        ));
    }
}
