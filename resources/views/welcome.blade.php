@extends('layouts.app')

@section('title', 'Selamat Datang di KampusPay')

@section('content')
<div class="py-8 md:py-16">
    <div class="text-center max-w-2xl mx-auto mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
            Selamat Datang di <span class="text-indigo-600">KampusPay</span>
        </h1>
        <p class="mt-4 text-lg text-gray-600">
            Sistem pembayaran digital praktis untuk civitas akademika dan merchant kampus. Transaksi cepat, aman, dan terintegrasi.
        </p>

        <div class="mt-8 flex justify-center gap-4">
            @auth
                <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                   class="rounded-lg bg-indigo-600 px-6 py-3 text-white font-medium shadow hover:bg-indigo-700 transition">
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="rounded-lg bg-indigo-600 px-6 py-3 text-white font-medium shadow hover:bg-indigo-700 transition">
                    Masuk Sekarang
                </a>
                <a href="{{ route('register') }}"
                   class="rounded-lg bg-white px-6 py-3 text-indigo-600 font-medium border border-indigo-200 shadow-sm hover:bg-gray-50 transition">
                    Daftar Akun
                </a>
            @endauth
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-bold text-xl mb-4">
                💳
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Transaksi Praktis</h2>
            <p class="text-gray-600 text-sm">
                Nikmati kemudahan transaksi nontunai di seluruh kantin dan merchant kampus hanya dengan satu akun.
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-bold text-xl mb-4">
                🏪
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Untuk Merchant Kampus</h2>
            <p class="text-gray-600 text-sm">
                Kelola transaksi penjualan dan penerimaan pembayaran dengan mudah melalui dasbor khusus merchant.
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-bold text-xl mb-4">
                🔒
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Aman & Terpercaya</h2>
            <p class="text-gray-600 text-sm">
                Sistem keamanan terverifikasi untuk melindungi data akun dan kelancaran setiap transaksi Anda.
            </p>
        </div>
    </div>
</div>
@endsection
