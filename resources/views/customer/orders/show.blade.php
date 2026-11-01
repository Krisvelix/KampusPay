@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Detail Pesanan</h1>
        <a href="{{ route('customer.orders.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Semua pesanan</a>
    </div>

    <div class="mt-6 rounded-xl bg-white border">
        <div class="px-5 py-4 border-b flex flex-col md:flex-row md:items-center justify-between gap-2">
            <div>
                <p class="text-xs text-gray-500">Nomor pesanan</p>
                <p class="font-semibold">#{{ $order->order_number }}</p>
            </div>
            <div class="md:text-right">
                <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $order->status_badge }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <div class="px-5 py-4 border-b">
            <p class="text-xs text-gray-500">Toko</p>
            <p class="font-medium">{{ $order->merchant->store_name }}</p>
        </div>

        <div class="divide-y">
            @foreach ($order->items as $item)
                <div class="px-5 py-3 flex items-center justify-between gap-4">
                    <div>
                        <p class="font-medium">{{ $item->product_name }}</p>
                        <p class="text-sm text-gray-500">{{ $item->quantity }} x {{ $item->formatted_price }}</p>
                    </div>
                    <p class="font-semibold">{{ $item->formatted_subtotal }}</p>
                </div>
            @endforeach
        </div>

        <div class="px-5 py-4 border-t flex items-center justify-between">
            <p class="text-gray-500">Total</p>
            <p class="text-xl font-bold text-indigo-600">{{ $order->formatted_total }}</p>
        </div>

        @if ($order->notes)
            <div class="px-5 py-4 border-t">
                <p class="text-xs text-gray-500">Catatan</p>
                <p class="text-sm">{{ $order->notes }}</p>
            </div>
        @endif
    </div>

    <div class="mt-6 rounded-xl bg-white border p-5">
        <p class="font-semibold">Pembayaran</p>
        @if ($payment)
            <div class="mt-3 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Referensi</span><span>{{ $payment->reference }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Dibayar</span><span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Saldo sebelum</span><span>Rp {{ number_format($payment->balance_before, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Saldo sesudah</span><span>Rp {{ number_format($payment->balance_after, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Waktu</span><span>{{ $payment->created_at->format('d M Y H:i') }}</span></div>
            </div>
        @else
            <p class="mt-2 text-sm text-gray-500">Catatan pembayaran dompet tidak tersedia untuk pesanan ini.</p>
        @endif
    </div>
@endsection