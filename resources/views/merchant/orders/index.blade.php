@extends('layouts.app')
@section('title', 'Pesanan Masuk - Merchant')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pesanan Masuk</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar transaksi dan pesanan pelanggan untuk toko {{ $merchant->store_name }}</p>
    </div>
    <a href="{{ route('merchant.dashboard') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Dashboard</a>
</div>

@if (! $merchant->isApproved())
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
        <span class="text-3xl">⏳</span>
        <h2 class="text-lg font-bold text-amber-900 mt-2">Toko Belum Disetujui</h2>
        <p class="text-sm text-amber-800 max-w-md mx-auto mt-1">
            Status tokomu saat ini adalah <strong>{{ ucfirst($merchant->status) }}</strong>. Toko harus disetujui terlebih dahulu oleh admin untuk dapat menerima pesanan.
        </p>
    </div>
@else
    {{-- STATUS TABS --}}
    <div class="flex flex-wrap gap-2 mb-6 text-sm">
        <a href="{{ route('merchant.orders.index') }}"
           class="px-3.5 py-2 rounded-xl font-medium transition {{ !request('status') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            Semua Pesanan ({{ $statusCounts['all'] }})
        </a>
        <a href="{{ route('merchant.orders.index', ['status' => 'pending']) }}"
           class="px-3.5 py-2 rounded-xl font-medium transition {{ request('status') === 'pending' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            Pending ({{ $statusCounts['pending'] }})
        </a>
        <a href="{{ route('merchant.orders.index', ['status' => 'processing']) }}"
           class="px-3.5 py-2 rounded-xl font-medium transition {{ request('status') === 'processing' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            Diproses ({{ $statusCounts['processing'] }})
        </a>
        <a href="{{ route('merchant.orders.index', ['status' => 'completed']) }}"
           class="px-3.5 py-2 rounded-xl font-medium transition {{ request('status') === 'completed' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            Selesai ({{ $statusCounts['completed'] }})
        </a>
        <a href="{{ route('merchant.orders.index', ['status' => 'cancelled']) }}"
           class="px-3.5 py-2 rounded-xl font-medium transition {{ request('status') === 'cancelled' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            Dibatalkan ({{ $statusCounts['cancelled'] }})
        </a>
    </div>

    {{-- SEARCH --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('merchant.orders.index') }}" class="flex gap-2">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan (#KP-...) atau nama pembeli..."
                   class="flex-1 border rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium px-5 py-2 transition">
                Cari
            </button>
            @if (request()->hasAny(['search', 'status']))
                <a href="{{ route('merchant.orders.index') }}" class="px-3 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50 flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- ORDERS TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="py-3.5 px-4">No. Pesanan & Waktu</th>
                        <th class="py-3.5 px-4">Pembeli</th>
                        <th class="py-3.5 px-4">Item Pesanan</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-4 px-4 whitespace-nowrap">
                            <a href="{{ route('merchant.orders.show', $order) }}" class="font-bold text-indigo-600 hover:underline">
                                #{{ $order->order_number }}
                            </a>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $order->customer->name ?? 'User Pembeli' }}</div>
                            <div class="text-xs text-gray-400">{{ $order->customer->email ?? '-' }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="text-xs text-gray-700 space-y-1">
                                @foreach ($order->items as $item)
                                    <div>{{ $item->quantity }}x {{ $item->product_name }}</div>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-4 px-4 font-bold text-gray-900 whitespace-nowrap">
                            {{ $order->formatted_total }}
                            <div class="text-[11px] font-normal text-emerald-600">✓ {{ ucfirst($order->payment_status) }}</div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full {{ $order->status_badge }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            <a href="{{ route('merchant.orders.show', $order) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition">
                                Rincian &rarr;
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            <div class="text-4xl mb-2">📦</div>
                            <p class="font-medium text-gray-600">Belum ada pesanan masuk.</p>
                            <p class="text-xs text-gray-400 mt-1">Pesanan dari pembeli akan muncul secara otomatis di halaman ini.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endif
@endsection
