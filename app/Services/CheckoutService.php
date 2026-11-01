<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\WalletMutation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutService
{
    public function __construct(private WalletMutationService $mutations)
    {
    }

    public function checkout(User $customer, ?string $notes = null): Collection
    {
        return DB::transaction(function () use ($customer, $notes) {
            $cartItems = $customer->cartItems()->get();

            if ($cartItems->isEmpty()) {
                throw new RuntimeException('Keranjang kosong.');
            }

            $products = Product::whereIn('id', $cartItems->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $merchants = Merchant::with('user')
                ->whereIn('id', $products->pluck('merchant_id')->unique())
                ->get()
                ->keyBy('id');

            $customerWallet = $customer->ensureWallet();
            $orders = collect();

            $groups = $cartItems->groupBy(fn (CartItem $item) => $products[$item->product_id]->merchant_id);

            foreach ($groups as $merchantId => $items) {
                $merchant = $merchants[$merchantId];

                if (! $merchant->isApproved()) {
                    throw new RuntimeException('Toko ' . $merchant->store_name . ' belum aktif.');
                }

                if ($merchant->user_id === $customer->id) {
                    throw new RuntimeException('Tidak bisa membeli dari toko sendiri.');
                }

                $total = '0.00';
                $lines = [];

                foreach ($items as $item) {
                    $product = $products[$item->product_id];

                    if (! $product->is_available || $product->stock < $item->quantity) {
                        throw new RuntimeException('Stok ' . $product->name . ' tidak mencukupi.');
                    }

                    $subtotal = bcmul((string) $product->price, (string) $item->quantity, 2);
                    $total = bcadd($total, $subtotal, 2);
                    $lines[] = [
                        'product' => $product,
                        'quantity' => $item->quantity,
                        'subtotal' => $subtotal,
                    ];
                }

                $orderNumber = $this->makeOrderNumber();

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'merchant_id' => $merchant->id,
                    'customer_id' => $customer->id,
                    'total_amount' => $total,
                    'status' => Order::STATUS_PENDING,
                    'payment_status' => 'paid',
                    'notes' => $notes,
                ]);

                foreach ($lines as $line) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $line['product']->id,
                        'product_name' => $line['product']->name,
                        'price' => $line['product']->price,
                        'quantity' => $line['quantity'],
                        'subtotal' => $line['subtotal'],
                    ]);

                    $line['product']->decrement('stock', $line['quantity']);
                }

                $merchantWallet = $merchant->user->ensureWallet();

                $this->mutations->debit(
                    $customerWallet,
                    $total,
                    WalletMutation::TYPE_PAYMENT,
                    'Pembayaran pesanan ' . $orderNumber,
                    $orderNumber,
                    $merchantWallet
                );

                $this->mutations->credit(
                    $merchantWallet,
                    $total,
                    WalletMutation::TYPE_PAYMENT,
                    'Pembayaran dari ' . $customer->name . ' untuk pesanan ' . $orderNumber,
                    $orderNumber,
                    $customerWallet
                );

                $orders->push($order);
            }

            $customer->cartItems()->delete();

            return $orders;
        });
    }

    private function makeOrderNumber(): string
    {
        do {
            $number = 'KP-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
