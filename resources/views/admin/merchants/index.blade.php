@extends('layouts.app')
@section('title', 'Verifikasi Merchant')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Verifikasi Pengajuan Toko Merchant</h1>

<div class="flex gap-3 mb-4 text-sm">
    <a href="{{ route('admin.merchants.index') }}" class="px-3 py-1.5 rounded-lg {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-white border text-gray-700' }}">Semua</a>
    @foreach (['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $status => $label)
        <a href="{{ route('admin.merchants.index', ['status' => $status]) }}"
           class="px-3 py-1.5 rounded-lg {{ request('status') === $status ? 'bg-indigo-600 text-white' : 'bg-white border text-gray-700' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm [&_td]:p-4 [&_th]:p-4">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th>Toko & Lokasi</th>
                <th>Pemilik</th>
                <th>Status</th>
                <th>Aksi Verifikasi</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($merchants as $merchant)
            <tr class="border-t align-top">
                <td>
                    <div class="font-semibold text-gray-900">{{ $merchant->store_name }}</div>
                    @if ($merchant->store_address)
                        <div class="text-xs text-gray-500 mt-1">📍 {{ $merchant->store_address }}</div>
                    @endif
                    @if ($merchant->phone_number)
                        <div class="text-xs text-gray-500">📞 {{ $merchant->phone_number }}</div>
                    @endif
                    @if ($merchant->description)
                        <div class="text-xs text-gray-600 mt-1 italic">{{ Str::limit($merchant->description, 80) }}</div>
                    @endif
                </td>
                <td>
                    <div class="font-medium text-gray-900">{{ $merchant->user->name }}</div>
                    <div class="text-xs text-gray-500">{{ $merchant->user->email }}</div>
                </td>
                <td>
                    @if ($merchant->status === 'approved')
                        <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                    @elseif ($merchant->status === 'pending')
                        <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Menunggu Review</span>
                    @else
                        <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                        @if ($merchant->rejection_reason)
                            <div class="text-xs text-red-600 mt-1 font-medium">Alasan: {{ $merchant->rejection_reason }}</div>
                        @endif
                    @endif
                </td>
                <td class="space-y-2">
                    <div class="flex flex-col gap-2">
                        @if ($merchant->status !== 'approved')
                            <form method="POST" action="{{ route('admin.merchants.approve', $merchant) }}">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium">
                                    ✓ Setujui Toko
                                </button>
                            </form>
                        @endif
                        @if ($merchant->status !== 'rejected')
                            <form method="POST" action="{{ route('admin.merchants.reject', $merchant) }}" class="flex flex-col gap-1">
                                @csrf @method('PATCH')
                                <input name="rejection_reason" placeholder="Alasan penolakan" required class="border rounded px-2 py-1 text-xs w-48">
                                <button class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium self-start">
                                    ✕ Tolak Toko
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center py-8 text-gray-500">Belum ada pengajuan toko.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $merchants->links() }}</div>
@endsection