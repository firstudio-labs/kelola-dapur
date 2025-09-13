<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Models\PromoCode;
use App\Models\SubscriptionRequest;
use App\Models\Dapur;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Dapur $dapur)
    {
        $dapur->load(['subscriptionRequests' => function ($query) {
            $query->with(['package', 'promoCode'])->latest('tanggal_request');
        }]);

        $currentSubscription = $dapur->activeSubscriptionRequest;
        $pendingRequest = $dapur->pendingSubscriptionRequest;

        return view('kepaladapur.subscription.index', compact('dapur', 'currentSubscription', 'pendingRequest'));
    }

    public function choosePackage(Dapur $dapur)
    {
        // Cek apakah sudah ada pending request
        $pendingRequest = $dapur->pendingSubscriptionRequest;
        if ($pendingRequest) {
            return redirect()->route('kepala-dapur.subscription.index', $dapur)
                ->with('warning', 'Anda masih memiliki request subscription yang belum diproses');
        }

        $packages = SubscriptionPackage::active()->get();

        return view('kepaladapur.subscription.choose-package', compact('dapur', 'packages'));
    }

    public function processPayment(Request $request, Dapur $dapur)
    {
        $request->validate([
            'id_package' => 'required|exists:subscription_packages,id_package',
            'kode_promo' => 'nullable|string',
            'bukti_transfer' => 'nullable|image|max:2048' // max 2MB
        ]);

        // Validasi tidak ada pending request
        if ($dapur->pendingSubscriptionRequest) {
            return redirect()->back()
                ->with('error', 'Anda masih memiliki request subscription yang belum diproses');
        }

        $package = SubscriptionPackage::findOrFail($request->id_package);
        if (!$package->is_active) {
            return redirect()->back()
                ->with('error', 'Paket subscription tidak aktif');
        }

        $promoCode = null;
        if ($request->kode_promo) {
            $promoCode = PromoCode::where('kode_promo', strtoupper($request->kode_promo))->first();
            if (!$promoCode || !$promoCode->isValid()) {
                return redirect()->back()
                    ->with('error', 'Kode promo tidak valid atau sudah kadaluarsa')
                    ->withInput();
            }
        }

        // Calculate pricing
        $pricing = SubscriptionRequest::calculatePrice($package, $dapur->id_dapur, $promoCode);

        // Handle file upload
        $buktiTransferPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiTransferPath = $request->file('bukti_transfer')->store('bukti-transfer', 'public');
        }

        // Create subscription request
        SubscriptionRequest::create([
            'id_dapur' => $dapur->id_dapur,
            'id_package' => $package->id_package,
            'id_promo' => $promoCode?->id_promo,
            'harga_asli' => $pricing['harga_asli'],
            'diskon' => $pricing['diskon'],
            'harga_final' => $pricing['harga_final'],
            'bukti_transfer' => $buktiTransferPath,
            'status' => 'pending'
        ]);

        return redirect()->route('kepala-dapur.subscription.index', $dapur)
            ->with('success', 'Request subscription berhasil dikirim. Silakan tunggu approval dari admin');
    }

    public function calculatePrice(Request $request, Dapur $dapur)
    {
        $request->validate([
            'id_package' => 'required|exists:subscription_packages,id_package',
            'kode_promo' => 'nullable|string'
        ]);

        $package = SubscriptionPackage::findOrFail($request->id_package);

        $promoCode = null;
        if ($request->kode_promo) {
            $promoCode = PromoCode::where('kode_promo', strtoupper($request->kode_promo))->first();
        }

        $pricing = SubscriptionRequest::calculatePrice($package, $dapur->id_dapur, $promoCode);

        return response()->json([
            'success' => true,
            'data' => [
                'harga_asli' => $pricing['harga_asli'],
                'diskon' => $pricing['diskon'],
                'harga_final' => $pricing['harga_final'],
                'formatted' => [
                    'harga_asli' => 'Rp ' . number_format($pricing['harga_asli'], 0, ',', '.'),
                    'diskon' => 'Rp ' . number_format($pricing['diskon'], 0, ',', '.'),
                    'harga_final' => 'Rp ' . number_format($pricing['harga_final'], 0, ',', '.')
                ],
                'promo_valid' => $promoCode && $promoCode->isValid(),
                'promo_message' => $promoCode && $promoCode->isValid()
                    ? "Diskon {$promoCode->persentase_diskon}% berhasil diterapkan"
                    : ($request->kode_promo ? 'Kode promo tidak valid' : null)
            ]
        ]);
    }

    public function show(Dapur $dapur, SubscriptionRequest $subscriptionRequest)
    {
        // Pastikan subscription request milik dapur ini
        if ($subscriptionRequest->id_dapur !== $dapur->id_dapur) {
            abort(404);
        }

        $subscriptionRequest->load(['package', 'promoCode']);

        return view('kepaladapur.subscription.show', compact('dapur', 'subscriptionRequest'));
    }

    public function cancel(Dapur $dapur, SubscriptionRequest $subscriptionRequest)
    {
        // Pastikan subscription request milik dapur ini dan masih pending
        if ($subscriptionRequest->id_dapur !== $dapur->id_dapur || $subscriptionRequest->status !== 'pending') {
            abort(404);
        }

        $subscriptionRequest->delete();

        return redirect()->route('kepala-dapur.subscription.index', $dapur)
            ->with('success', 'Request subscription berhasil dibatalkan');
    }
}
