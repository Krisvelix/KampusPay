@extends('layouts.app')
@section('title', 'Verifikasi Merchant')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Verifikasi Merchant</h1>

<div class="flex gap-3 mb-4 text-sm">
    <a href="{{ route('admin.merchants.index') }}" class="text-indigo-600">Semua</a>
    @foreach (['pending', 'approved', 'rejected'] as $status)
        <a href="{{ route('admin.merchants.index', ['status' => $status]) }}" class="text-indigo-600">{{ ucfirst($status) }}</a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm [&_td]:p-3 [&_th]:p-3">
        <thead class="bg-gray-100 text-left">
            <tr><th>Toko</th><th>Pemilik</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        @forelse ($merchants as $merchant)
            <tr class="border-t align-top">
                <td>{{ $merchant->store_name }}</td>
                <td>{{ $merchant->user->name }}<br><span class="text-gray-500">{{ $merchant->user->email }}</span></td>
                <td>
                    {{ ucfirst($merchant->status) }}
                    @if ($merchant->status === 'rejected' && $merchant->rejection_reason)
                        <br><span class="text-gray-500">{{ $merchant->rejection_reason }}</span>
                    @endif
                </td>
                <td class="space-y-2">
                    @if ($merchant->status !== 'approved')
                        <form method="POST" action="{{ route('admin.merchants.approve', $merchant) }}">
                            @csrf @method('PATCH')
                            <button class="text-green-700 hover:underline">Setujui</button>
                        </form>
                    @endif
                    @if ($merchant->status !== 'rejected')
                        <form method="POST" action="{{ route('admin.merchants.reject', $merchant) }}" class="flex gap-2">
                            @csrf @method('PATCH')
                            <input name="rejection_reason" placeholder="Alasan penolakan" required class="border rounded px-2 py-1">
                            <button class="text-red-600 hover:underline">Tolak</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-gray-500">Belum ada pengajuan toko.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $merchants->links() }}</div>
@endsection