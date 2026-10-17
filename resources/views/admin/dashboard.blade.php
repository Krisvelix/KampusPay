@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Dashboard Admin</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach ([
        'Total User' => $totalUsers,
        'Customer' => $totalCustomers,
        'Merchant' => $totalMerchants,
        'Akun Nonaktif' => $inactiveUsers,
    ] as $label => $value)
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">{{ $label }}</p>
            <p class="text-3xl font-bold mt-1">{{ $value }}</p>
        </div>
    @endforeach
</div>
@endsection