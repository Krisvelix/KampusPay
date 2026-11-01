@extends('layouts.app')
@section('title', 'Keranjang')

@section('content')
    <h1 class="text-2xl font-semibold">Keranjang Belanja</h1>

    @if ($groups->isEmpty())
        <div class="mt-6 rounded-xl bg-white border p-8 text-center text-gray-500">
            Keranjangmu masih kosong.
            <a href="{{ route('customer.catalog.index') }}" class="text-indigo-600 hover:underline">Lihat katalog</a>
        </div>
    @else
        <div class="mt-6 space-y-6">
            @foreach ($groups as $items)
                <div class="rounded-xl bg-white border">
                    <div class="px-5 py-3 border-b bg-gray-50 rounded-t-xl">
                        <p class="text-xs text-gray-500">Toko</p>
                        <p class="font-semibold">{{ $items->first()->product->merchant->store_name }}</p>
                    </div>

                    <div class="divide-y">
                        @foreach ($items as $item)
                            <div class="px-5 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <p class="font-medium">{{ $item->product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->product->formatted_price }} &middot; stok {{ $item->product->stock }}</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <form method="POST" action="{{ route('customer.cart.update', $item) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99"
                                               class="w-20 rounded-lg border px-3 py-1.5 text-sm">
                                        <button class="rounded-lg border bg-white px-3 py-1.5 text-sm hover:bg-gray-50">Ubah</button>
                                    </form>

                                    <p class="w-28 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>

                                    <form method="POST" action="{{ route('customer.cart.destroy', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @php($insufficient = bccomp($balance, $total, 2) < 0)

        <div class="mt-6 rounded-xl bg-white border p-5">
            <div class="flex items-center justify-between">
                <p class="text-gray-500">Saldo dompet</p>
                <p class="font-semibold">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <p class="text-gray-500">Total</p>
                <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($total, 0, ',', '.') }}</p>
            </div>

            @if ($insufficient)
                <div class="mt-4 rounded-lg bg-red-100 text-red-800 px-4 py-3 text-sm">
                    Saldo tidak cukup.
                    <a href="{{ route('customer.topup.create') }}" class="underline font-medium">Top up dulu</a>
                </div>
            @endif

            <form method="POST" action="{{ route('customer.checkout.store') }}" class="mt-4">
                @csrf
                <label for="notes" class="text-sm text-gray-500">Catatan untuk penjual (opsional)</label>
                <input type="text" id="notes" name="notes" maxlength="255" value="{{ old('notes') }}"
                       class="mt-1 w-full rounded-lg border px-3 py-2 text-sm">
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <button class="mt-4 w-full rounded-lg bg-indigo-600 text-white px-4 py-3 font-medium hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed"
                        @disabled($insufficient)>
                    Bayar Rp {{ number_format($total, 0, ',', '.') }}
                </button>
            </form>
        </div>
    @endif
@endsection