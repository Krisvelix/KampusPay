<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $items = $user->cartItems()
            ->with('product.merchant')
            ->get();

        $groups = $items->groupBy(fn (CartItem $item) => $item->product->merchant_id);

        $total = $items->reduce(
            fn (string $carry, CartItem $item) => bcadd($carry, $item->subtotal, 2),
            '0.00'
        );

        $balance = (string) $user->ensureWallet()->balance;

        return view('customer.cart.index', compact('groups', 'total', 'balance'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $quantity = $data['quantity'] ?? 1;

        if (! $product->is_available || $product->stock < 1 || ! $product->merchant->isApproved()) {
            return back()->with('error', 'Produk tidak tersedia.');
        }

        $item = $request->user()->cartItems()->firstOrNew(['product_id' => $product->id]);
        $newQuantity = ($item->exists ? $item->quantity : 0) + $quantity;

        if ($newQuantity > $product->stock) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $item->quantity = $newQuantity;
        $item->save();

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        if ($data['quantity'] > $cartItem->product->stock) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $cartItem->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);

        $cartItem->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}