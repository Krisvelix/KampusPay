<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderCancellationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant) {
            return redirect()->route('merchant.store.create');
        }

        if (! $merchant->isApproved()) {
            return view('merchant.orders.index', compact('merchant'))->with('orders', null);
        }

        $query = $merchant->orders()->with(['customer', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'ilike', $search)
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'ilike', $search)
                           ->orWhere('email', 'ilike', $search);
                    });
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        $statusCounts = [
            'all'        => $merchant->orders()->count(),
            'pending'    => $merchant->orders()->where('status', Order::STATUS_PENDING)->count(),
            'processing' => $merchant->orders()->where('status', Order::STATUS_PROCESSING)->count(),
            'completed'  => $merchant->orders()->where('status', Order::STATUS_COMPLETED)->count(),
            'cancelled'  => $merchant->orders()->where('status', Order::STATUS_CANCELLED)->count(),
        ];

        return view('merchant.orders.index', compact('merchant', 'orders', 'statusCounts'));
    }

    public function show(Order $order): View|RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant || $order->merchant_id !== $merchant->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load(['customer', 'items.product']);

        return view('merchant.orders.show', compact('merchant', 'order'));
    }

    public function updateStatus(Request $request, Order $order, OrderCancellationService $cancellation): RedirectResponse
    {
        $merchant = auth()->user()->merchant;

        if (! $merchant || $order->merchant_id !== $merchant->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $data = $request->validate([
            'status' => ['required', Rule::in([
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])],
        ]);

        $target = $data['status'];

        $transitions = [
            Order::STATUS_PENDING => [Order::STATUS_PROCESSING, Order::STATUS_CANCELLED],
            Order::STATUS_PROCESSING => [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED],
        ];

        if (! in_array($target, $transitions[$order->status] ?? [], true)) {
            return back()->with('error', 'Status pesanan tidak bisa diubah dari ' . ucfirst($order->status) . ' ke ' . ucfirst($target) . '.');
        }

        if ($target === Order::STATUS_CANCELLED) {
            try {
                $cancellation->cancel($order, [Order::STATUS_PENDING, Order::STATUS_PROCESSING]);
            } catch (RuntimeException $e) {
                return back()->with('error', $e->getMessage());
            }

            return back()->with('success', "Pesanan #{$order->order_number} dibatalkan dan saldo customer dikembalikan.");
        }

        $order->update(['status' => $target]);

        return back()->with('success', "Status pesanan #{$order->order_number} berhasil diubah menjadi " . ucfirst($target) . '.');
    }
}