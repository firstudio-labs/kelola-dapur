<?php

namespace App\Http\Controllers\PenerimaMbg;

use App\Http\Controllers\Controller;
use App\Models\OrderDistribusiDetail;
use App\Models\OrderDistribusiDetailPenerimaanFoto;
use App\Models\PenerimaMbg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private function getPenerimaOrFail()
    {
        $user = Auth::user();
        $penerima = PenerimaMbg::whereHas(
            'userRole',
            fn($q) => $q->where('id_user', $user->id_user)
        )->first();

        if (!$penerima || !$penerima->isApproved()) {
            abort(403, 'Unauthorized');
        }

        return $penerima;
    }

    public function index(Request $request)
    {
        $penerima = $this->getPenerimaOrFail();

        $query = OrderDistribusiDetail::where('id_penerima', $penerima->id_penerima)
            ->where('status', OrderDistribusiDetail::STATUS_SUDAH_DIKIRIM);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('orderDistribusi.orderProduksi.transaksiDapur.detailTransaksiDapur.menuMakanan', function($sq) use ($search) {
                    $sq->where('nama_menu', 'like', "%{$search}%");
                })
                ->orWhereHas('orderDistribusi.dapur', function($sq) use ($search) {
                    $sq->where('nama_dapur', 'like', "%{$search}%");
                });
            });
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_penerimaan', $request->status);
        }

        // Date Filter
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_dikirim', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_dikirim', '<=', $request->date_to);
        }

        // Stats (calculate before searching to keep them consistent, or after if you want them to reflect search)
        // Usually stats reflect the total for the user, but sometimes they reflect the filter.
        // Let's keep them as the total for the context, but the user might expect them to reflect filters.
        // The distributor's view seems to show overall totals (filtered by status in some cases).
        // Let's follow the distributor pattern: show totals for the whole set of 'Sudah Dikirim'.
        $baseQuery = OrderDistribusiDetail::where('id_penerima', $penerima->id_penerima)
            ->where('status', OrderDistribusiDetail::STATUS_SUDAH_DIKIRIM);

        $stats = [
            'total'    => (clone $baseQuery)->count(),
            'menunggu' => (clone $baseQuery)->where('status_penerimaan', OrderDistribusiDetail::STATUS_PENERIMAAN_MENUNGGU)->count(),
            'diterima' => (clone $baseQuery)->where('status_penerimaan', OrderDistribusiDetail::STATUS_PENERIMAAN_DITERIMA)->count(),
            'ditolak'  => (clone $baseQuery)->where('status_penerimaan', OrderDistribusiDetail::STATUS_PENERIMAAN_DITOLAK)->count(),
        ];

        $kiriman = $query->with([
                'orderDistribusi.orderProduksi.transaksiDapur.detailTransaksiDapur.menuMakanan',
                'orderDistribusi.dapur',
                'penerimaanFoto',
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('penerima_mbg.order.index', compact('penerima', 'kiriman', 'stats'));
    }

    public function show(OrderDistribusiDetail $detail)
    {
        $penerima = $this->getPenerimaOrFail();

        if ($detail->id_penerima !== $penerima->id_penerima) {
            abort(403, 'Unauthorized');
        }

        if ($detail->status !== OrderDistribusiDetail::STATUS_SUDAH_DIKIRIM) {
            return redirect()->route('penerima-mbg.history.index')
                ->with('error', 'Kiriman ini belum berstatus Sudah Dikirim.');
        }

        $detail->load([
            'orderDistribusi.orderProduksi.transaksiDapur.detailTransaksiDapur.menuMakanan',
            'orderDistribusi.dapur',
            'orderDistribusi.dokumentasi',
            'dokumentasi',
            'penerimaanFoto',
        ]);

        return view('penerima_mbg.order.show', compact('penerima', 'detail'));
    }

    public function store(Request $request, OrderDistribusiDetail $detail)
    {
        $penerima = $this->getPenerimaOrFail();

        if ($detail->id_penerima !== $penerima->id_penerima) {
            abort(403, 'Unauthorized');
        }

        if ($detail->status !== OrderDistribusiDetail::STATUS_SUDAH_DIKIRIM) {
            return redirect()->route('penerima-mbg.history.index')
                ->with('error', 'Kiriman ini belum berstatus Sudah Dikirim.');
        }

        if ($detail->status_penerimaan !== OrderDistribusiDetail::STATUS_PENERIMAAN_MENUNGGU) {
            return redirect()->back()
                ->with('error', 'Konfirmasi sudah pernah dikirimkan sebelumnya.');
        }

        $maxBesar = $detail->porsi_besar ?? 0;
        $maxKecil = $detail->porsi_kecil ?? 0;

        $request->validate([
            'status_penerimaan'   => 'required|in:diterima,ditolak',
            'foto'                => 'required|array|min:1',
            'foto.*'              => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'ulasan'              => 'nullable|string|max:2000',
            'porsi_besar_diterima' => "nullable|integer|min:0|max:{$maxBesar}",
            'porsi_kecil_diterima' => "nullable|integer|min:0|max:{$maxKecil}",
        ], [
            'foto.required'                => 'Harap unggah minimal 1 foto sebagai bukti.',
            'foto.min'                     => 'Harap unggah minimal 1 foto sebagai bukti.',
            'foto.*.image'                 => 'File harus berupa gambar.',
            'foto.*.mimes'                 => 'Format gambar harus jpeg, png, atau jpg.',
            'foto.*.max'                   => 'Ukuran foto maksimal 5 MB per file.',
            'porsi_besar_diterima.max'     => "Porsi besar diterima tidak boleh melebihi porsi yang dikirim ({$maxBesar}).",
            'porsi_kecil_diterima.max'     => "Porsi kecil diterima tidak boleh melebihi porsi yang dikirim ({$maxKecil}).",
        ]);

        DB::beginTransaction();
        try {
            $detail->status_penerimaan    = $request->status_penerimaan;
            $detail->ulasan               = $request->ulasan;
            $detail->porsi_besar_diterima = $request->filled('porsi_besar_diterima')
                ? (int) $request->porsi_besar_diterima
                : $detail->porsi_besar;
            $detail->porsi_kecil_diterima = $request->filled('porsi_kecil_diterima')
                ? (int) $request->porsi_kecil_diterima
                : $detail->porsi_kecil;
            $detail->save();

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            foreach ($request->file('foto') as $file) {
                $filename = time() . '_' . uniqid() . '.webp';
                $path = 'penerimaan/foto/' . $filename;
                $img = $manager->read($file->getRealPath());
                if ($img->width() > 1920) $img->scaleDown(width: 1920);
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $img->toWebp(80));
                OrderDistribusiDetailPenerimaanFoto::create([
                    'id_detail' => $detail->id_detail,
                    'path_foto' => $path,
                ]);
            }

            DB::commit();

            $label = $request->status_penerimaan === 'diterima' ? 'Diterima' : 'Tidak Diterima';
            return redirect()->route('penerima-mbg.history.show', $detail->id_detail)
                ->with('success', "Konfirmasi penerimaan berhasil disimpan sebagai \"{$label}\".");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save penerimaan konfirmasi', [
                'id_detail' => $detail->id_detail,
                'error'     => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Gagal menyimpan konfirmasi: ' . $e->getMessage());
        }
    }

    public function submitKritik(Request $request, OrderDistribusiDetail $detail)
    {
        $penerima = $this->getPenerimaOrFail();

        if ($detail->id_penerima !== $penerima->id_penerima) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($detail->status_penerimaan, [OrderDistribusiDetail::STATUS_PENERIMAAN_DITERIMA, OrderDistribusiDetail::STATUS_PENERIMAAN_DITOLAK])) {
            return redirect()->back()->with('error', 'Kritik hanya dapat dikirim setelah melakukan konfirmasi penerimaan.');
        }

        $request->validate([
            'kritik' => 'required|string|max:1000',
            'kritik_foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $detail->kritik = $request->kritik;

            if ($request->hasFile('kritik_foto')) {
                $file = $request->file('kritik_foto');
                $filename = 'kritik_' . time() . '_' . uniqid() . '.webp';
                $path = 'penerima_mbg/kritik/' . $filename;

                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($file);
                
                $image->scaleDown(width: 1200);
                $encoded = $image->toWebp(quality: 75);

                \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $encoded);

                $detail->kritik_foto = $path;
            }

            $detail->save();
            DB::commit();

            return redirect()->back()->with('success', 'Kritik berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit kritik', [
                'id_detail' => $detail->id_detail,
                'error'     => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Gagal menyimpan kritik: ' . $e->getMessage());
        }
    }
}
