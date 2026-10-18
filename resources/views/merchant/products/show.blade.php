@extends('layouts.app')
@section('title', 'Detail Produk - ' . $product->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('merchant.products.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Daftar Produk</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Detail Produk</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('merchant.products.edit', $product) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                ✏️ Edit Produk
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2">
            {{-- Image Box --}}
            <div class="h-64 md:h-full min-h-[260px] bg-gray-50 flex items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 overflow-hidden p-4">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="max-h-72 w-auto object-contain rounded-xl shadow-sm">
                @else
                    <div class="text-center text-gray-400">
                        <span class="text-6xl block mb-2">🍽️</span>
                        <span class="text-xs">Belum ada foto produk</span>
                    </div>
                @endif
            </div>

            {{-- Info Box --}}
            <div class="p-6 md:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $product->is_available ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $product->is_available ? '● Tersedia untuk Dipesan' : '○ Nonaktif / Tidak Dijual' }}
                        </span>
                        @if ($product->stock <= 0)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-800">Stok Habis</span>
                        @endif
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                    <p class="text-2xl font-extrabold text-indigo-600 mt-2">{{ $product->formatted_price }}</p>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Deskripsi</span>
                        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                            {{ $product->description ?? 'Tidak ada deskripsi untuk produk ini.' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Stok Tersedia</span>
                        <span class="text-base font-bold {{ $product->stock > 0 ? 'text-gray-900' : 'text-red-600' }}">
                            {{ $product->stock }} unit
                        </span>
                    </div>

                    {{-- Quick update stock --}}
                    <form method="POST" action="{{ route('merchant.products.update-stock', $product) }}" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="stock" value="{{ $product->stock }}" min="0" required
                               class="w-28 border rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold transition">
                            Perbarui Stok
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Store Info Card --}}
    <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-5 flex items-center justify-between">
        <div>
            <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Toko Penjual</div>
            <div class="font-bold text-gray-900 mt-0.5">{{ $merchant->store_name }}</div>
            <div class="text-xs text-gray-500 mt-0.5">📍 {{ $merchant->store_address ?? 'Kampus' }} • 📞 {{ $merchant->phone_number ?? '-' }}</div>
        </div>
        <a href="{{ route('merchant.store.show') }}" class="px-3.5 py-1.5 bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-50 rounded-lg text-xs font-medium transition">
            Lihat Profil Toko
        </a>
    </div>
</div>
@endsection
