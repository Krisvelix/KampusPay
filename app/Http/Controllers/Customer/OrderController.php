<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WalletMutation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()
            ->with('merchant')
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->customer_id === $request->user()->id, 403);

        $order->load('items', 'merchant');

        $payment = WalletMutation::where('reference', $order->order_number)
            ->where('wallet_id', $request->user()->ensureWallet()->id)
            ->first();

        return view('customer.orders.show', compact('order', 'payment'));
    }
}