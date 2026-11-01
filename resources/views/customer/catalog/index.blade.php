@extends('layouts.app')
@section('title', 'Katalog')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold">Katalog Produk</h1>

        <form method="GET" action="{{ route('customer.catalog.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari produk"
                   class="w-64 rounded-lg border px-3 py-2 text-sm">
            <button class="rounded-lg bg-indigo-600 text-white px-4 py-2 text-sm">Cari</button>
        </form>
    </div>

    @if ($products->isEmpty())
        <div class="mt-6 rounded-xl bg-white border p-8 text-center text-gray-500">
            Produk tidak ditemukan.
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($products as $product)
                <div class="rounded-xl bg-white border p-5 flex flex-col">
                    <div class="h-32 rounded-lg bg-indigo-50 overflow-hidden flex items-center justify-center text-indigo-300 text-sm">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            Tanpa gambar
                        @endif
                    </div>

                    <p class="mt-3 text-xs text-gray-500">{{ $product->merchant->store_name }}</p>
                    <p class="font-semibold">{{ $product->name }}</p>
                    <p class="mt-1 text-sm text-gray-600 flex-1">{{ \Illuminate\Support\Str::limit($product->description, 70) }}</p>

                    <div class="mt-3 flex items-center justify-between">
                        <p class="font-bold text-indigo-600">{{ $product->formatted_price }}</p>
                        <p class="text-xs text-gray-500">Stok {{ $product->stock }}</p>
                    </div>

                    <form method="POST" action="{{ route('customer.cart.store', $product) }}" class="mt-3 flex gap-2">
                        @csrf
                        <input type="number" name="quantity" value="1" min="1" max="{{ min(99, $product->stock) }}"
                               class="w-20 rounded-lg border px-3 py-2 text-sm">
                        <button class="flex-1 rounded-lg bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700">
                            Tambah ke Keranjang
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
@endsection