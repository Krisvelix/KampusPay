<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant) {
            return redirect()->route('merchant.store.create');
        }

        if (! $merchant->isApproved()) {
            return view('merchant.products.index', compact('merchant'))->with('products', null);
        }

        $query = $merchant->products();

        if ($request->filled('search')) {
            $keyword = '%' . $request->input('search') . '%';
            $query->where('name', 'like', $keyword);
        }

        if ($request->input('stock_status') === 'in_stock') {
            $query->where('stock', '>', 0);
        } elseif ($request->input('stock_status') === 'out_of_stock') {
            $query->where('stock', '<=', 0);
        }

        if ($request->filled('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return view('merchant.products.index', compact('merchant', 'products'));
    }

    public function create(): View|RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant) {
            return redirect()->route('merchant.store.create');
        }

        if (! $merchant->isApproved()) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'Toko Anda belum disetujui oleh admin.');
        }

        return view('merchant.products.create', compact('merchant'));
    }

    public function store(Request $request): RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant || ! $merchant->isApproved()) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'Toko Anda belum disetujui oleh admin.');
        }

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'price'        => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'is_available' => ['nullable'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $merchant->products()->create($data);

        return redirect()->route('merchant.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View|RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant || $product->merchant_id !== $merchant->id) {
            abort(403, 'Anda tidak memiliki akses ke produk ini.');
        }

        return view('merchant.products.show', compact('product', 'merchant'));
    }

    public function edit(Product $product): View|RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant || $product->merchant_id !== $merchant->id) {
            abort(403, 'Anda tidak memiliki akses ke produk ini.');
        }

        return view('merchant.products.edit', compact('product', 'merchant'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant || $product->merchant_id !== $merchant->id) {
            abort(403, 'Anda tidak memiliki akses ke produk ini.');
        }

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'price'        => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'is_available' => ['nullable'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('merchant.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant || $product->merchant_id !== $merchant->id) {
            abort(403, 'Anda tidak memiliki akses ke produk ini.');
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('merchant.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function toggleAvailable(Product $product): RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant || $product->merchant_id !== $merchant->id) {
            abort(403, 'Anda tidak memiliki akses ke produk ini.');
        }

        $product->update(['is_available' => ! $product->is_available]);

        $statusText = $product->is_available ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status ketersediaan produk {$product->name} berhasil {$statusText}.");
    }

    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant || $product->merchant_id !== $merchant->id) {
            abort(403, 'Anda tidak memiliki akses ke produk ini.');
        }

        $data = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $product->update(['stock' => $data['stock']]);

        return back()->with('success', "Stok produk {$product->name} berhasil diperbarui menjadi {$product->stock}.");
    }
}
