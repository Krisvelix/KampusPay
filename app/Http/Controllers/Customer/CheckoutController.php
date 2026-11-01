<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class CheckoutController extends Controller
{
    public function store(Request $request, CheckoutService $checkout): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $orders = $checkout->checkout($request->user(), $data['notes'] ?? null);
        } catch (RuntimeException $e) {
            return redirect()->route('customer.cart.index')->with('error', $e->getMessage());
        }

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Pembayaran berhasil. ' . $orders->count() . ' pesanan dibuat.');
    }
}