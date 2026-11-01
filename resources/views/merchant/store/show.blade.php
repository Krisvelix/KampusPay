@extends('layouts.app')
@section('title', 'Detail Toko - ' . $merchant->store_name)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <a href="{{ route('merchant.dashboard') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Dashboard</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Detail Profil Toko</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('merchant.store.edit') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
            ✏️ Edit Data Toko
        </a>
    </div>
</div>

{{-- STORE CARD --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold">{{ $merchant->store_name }}</h2>
                <p class="text-indigo-100 text-sm mt-0.5">Pemilik: {{ $merchant->user->name }} ({{ $merchant->user->email }})</p>
            </div>
            <div>
                @if ($merchant->status === 'approved')
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-500/90 text-white">
                        ✓ Disetujui / Aktif
                    </span>
                @elseif ($merchant->status === 'pending')
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-amber-400 text-amber-950">
                        ⏳ Menunggu Review Admin
                    </span>
                @else
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-500 text-white">
                        ✕ Pengajuan Ditolak
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi di Kampus</span>
            <p class="text-gray-900 font-medium text-sm mt-1">📍 {{ $merchant->store_address ?? '-' }}</p>
        </div>

        <div>
            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak / WhatsApp</span>
            <p class="text-gray-900 font-medium text-sm mt-1">📞 {{ $merchant->phone_number ?? '-' }}</p>
        </div>

        <div>
            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Terdaftar Sejak</span>
            <p class="text-gray-900 font-medium text-sm mt-1">📅 {{ $merchant->created_at->format('d M Y, H:i') }}</p>
        </div>

        <div class="md:col-span-3 border-t pt-4">
            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi Toko</span>
            <p class="text-gray-700 text-sm mt-1 whitespace-pre-line">{{ $merchant->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>

        @if ($merchant->isRejected() && $merchant->rejection_reason)
            <div class="md:col-span-3 bg-red-50 border border-red-200 rounded-xl p-4">
                <span class="block text-xs font-semibold text-red-700 uppercase tracking-wider">Catatan Penolakan dari Admin:</span>
                <p class="text-red-900 text-sm mt-1 font-medium">{{ $merchant->rejection_reason }}</p>
            </div>
        @endif
    </div>
</div>

{{-- STORE PRODUCTS LIST --}}
<div>
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Katalog Produk Toko</h3>
            <p class="text-xs text-gray-500">Daftar produk yang terdaftar di toko ini</p>
        </div>
        @if ($merchant->isApproved())
            <a href="{{ route('merchant.products.create') }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-medium">
                + Tambah Produk
            </a>
        @endif
    </div>

    @if ($products->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-500 text-sm">
            @if ($merchant->isApproved())
                Toko ini belum memiliki produk. <a href="{{ route('merchant.products.create') }}" class="text-indigo-600 underline font-medium">Tambah produk pertama</a>.
            @else
                Produk dapat ditambahkan setelah toko disetujui oleh admin.
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($products as $product)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="h-36 bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl text-gray-300">🍽️</span>
                        @endif
                    </div>
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="font-semibold text-sm text-gray-900 truncate">{{ $product->name }}</h4>
                                <span class="text-[11px] {{ $product->is_available ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                    {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Stok: {{ $product->stock }}</div>
                            <div class="text-sm font-bold text-indigo-600 mt-2">{{ $product->formatted_price }}</div>
                        </div>
                        <div class="mt-3 pt-3 border-t flex justify-end gap-2 text-xs">
                            <a href="{{ route('merchant.products.show', $product) }}" class="text-indigo-600 hover:underline">Detail</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('merchant.products.edit', $product) }}" class="text-gray-600 hover:underline">Edit</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
</div>
@endsection
