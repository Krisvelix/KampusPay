<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KampusPay')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">
    <nav class="bg-white border-b">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="/" class="font-bold text-indigo-600">KampusPay</a>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm hover:text-indigo-600">Dashboard</a>
                        <a href="{{ route('admin.merchants.index') }}" class="text-sm hover:text-indigo-600">Verifikasi Merchant</a>
                        <a href="{{ route('admin.users.index') }}" class="text-sm hover:text-indigo-600">Users</a>
                    @elseif (auth()->user()->isMerchant())
                        <a href="{{ route('merchant.dashboard') }}" class="text-sm hover:text-indigo-600">Dashboard</a>
                        @if (auth()->user()->merchant)
                            <a href="{{ route('merchant.store.show') }}" class="text-sm hover:text-indigo-600">Toko Saya</a>
                            @if (auth()->user()->merchant->isApproved())
                                <a href="{{ route('merchant.products.index') }}" class="text-sm hover:text-indigo-600">Produk</a>
                                <a href="{{ route('merchant.orders.index') }}" class="text-sm hover:text-indigo-600">Pesanan Masuk</a>
                            @endif
                        @else
                            <a href="{{ route('merchant.store.create') }}" class="text-sm text-indigo-600 font-medium hover:underline">+ Buka Toko</a>
                        @endif
                    @elseif (auth()->user()->isCustomer())
                        <a href="{{ route('customer.dashboard') }}" class="text-sm hover:text-indigo-600">Dashboard</a>
                    @endif
                @endauth
            </div>
            <div class="flex items-center gap-4 text-sm">
                @auth
                    <span>{{ auth()->user()->name }}
                        <span class="ml-1 rounded bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs">{{ ucfirst(auth()->user()->role) }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-red-600 hover:underline">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-indigo-600">Login</a>
                    <a href="{{ route('register') }}" class="hover:text-indigo-600">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>