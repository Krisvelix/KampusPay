@extends('layouts.app')
@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('merchant.orders.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Daftar Pesanan</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Detail Pesanan #{{ $order->order_number }}</h1>
            <p class="text-xs text-gray-400">Diterima pada {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div>
            <span class="inline-block px-3 py-1.5 rounded-full text-xs font-bold {{ $order->status_badge }}">
                Status: {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    {{-- STATUS CHANGER CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-sm font-bold text-gray-900 mb-3">Perbarui Status Pesanan</h2>
        <form method="POST" action="{{ route('merchant.orders.update-status', $order) }}" class="flex flex-wrap items-center gap-3">
            @csrf
            @method('PATCH')

            <select name="status" class="border rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium">
                <option value="pending" @selected($order->status === 'pending')>⏳ Menunggu Diproses (Pending)</option>
                <option value="processing" @selected($order->status === 'processing')>👨‍🍳 Sedang Diproses / Disiapkan (Processing)</option>
                <option value="completed" @selected($order->status === 'completed')>✅ Selesai Diambil / Diantar (Completed)</option>
                <option value="cancelled" @selected($order->status === 'cancelled')>❌ Batalkan Pesanan (Cancelled)</option>
            </select>

            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition">
                Simpan Status
            </button>
        </form>
    </div>

    {{-- BUYER & ORDER DETAILS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">Informasi Pembeli</span>
            <div class="font-bold text-gray-900 text-base">{{ $order->customer->name ?? 'User Pembeli' }}</div>
            <div class="text-sm text-gray-500 mt-0.5">✉️ {{ $order->customer->email ?? '-' }}</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">Status Pembayaran</span>
            <div class="font-bold text-emerald-600 text-base">✓ {{ ucfirst($order->payment_status) }}</div>
            <div class="text-xs text-gray-500 mt-0.5">Metode: KampusPay Balance</div>
        </div>
    </div>

    @if ($order->notes)
        <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-900">
            <span class="font-bold block mb-1">Catatan Pembeli:</span>
            {{ $order->notes }}
        </div>
    @endif

    {{-- ORDER ITEMS TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-sm text-gray-700">
            Rincian Item yang Dipesan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-gray-500 border-b border-gray-100 text-xs">
                    <tr>
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="py-3.5 px-4 font-medium text-gray-900">
                            {{ $item->product_name }}
                        </td>
                        <td class="py-3.5 px-4 text-right text-gray-600">
                            {{ $item->formatted_price }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-semibold text-gray-800">
                            {{ $item->quantity }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-gray-900">
                            {{ $item->formatted_subtotal }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot class="bg-gray-50/70 border-t-2 border-gray-200 font-bold text-gray-900">
                    <tr>
                        <td colspan="3" class="py-4 px-4 text-right">Total Transaksi:</td>
                        <td class="py-4 px-4 text-right text-lg text-indigo-600">{{ $order->formatted_total }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
