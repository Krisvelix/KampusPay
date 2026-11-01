@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
    <h1 class="text-2xl font-semibold">Pesanan Saya</h1>

    @if ($orders->isEmpty())
        <div class="mt-6 rounded-xl bg-white border p-8 text-center text-gray-500">
            Belum ada pesanan.
            <a href="{{ route('customer.catalog.index') }}" class="text-indigo-600 hover:underline">Lihat katalog</a>
        </div>
    @else
        <div class="mt-6 rounded-xl bg-white border divide-y">
            @foreach ($orders as $order)
                <a href="{{ route('customer.orders.show', $order) }}" class="px-5 py-4 flex items-center justify-between gap-4 hover:bg-gray-50">
                    <div>
                        <p class="font-medium">#{{ $order->order_number }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $order->merchant->store_name }} &middot; {{ $order->items_count }} produk &middot; {{ $order->created_at->format('d M Y H:i') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">{{ $order->formatted_total }}</p>
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $order->status_badge }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
@endsection