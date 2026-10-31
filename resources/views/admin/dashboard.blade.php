@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Dashboard Admin</h1>
    <a href="{{ route('admin.merchants.index') }}" class="text-sm text-indigo-600 hover:underline">Kelola Verifikasi Toko &rarr;</a>
</div>

@if (isset($pendingMerchants) && $pendingMerchants > 0)
    <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-2xl">⏳</span>
            <div>
                <p class="font-medium text-amber-900">Ada {{ $pendingMerchants }} pengajuan toko menunggu verifikasi!</p>
                <p class="text-sm text-amber-700">Tinjau data toko dan setujui atau tolak pengajuan merchant.</p>
            </div>
        </div>
        <a href="{{ route('admin.merchants.index', ['status' => 'pending']) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium">
            Tinjau Sekarang
        </a>
    </div>
@endif

<div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    @foreach ([
        'Total User' => $totalUsers,
        'Customer' => $totalCustomers,
        'Merchant' => $totalMerchants,
        'Toko Pending' => $pendingMerchants ?? 0,
        'Akun Nonaktif' => $inactiveUsers,
    ] as $label => $value)
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">{{ $label }}</p>
            <p class="text-3xl font-bold mt-1">{{ $value }}</p>
        </div>
    @endforeach
</div>
@endsection