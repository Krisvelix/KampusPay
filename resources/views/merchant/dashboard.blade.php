@extends('layouts.app')
@section('title', 'Dashboard Merchant')

@section('content')
    <h1 class="text-2xl font-semibold">Dashboard Merchant</h1>

    @if (! $merchant)
        <div class="mt-6 rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
            Data toko belum ada. Hubungi admin.
        </div>
    @else
        <div class="mt-6 rounded-xl bg-white border p-6">
            <p class="text-sm text-gray-500">Nama Toko</p>
            <p class="text-xl font-semibold mt-1">{{ $merchant->store_name }}</p>

            <p class="text-sm text-gray-500 mt-4">Status Verifikasi</p>

            @if ($merchant->status === 'approved')
                <span class="inline-block mt-1 rounded bg-green-100 text-green-800 px-3 py-1 text-sm">Disetujui</span>
                <p class="mt-3 text-sm text-gray-600">Tokomu sudah aktif.</p>
            @elseif ($merchant->status === 'rejected')
                <span class="inline-block mt-1 rounded bg-red-100 text-red-800 px-3 py-1 text-sm">Ditolak</span>
                @if ($merchant->rejection_reason)
                    <p class="mt-3 text-sm text-gray-600">Alasan: {{ $merchant->rejection_reason }}</p>
                @endif
            @else
                <span class="inline-block mt-1 rounded bg-yellow-100 text-yellow-800 px-3 py-1 text-sm">Menunggu verifikasi</span>
                <p class="mt-3 text-sm text-gray-600">Pengajuan tokomu sedang ditinjau admin.</p>
            @endif
        </div>
    @endif
@endsection