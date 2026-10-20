@extends('layouts.app')
@section('title', 'Riwayat Top Up')

@section('content')
    <h1 class="text-2xl font-semibold">Riwayat Top Up</h1>

    <div class="mt-6 rounded-lg bg-white border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Nominal</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($topups as $topup)
                    <tr>
                        <td class="px-4 py-3">{{ $topup->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($topup->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ ucfirst($topup->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada riwayat top up.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $topups->links() }}</div>
@endsection