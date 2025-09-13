<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::latest()->paginate(10);

        return view('superadmin.promo-codes.index', compact('promoCodes'));
    }

    public function create()
    {
        return view('superadmin.promo-codes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_promo' => 'required|string|max:50|unique:promo_codes,kode_promo',
            'persentase_diskon' => 'required|integer|min:1|max:100',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean'
        ]);

        PromoCode::create([
            'kode_promo' => strtoupper($request->kode_promo),
            'persentase_diskon' => $request->persentase_diskon,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('superadmin.promo-codes.index')
            ->with('success', 'Kode promo berhasil dibuat');
    }

    public function show(PromoCode $promoCode)
    {
        $promoCode->load('subscriptionRequests.dapur');

        return view('superadmin.promo-codes.show', compact('promoCode'));
    }

    public function edit(PromoCode $promoCode)
    {
        return view('superadmin.promo-codes.edit', compact('promoCode'));
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $request->validate([
            'kode_promo' => 'required|string|max:50|unique:promo_codes,kode_promo,' . $promoCode->id_promo . ',id_promo',
            'persentase_diskon' => 'required|integer|min:1|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean'
        ]);

        $promoCode->update([
            'kode_promo' => strtoupper($request->kode_promo),
            'persentase_diskon' => $request->persentase_diskon,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('superadmin.promo-codes.index')
            ->with('success', 'Kode promo berhasil diperbarui');
    }

    public function destroy(PromoCode $promoCode)
    {
        // Cek apakah ada subscription request yang menggunakan promo ini
        if ($promoCode->subscriptionRequests()->exists()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus kode promo yang sudah digunakan');
        }

        $promoCode->delete();

        return redirect()->route('superadmin.promo-codes.index')
            ->with('success', 'Kode promo berhasil dihapus');
    }

    public function toggleStatus(PromoCode $promoCode)
    {
        $promoCode->update([
            'is_active' => !$promoCode->is_active
        ]);

        $status = $promoCode->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Kode promo berhasil {$status}");
    }

    // API untuk validasi promo code
    public function validatePromo(Request $request)
    {
        $request->validate([
            'kode_promo' => 'required|string'
        ]);

        $promoCode = PromoCode::where('kode_promo', strtoupper($request->kode_promo))->first();

        if (!$promoCode) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak ditemukan'
            ]);
        }

        if (!$promoCode->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak valid atau sudah kadaluarsa'
            ]);
        }

        return response()->json([
            'valid' => true,
            'data' => [
                'id_promo' => $promoCode->id_promo,
                'kode_promo' => $promoCode->kode_promo,
                'persentase_diskon' => $promoCode->persentase_diskon,
                'message' => "Diskon {$promoCode->persentase_diskon}% berhasil diterapkan"
            ]
        ]);
    }
}
