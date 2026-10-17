@extends('layouts.app')
@section('title', 'Kelola User')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Kelola User</h1>

<form method="GET" class="flex flex-wrap gap-2 mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email"
           class="border rounded-lg px-3 py-2">
    <select name="role" class="border rounded-lg px-3 py-2">
        <option value="">Semua role</option>
        @foreach (['admin', 'merchant', 'customer'] as $role)
            <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
        @endforeach
    </select>
    <button class="bg-indigo-600 text-white rounded-lg px-4 py-2">Filter</button>
</form>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm [&_td]:p-3 [&_th]:p-3">
        <thead class="bg-gray-100 text-left">
            <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        @forelse ($users as $user)
            <tr class="border-t">
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ ucfirst($user->role) }}</td>
                <td>
                    @if ($user->is_active)
                        <span class="text-green-700">Aktif</span>
                    @else
                        <span class="text-red-600">Nonaktif</span>
                    @endif
                </td>
                <td>
                    @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-indigo-600 hover:underline">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-gray-500">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection