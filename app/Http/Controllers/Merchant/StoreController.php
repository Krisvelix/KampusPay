<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (auth()->user()->merchant) {
            return redirect()->route('merchant.store.show');
        }

        return view('merchant.store.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->merchant) {
            return redirect()->route('merchant.store.show');
        }

        $data = $request->validate([
            'store_name'    => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:500'],
            'store_address' => ['required', 'string', 'max:255'],
            'phone_number'  => ['nullable', 'string', 'max:25'],
        ]);

        $data['status'] = Merchant::STATUS_PENDING;

        auth()->user()->merchant()->create($data);

        return redirect()->route('merchant.dashboard')
            ->with('success', 'Pengajuan toko berhasil dikirim! Menunggu verifikasi dari admin.');
    }

    public function show(): View|RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant) {
            return redirect()->route('merchant.store.create');
        }

        $products = $merchant->products()->latest()->paginate(8);

        return view('merchant.store.show', compact('merchant', 'products'));
    }

    public function edit(): View|RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant) {
            return redirect()->route('merchant.store.create');
        }

        return view('merchant.store.edit', compact('merchant'));
    }

    public function update(Request $request): RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant) {
            return redirect()->route('merchant.store.create');
        }

        $data = $request->validate([
            'store_name'    => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:500'],
            'store_address' => ['required', 'string', 'max:255'],
            'phone_number'  => ['nullable', 'string', 'max:25'],
        ]);

        // If the store was rejected and merchant re-submits changes, reset status to pending for review
        if ($merchant->isRejected()) {
            $data['status'] = Merchant::STATUS_PENDING;
            $data['rejection_reason'] = null;
        }

        $merchant->update($data);

        $message = $merchant->isPending()
            ? 'Informasi toko diperbarui dan pengajuan diajukan ulang untuk verifikasi admin.'
            : 'Informasi toko berhasil diperbarui.';

        return redirect()->route('merchant.store.show')->with('success', $message);
    }
}
