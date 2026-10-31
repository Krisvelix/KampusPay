@extends('layouts.app')
@section('title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('merchant.products.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Daftar Produk</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Produk Baru</h1>
        <p class="text-sm text-gray-500">Masukkan detail produk untuk toko {{ $merchant->store_name }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <form method="POST" action="{{ route('merchant.products.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Nasi Goreng Spesial Kampus"
                       class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('name') border-red-500 @enderror">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">Rp</span>
                        <input id="price" type="number" name="price" value="{{ old('price') }}" required min="0" step="500"
                               placeholder="15000"
                               class="w-full border rounded-lg pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('price') border-red-500 @enderror">
                    </div>
                    @error('price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok Awal <span class="text-red-500">*</span></label>
                    <input id="stock" type="number" name="stock" value="{{ old('stock', 10) }}" required min="0"
                           placeholder="10"
                           class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('stock') border-red-500 @enderror">
                    @error('stock') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Foto / Gambar Produk</label>
                <input id="image" type="file" name="image" accept="image/*"
                       class="w-full border rounded-lg px-3.5 py-2 text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('image') border-red-500 @enderror">
                <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                @error('image') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Bahan, porsi, rasa, atau catatan menu..."
                          class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input id="is_available" type="checkbox" name="is_available" value="1"
                       @checked(old('is_available', true))
                       class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                <label for="is_available" class="text-sm font-medium text-gray-700">
                    Produk langsung aktif dan tersedia untuk dipesan
                </label>
            </div>

            <div class="pt-4 border-t flex items-center justify-end gap-3">
                <a href="{{ route('merchant.products.index') }}" class="px-5 py-2.5 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
