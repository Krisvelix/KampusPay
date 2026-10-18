@extends('layouts.app')
@section('title', 'Kelola Produk Toko')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Kelola Produk</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar produk dan pengelolaan stok toko {{ $merchant->store_name }}</p>
    </div>
    @if ($merchant->isApproved())
        <a href="{{ route('merchant.products.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium shadow-sm transition self-start sm:self-auto">
            + Tambah Produk Baru
        </a>
    @endif
</div>

@if (! $merchant->isApproved())
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
        <span class="text-3xl">⏳</span>
        <h2 class="text-lg font-bold text-amber-900 mt-2">Toko Belum Disetujui</h2>
        <p class="text-sm text-amber-800 max-w-md mx-auto mt-1">
            Status tokomu saat ini adalah <strong>{{ ucfirst($merchant->status) }}</strong>. Kamu baru dapat menambah dan mengelola produk setelah tokomu disetujui oleh admin KampusPay.
        </p>
        <div class="mt-4">
            <a href="{{ route('merchant.dashboard') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Dashboard</a>
        </div>
    </div>
@else
    {{-- SEARCH & FILTER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('merchant.products.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..."
                       class="w-full border rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <select name="stock_status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Stok</option>
                    <option value="in_stock" @selected(request('stock_status') === 'in_stock')>Stok Tersedia (>0)</option>
                    <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>Stok Habis (0)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium px-4 py-2 transition">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'stock_status', 'available']))
                    <a href="{{ route('merchant.products.index') }}" class="px-3 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50 flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- PRODUCTS TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="py-3.5 px-4">Produk</th>
                        <th class="py-3.5 px-4">Harga</th>
                        <th class="py-3.5 px-4">Stok</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl text-gray-400">🍽️</span>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('merchant.products.show', $product) }}" class="font-semibold text-gray-900 hover:text-indigo-600 transition">
                                        {{ $product->name }}
                                    </a>
                                    @if ($product->description)
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ Str::limit($product->description, 60) }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 font-semibold text-gray-900 whitespace-nowrap">
                            {{ $product->formatted_price }}
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-lg {{ $product->stock > 0 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                    {{ $product->stock }} unit
                                </span>
                                {{-- Quick stock update form --}}
                                <form method="POST" action="{{ route('merchant.products.update-stock', $product) }}" class="inline-flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="stock" value="{{ $product->stock }}" min="0" class="w-16 border rounded px-1.5 py-0.5 text-xs text-center" title="Ubah cepat stok">
                                    <button type="submit" class="text-xs px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 font-medium" title="Simpan Stok">
                                        ✓
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <form method="POST" action="{{ route('merchant.products.toggle-available', $product) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $product->is_available ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}" title="Klik untuk ubah status ketersediaan">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $product->is_available ? 'bg-emerald-600' : 'bg-gray-400' }}"></span>
                                    {{ $product->is_available ? 'Tersedia' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('merchant.products.show', $product) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg transition">
                                    Detail
                                </a>
                                <a href="{{ route('merchant.products.edit', $product) }}" class="text-xs font-medium text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-2.5 py-1.5 rounded-lg transition">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('merchant.products.destroy', $product) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400">
                            <div class="text-4xl mb-2">🍽️</div>
                            <p class="font-medium text-gray-600">Belum ada produk yang ditemukan.</p>
                            <p class="text-xs text-gray-400 mt-1">Mulai tambahkan produk tokomu sekarang.</p>
                            <a href="{{ route('merchant.products.create') }}" class="inline-block mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-medium transition">
                                + Tambah Produk
                            </a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endif
@endsection
