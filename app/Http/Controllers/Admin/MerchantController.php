<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MerchantController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'status' => ['nullable', Rule::in([
                Merchant::STATUS_PENDING,
                Merchant::STATUS_APPROVED,
                Merchant::STATUS_REJECTED,
            ])],
        ]);

        $merchants = Merchant::with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.merchants.index', compact('merchants'));
    }

    public function approve(Merchant $merchant): RedirectResponse
    {
        $merchant->update([
            'status' => Merchant::STATUS_APPROVED,
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Toko {$merchant->store_name} disetujui.");
    }

    public function reject(Request $request, Merchant $merchant): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $merchant->update([
            'status' => Merchant::STATUS_REJECTED,
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return back()->with('success', "Toko {$merchant->store_name} ditolak.");
    }
}