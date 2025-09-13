<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;

class SubscriptionRequestController extends Controller
{
    public function index()
    {
        $subscriptionRequests = SubscriptionRequest::with(['dapur', 'package', 'promoCode'])
            ->latest('tanggal_request')
            ->paginate(15);

        return view('superadmin.subscription-requests.index', compact('subscriptionRequests'));
    }

    public function show(SubscriptionRequest $subscriptionRequest)
    {
        $subscriptionRequest->load(['dapur', 'package', 'promoCode']);

        return view('superadmin.subscription-requests.show', compact('subscriptionRequest'));
    }

    public function approve(Request $request, SubscriptionRequest $subscriptionRequest)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500'
        ]);

        if ($subscriptionRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Request subscription sudah diproses sebelumnya');
        }

        if ($subscriptionRequest->approve($request->catatan)) {
            return redirect()->route('superadmin.subscription-requests.index')
                ->with('success', 'Request subscription berhasil disetujui');
        }

        return redirect()->back()
            ->with('error', 'Gagal memproses approval');
    }

    public function reject(Request $request, SubscriptionRequest $subscriptionRequest)
    {
        $request->validate([
            'catatan' => 'required|string|max:500'
        ]);

        if ($subscriptionRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Request subscription sudah diproses sebelumnya');
        }

        if ($subscriptionRequest->reject($request->catatan)) {
            return redirect()->route('superadmin.subscription-requests.index')
                ->with('success', 'Request subscription berhasil ditolak');
        }

        return redirect()->back()
            ->with('error', 'Gagal memproses rejection');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'selected_requests' => 'required|array|min:1',
            'selected_requests.*' => 'exists:subscription_requests,id_subscription_request',
            'catatan' => 'nullable|string|max:500'
        ]);

        $requests = SubscriptionRequest::whereIn('id_subscription_request', $request->selected_requests)
            ->where('status', 'pending')
            ->get();

        $processed = 0;
        foreach ($requests as $subscriptionRequest) {
            if ($request->action === 'approve') {
                if ($subscriptionRequest->approve($request->catatan)) {
                    $processed++;
                }
            } else {
                if ($subscriptionRequest->reject($request->catatan ?? 'Ditolak melalui bulk action')) {
                    $processed++;
                }
            }
        }

        $action = $request->action === 'approve' ? 'disetujui' : 'ditolak';

        return redirect()->back()
            ->with('success', "{$processed} request subscription berhasil {$action}");
    }

    // Filter methods
    public function pending()
    {
        $subscriptionRequests = SubscriptionRequest::with(['dapur', 'package', 'promoCode'])
            ->pending()
            ->latest('tanggal_request')
            ->paginate(15);

        return view('superadmin.subscription-requests.index', compact('subscriptionRequests'));
    }

    public function approved()
    {
        $subscriptionRequests = SubscriptionRequest::with(['dapur', 'package', 'promoCode'])
            ->approved()
            ->latest('tanggal_approval')
            ->paginate(15);

        return view('superadmin.subscription-requests.index', compact('subscriptionRequests'));
    }

    public function rejected()
    {
        $subscriptionRequests = SubscriptionRequest::with(['dapur', 'package', 'promoCode'])
            ->rejected()
            ->latest('tanggal_approval')
            ->paginate(15);

        return view('superadmin.subscription-requests.index', compact('subscriptionRequests'));
    }
}
