@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-6">
    <h1 class="text-xl font-semibold mb-4">Login</h1>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border rounded-lg px-3 py-2">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="block text-sm mb-1">Password</label>
            <input id="password" type="password" name="password" required
                   class="w-full border rounded-lg px-3 py-2">
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember"> Ingat saya
        </label>
        <button class="w-full bg-indigo-600 text-white rounded-lg py-2 hover:bg-indigo-700">Login</button>
    </form>
    <p class="text-sm mt-4">Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600">Daftar</a></p>
</div>
@endsection