@extends('layouts.app')
@section('title', 'Top Up Saldo')

@section('content')
    <h1 class="text-2xl font-semibold">Top Up Saldo</h1>
    <p class="text-gray-500 mt-1">Saldo saat ini: Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>

    <form method="POST" action="{{ route('customer.topup.store') }}"
          class="mt-6 max-w-md rounded-xl bg-white border p-6 space-y-4">
        @csrf
        <div>
            <label for="amount" class="block text-sm font-medium">Nominal (Rp)</label>
            <input id="amount" name="amount" type="number" step="1" value="{{ old('amount') }}"
                   class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Contoh: 50000">
            @error('amount')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Minimal Rp 10.000, maksimal Rp 5.000.000.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ([20000, 50000, 100000, 200000] as $preset)
                <button type="button" onclick="document.getElementById('amount').value={{ $preset }}"
                        class="rounded-lg border px-3 py-1 text-sm hover:bg-gray-50">
                    Rp {{ number_format($preset, 0, ',', '.') }}
                </button>
            @endforeach
        </div>

        <button class="w-full rounded-lg bg-indigo-600 text-white py-2 font-medium">Top Up Sekarang</button>
    </form>
@endsection