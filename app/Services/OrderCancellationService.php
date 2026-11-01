<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\WalletMutation;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderCancellationService
{
    public function __construct(private WalletMutationService $mutations)
    {
    }

    public function cancel(Order $order, array $allowedStatuses = [Order::STATUS_PENDING]): Order
    {
        return DB::transaction(function () use ($order, $allowedStatuses) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (! in_array($locked->status, $allowedStatuses, true)) {
                throw new RuntimeException('Pesanan dengan status ' . ucfirst($locked->status) . ' tidak bisa dibatalkan.');
            }

            $locked->load('items', 'merchant.user', 'customer');

            $wasPaid = WalletMutation::where('reference', $locked->order_number)
                ->where('type', WalletMutation::TYPE_PAYMENT)
                ->exists();

            foreach ($locked->items as $item) {
                if ($item->product_id) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }

            if ($wasPaid) {
                $merchantWallet = $locked->merchant->user->ensureWallet();
                $customerWallet = $locked->customer->ensureWallet();
                $reference = 'RF-' . $locked->order_number;

                $this->mutations->debit(
                    $merchantWallet,
                    $locked->total_amount,
                    WalletMutation::TYPE_REFUND,
                    'Refund pesanan ' . $locked->order_number,
                    $reference,
                    $customerWallet
                );

                $this->mutations->credit(
                    $customerWallet,
                    $locked->total_amount,
                    WalletMutation::TYPE_REFUND,
                    'Refund pesanan ' . $locked->order_number,
                    $reference,
                    $merchantWallet
                );
            }

            $locked->update([
                'status' => Order::STATUS_CANCELLED,
                'payment_status' => $wasPaid ? 'refunded' : $locked->payment_status,
            ]);

            return $locked;
        });
    }
}