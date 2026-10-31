@extends('layouts.app')
@section('title', 'Dashboard Merchant')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Merchant</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang di panel pengelolaan toko KampusPay Anda.</p>
    </div>
    @if ($merchant && $merchant->isApproved())
        <div class="flex gap-2">
            <a href="{{ route('merchant.products.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                + Tambah Produk
            </a>
            <a href="{{ route('merchant.orders.index') }}" class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition">
                Pesanan Masuk
            </a>
        </div>
    @endif
</div>

{{-- STORE STATUS BANNER --}}
@if (! $merchant)
    <div class="mb-8 bg-white rounded-2xl shadow-sm border border-indigo-100 p-8 text-center">
        <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
            🏪
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Buka Toko KampusPay Anda</h2>
        <p class="text-gray-600 max-w-md mx-auto mb-6 text-sm">
            Anda belum mendaftarkan toko. Daftarkan tokomu sekarang untuk mulai menjual makanan, minuman, atau produk lainnya kepada mahasiswa kampus!
        </p>
        <a href="{{ route('merchant.store.create') }}" class="inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium shadow-md transition">
            Daftarkan Toko Sekarang &rarr;
        </a>
    </div>
@elseif ($merchant->isPending())
    <div class="mb-8 bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center text-2xl shrink-0">
                ⏳
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-amber-900">Pengajuan Toko Sedang Ditinjau</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-200 text-amber-900">Status: Pending</span>
                </div>
                <p class="text-sm text-amber-800 mt-1">
                    Toko <strong>{{ $merchant->store_name }}</strong> sedang dalam proses verifikasi oleh admin KampusPay. Anda dapat mengelola produk setelah toko Anda disetujui.
                </p>
                <div class="mt-4 flex gap-3 text-sm">
                    <a href="{{ route('merchant.store.show') }}" class="font-medium text-amber-900 underline hover:text-amber-950">Lihat Detail Pengajuan</a>
                    <span class="text-amber-400">•</span>
                    <a href="{{ route('merchant.store.edit') }}" class="font-medium text-amber-900 underline hover:text-amber-950">Ubah Data Toko</a>
                </div>
            </div>
        </div>
    </div>
@elseif ($merchant->isRejected())
    <div class="mb-8 bg-red-50 border border-red-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-red-100 text-red-700 rounded-xl flex items-center justify-center text-2xl shrink-0">
                ❌
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-red-900">Pengajuan Toko Ditolak</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-200 text-red-900">Status: Rejected</span>
                </div>
                <p class="text-sm text-red-800 mt-1">
                    Mohon maaf, pengajuan toko Anda ditolak oleh admin.
                </p>
                @if ($merchant->rejection_reason)
                    <div class="mt-3 p-3 bg-white rounded-lg border border-red-200 text-sm text-red-900">
                        <strong>Alasan penolakan:</strong> {{ $merchant->rejection_reason }}
                    </div>
                @endif
                <div class="mt-4">
                    <a href="{{ route('merchant.store.edit') }}" class="inline-block px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                        Perbaiki & Ajukan Ulang Toko
                    </a>
                </div>
            </div>
        </div>
    </div>
@else
    {{-- APPROVED STORE DASHBOARD --}}
    <div class="mb-6 bg-gradient-to-r from-indigo-500 to-indigo-700 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-semibold uppercase tracking-wider bg-indigo-400/40 px-2.5 py-0.5 rounded-full">Toko Terverifikasi</span>
                <span class="text-indigo-200 text-xs">📍 {{ $merchant->store_address ?? 'Kampus' }}</span>
            </div>
            <h2 class="text-2xl font-bold">{{ $merchant->store_name }}</h2>
            <p class="text-indigo-100 text-sm mt-1 max-w-xl">{{ $merchant->description ?? 'Toko resmi mitra KampusPay.' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('merchant.store.show') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-sm font-medium backdrop-blur-sm transition">
                Profil Toko
            </a>
            <a href="{{ route('merchant.store.edit') }}" class="px-4 py-2 bg-white text-indigo-700 hover:bg-indigo-50 rounded-xl text-sm font-medium transition">
                Edit Toko
            </a>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Produk</p>
                <span class="text-lg">📦</span>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalProducts }}</p>
            <p class="text-xs text-gray-500 mt-1"><span class="text-green-600 font-medium">{{ $availableProducts }} aktif</span> • {{ $outOfStockCount }} habis</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stok</p>
                <span class="text-lg">📊</span>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalStock }}</p>
            <p class="text-xs text-gray-500 mt-1">Item tersedia dijual</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pesanan Masuk</p>
                <span class="text-lg">🛍️</span>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalOrders }}</p>
            <p class="text-xs text-gray-500 mt-1">
                @if ($pendingOrdersCount > 0)
                    <span class="text-amber-600 font-semibold">{{ $pendingOrdersCount }} perlu diproses</span>
                @else
                    Semua pesanan diproses
                @endif
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan Selesai</p>
                <span class="text-lg">💰</span>
            </div>
            <p class="text-2xl font-bold text-indigo-600 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Dari transaksi berstatus selesai</p>
        </div>
    </div>

    {{-- RECENT ORDERS & PRODUCTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Orders --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">Pesanan Masuk Terbaru</h3>
                <a href="{{ route('merchant.orders.index') }}" class="text-xs text-indigo-600 hover:underline">Lihat Semua &rarr;</a>
            </div>
            @if ($recentOrders->isEmpty())
                <div class="py-8 text-center text-gray-400 text-sm">
                    Belum ada transaksi/pesanan masuk ke tokomu.
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                            <div>
                                <div class="font-medium text-sm text-gray-900">#{{ $order->order_number }}</div>
                                <div class="text-xs text-gray-500">Oleh: {{ $order->customer->name ?? 'Customer' }} • {{ $order->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-sm text-gray-900">{{ $order->formatted_total }}</div>
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium {{ $order->status_badge }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Products --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">Daftar Produk Toko</h3>
                <a href="{{ route('merchant.products.index') }}" class="text-xs text-indigo-600 hover:underline">Kelola Produk &rarr;</a>
            </div>
            @if ($recentProducts->isEmpty())
                <div class="py-8 text-center text-gray-400 text-sm">
                    Belum ada produk. <a href="{{ route('merchant.products.create') }}" class="text-indigo-600 underline font-medium">Tambah produk sekarang</a>.
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($recentProducts as $product)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold overflow-hidden">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        🛍️
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('merchant.products.show', $product) }}" class="font-medium text-sm text-gray-900 hover:text-indigo-600">
                                        {{ $product->name }}
                                    </a>
                                    <div class="text-xs text-gray-500">Stok: <span class="{{ $product->stock > 0 ? 'text-gray-700 font-medium' : 'text-red-600 font-bold' }}">{{ $product->stock }}</span></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-sm text-indigo-600">{{ $product->formatted_price }}</div>
                                <span class="text-[11px] {{ $product->is_available ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $product->is_available ? '● Tersedia' : '○ Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
@endsection