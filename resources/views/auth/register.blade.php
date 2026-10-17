@extends('layouts.app')
@section('title', 'Register')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-6">
    <h1 class="text-xl font-semibold mb-4">Daftar Akun</h1>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm mb-1">Nama</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2">
            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="block text-sm mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="role" class="block text-sm mb-1">Daftar sebagai</label>
            <select id="role" name="role" class="w-full border rounded-lg px-3 py-2">
                <option value="customer" @selected(old('role') === 'customer')>Customer (pembeli)</option>
                <option value="merchant" @selected(old('role') === 'merchant')>Merchant (penjual)</option>
            </select>
            @error('role') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="block text-sm mb-1">Password</label>
            <input id="password" type="password" name="password" required class="w-full border rounded-lg px-3 py-2">
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm mb-1">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <button class="w-full bg-indigo-600 text-white rounded-lg py-2 hover:bg-indigo-700">Daftar</button>
    </form>
    <p class="text-sm mt-4">Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600">Login</a></p>
</div>
@endsection