<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduksi extends Model
{
    use HasFactory;

    protected $table = 'order_produksi';
    protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_transaksi',
        'id_dapur',
        'status',
        'catatan',
        'ulasan',
        'ulasan_foto',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_STOK_KURANG   = 'stok_kurang';
    const STATUS_BELUM_DIBUAT  = 'belum_dibuat';
    const STATUS_SEDANG_DIBUAT = 'sedang_dibuat';
    const STATUS_SELESAI       = 'selesai';

    public static function statusLabel(): array
    {
        return [
            self::STATUS_STOK_KURANG   => 'Stok Kurang',
            self::STATUS_BELUM_DIBUAT  => 'Belum Dibuat',
            self::STATUS_SEDANG_DIBUAT => 'Sedang Dibuat',
            self::STATUS_SELESAI       => 'Selesai',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel()[$this->status] ?? $this->status;
    }

    public function isStokKurang(): bool
    {
        return $this->status === self::STATUS_STOK_KURANG;
    }

    public function transaksiDapur()
    {
        return $this->belongsTo(TransaksiDapur::class, 'id_transaksi', 'id_transaksi');
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function dokumentasi()
    {
        return $this->hasMany(OrderProduksiDokumentasi::class, 'id_order', 'id_order');
    }

    public function distribusiOrder()
    {
        return $this->hasOne(OrderDistribusi::class, 'id_order', 'id_order');
    }

    public function deductAdjustedStockForProduction(): array
    {
        $result = [
            'success'   => false,
            'message'   => '',
        ];

        $transaksi = $this->transaksiDapur;
        if (!$transaksi) {
            $result['message'] = 'Transaksi Dapur tidak ditemukan';
            return $result;
        }

        $baseIngredients = [];
        foreach ($transaksi->detailTransaksiDapur as $detail) {
            $reqs = $detail->getRequiredIngredients();
            foreach ($reqs as $ing) {
                $id = $ing['id_template_item'];
                if (!isset($baseIngredients[$id])) {
                    $baseIngredients[$id] = [
                        'id_template_item' => $id,
                        'nama_bahan' => $ing['nama_bahan'],
                        'amount' => 0,
                    ];
                }
                $amountToReduce = isset($ing['is_bahan_basah']) && $ing['is_bahan_basah']
                    ? $ing['total_berat_basah']
                    : $ing['total_needed'];
                $baseIngredients[$id]['amount'] += $amountToReduce;
            }
        }

        $handlers = \App\Models\ProduksiHandlerBahan::where('id_order', $this->id_order)
            ->whereIn('status', ['approved', 'resolved'])
            ->get();

        foreach ($handlers as $h) {
            $id = $h->id_template_item;
            if (!isset($baseIngredients[$id])) {
                $baseIngredients[$id] = [
                    'id_template_item' => $id,
                    'nama_bahan' => 'Bahan Tambahan',
                    'amount' => 0,
                ];
            }
            if ($h->jenis === 'kekurangan') {
                $baseIngredients[$id]['amount'] += (float)$h->jumlah;
            } else if ($h->jenis === 'kelebihan') {
                $baseIngredients[$id]['amount'] -= (float)$h->jumlah;
            }
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($baseIngredients as $id => $data) {
                if ($data['amount'] <= 0) continue;

                $stockItem = \App\Models\StockItem::where('id_dapur', $this->id_dapur)
                    ->where('id_template_item', $id)
                    ->lockForUpdate()
                    ->first();

                if ($stockItem) {
                    $amountToReduce = $data['amount'];
                    $actual = (float) $stockItem->jumlah;
                    
                    $deduct = min($actual, $amountToReduce);
                    if ($deduct > 0) {
                        $stockItem->jumlah -= $deduct;
                        $stockItem->save();
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            $result['success'] = true;
            $result['message'] = 'Stok berhasil dikurangkan sesuai dengan perhitungan terbaru.';
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Stock adjusted reduction failed for order', [
                'id_order' => $this->id_order,
                'error'    => $e->getMessage(),
            ]);
            $result['message'] = 'Gagal mengurangi stok: ' . $e->getMessage();
        }

        return $result;
    }
}
