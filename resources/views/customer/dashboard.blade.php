@extends('layouts.app')
@section('title', 'Dashboard Customer')

@section('content')
    <h1 class="text-2xl font-semibold">Dashboard Customer</h1>

    <div class="mt-6 rounded-xl bg-indigo-600 text-white p-6">
        <p class="text-sm opacity-80">Saldo KampusPay</p>
        <p class="text-3xl font-bold mt-1">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
        <a href="{{ route('customer.topup.create') }}"
           class="inline-block mt-4 rounded-lg bg-white text-indigo-600 px-4 py-2 text-sm font-medium">Top Up</a>
    </div>

    <div class="mt-8">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold">Top up terakhir</h2>
            <a href="{{ route('customer.topup.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua</a>
        </div>

        <div class="mt-3 rounded-lg bg-white border divide-y">
            @forelse ($recentTopups as $topup)
                <div class="px-4 py-3 flex items-center justify-between text-sm">
                    <span>{{ $topup->created_at->format('d M Y H:i') }}</span>
                    <span class="font-medium">Rp {{ number_format($topup->amount, 0, ',', '.') }}</span>
                </div>
            @empty
                <p class="px-4 py-6 text-center text-sm text-gray-500">Belum ada top up.</p>
            @endforelse
        </div>
    </div>
@endsection