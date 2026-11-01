@extends('layouts.app')
@section('title', 'Edit Informasi Toko')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('merchant.store.show') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Profil Toko</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Informasi Toko</h1>
        <p class="text-sm text-gray-600">Perbarui profil dan alamat tokomu di KampusPay.</p>
    </div>

    @if ($merchant->isRejected())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-900">
            <p class="font-semibold">Perhatian: Pengajuan toko Anda sebelumnya ditolak.</p>
            @if ($merchant->rejection_reason)
                <p class="mt-1">Alasan: {{ $merchant->rejection_reason }}</p>
            @endif
            <p class="mt-2 text-xs text-red-700">Dengan menyimpan perubahan di bawah, status pengajuan toko Anda akan otomatis direset menjadi <strong>Pending</strong> agar admin dapat meninjau kembali.</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <form method="POST" action="{{ route('merchant.store.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Toko <span class="text-red-500">*</span></label>
                <input id="store_name" type="text" name="store_name" value="{{ old('store_name', $merchant->store_name) }}" required
                       class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('store_name') border-red-500 @enderror">
                @error('store_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="store_address" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Toko di Kampus <span class="text-red-500">*</span></label>
                <input id="store_address" type="text" name="store_address" value="{{ old('store_address', $merchant->store_address) }}" required
                       class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('store_address') border-red-500 @enderror">
                @error('store_address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number', $merchant->phone_number) }}"
                       class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('phone_number') border-red-500 @enderror">
                @error('phone_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Toko</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('description') border-red-500 @enderror">{{ old('description', $merchant->description) }}</textarea>
                @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t flex items-center justify-end gap-3">
                <a href="{{ route('merchant.store.show') }}" class="px-5 py-2.5 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
