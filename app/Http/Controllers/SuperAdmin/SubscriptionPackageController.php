<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionPackageController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::latest()->paginate(10);

        return view('superadmin.subscription-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('superadmin.subscription-packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'durasi_hari' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        SubscriptionPackage::create([
            'nama_paket' => $request->nama_paket,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'durasi_hari' => $request->durasi_hari,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('superadmin.subscription-packages.index')
            ->with('success', 'Paket subscription berhasil dibuat');
    }

    public function show(SubscriptionPackage $subscriptionPackage)
    {
        $subscriptionPackage->load('subscriptionRequests.dapur');

        return view('superadmin.subscription-packages.show', compact('subscriptionPackage'));
    }

    public function edit(SubscriptionPackage $subscriptionPackage)
    {
        return view('superadmin.subscription-packages.edit', compact('subscriptionPackage'));
    }

    public function update(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'durasi_hari' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $subscriptionPackage->update([
            'nama_paket' => $request->nama_paket,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'durasi_hari' => $request->durasi_hari,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('superadmin.subscription-packages.index')
            ->with('success', 'Paket subscription berhasil diperbarui');
    }

    public function destroy(SubscriptionPackage $subscriptionPackage)
    {
        // Cek apakah ada subscription request yang menggunakan package ini
        if ($subscriptionPackage->subscriptionRequests()->exists()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus paket yang sudah digunakan');
        }

        $subscriptionPackage->delete();

        return redirect()->route('superadmin.subscription-packages.index')
            ->with('success', 'Paket subscription berhasil dihapus');
    }

    public function toggleStatus(SubscriptionPackage $subscriptionPackage)
    {
        $subscriptionPackage->update([
            'is_active' => !$subscriptionPackage->is_active
        ]);

        $status = $subscriptionPackage->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Paket subscription berhasil {$status}");
    }
}
