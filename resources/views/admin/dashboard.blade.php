@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="text-2xl font-semibold">Dashboard Admin</h1>

    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white border p-5">
            <p class="text-sm text-gray-500">Total User</p>
            <p class="text-3xl font-bold mt-1">{{ $totalUsers }}</p>
        </div>
        <div class="rounded-xl bg-white border p-5">
            <p class="text-sm text-gray-500">Customer</p>
            <p class="text-3xl font-bold mt-1">{{ $totalCustomers }}</p>
        </div>
        <div class="rounded-xl bg-white border p-5">
            <p class="text-sm text-gray-500">Merchant</p>
            <p class="text-3xl font-bold mt-1">{{ $totalMerchants }}</p>
        </div>
        <div class="rounded-xl bg-white border p-5">
            <p class="text-sm text-gray-500">User Nonaktif</p>
            <p class="text-3xl font-bold mt-1 text-red-600">{{ $inactiveUsers }}</p>
        </div>
    </div>

    <div class="mt-6 flex gap-3 text-sm">
        <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-indigo-600 text-white px-4 py-2">Kelola User</a>
        <a href="{{ route('admin.merchants.index', ['status' => 'pending']) }}" class="rounded-lg border bg-white px-4 py-2">Verifikasi Merchant</a>
    </div>
@endsection