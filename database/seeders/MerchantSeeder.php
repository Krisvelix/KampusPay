<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        $merchantUser = User::where('email', 'merchant@kampuspay.test')->first();
        $customerUser = User::where('email', 'customer@kampuspay.test')->first();

        if (! $merchantUser) {
            return;
        }

        // 1. Create or update sample approved merchant
        $merchant = Merchant::updateOrCreate(
            ['user_id' => $merchantUser->id],
            [
                'store_name'       => 'Kantin Berkah Nusantara',
                'description'      => 'Menyediakan aneka makanan berat nusantara, paket nasi ayam, dan aneka minuman dingin segar untuk mahasiswa dan staf kampus.',
                'store_address'    => 'Kantin Pusat Gedung Bersama Lt. 1, Booth No. 05',
                'phone_number'     => '081298765432',
                'status'           => Merchant::STATUS_APPROVED,
                'rejection_reason' => null,
            ]
        );

        // 2. Create sample products
        $productsData = [
            [
                'name'         => 'Nasi Goreng Spesial Kampus',
                'description'  => 'Nasi goreng dengan bumbu khas, telur mata sapi, sosis, suwiran ayam, dan kerupuk.',
                'price'        => 15000,
                'stock'        => 25,
                'is_available' => true,
            ],
            [
                'name'         => 'Ayam Geprek Sambal Bawang + Nasi',
                'description'  => 'Ayam goreng crispy digeprek dengan sambal bawang pedas nampol, disajikan dengan nasi hangat dan lalapan.',
                'price'        => 18000,
                'stock'        => 20,
                'is_available' => true,
            ],
            [
                'name'         => 'Es Teh Manis Jumbo',
                'description'  => 'Es teh melati segar manis ukuran jumbo 22oz.',
                'price'        => 5000,
                'stock'        => 50,
                'is_available' => true,
            ],
            [
                'name'         => 'Kopi Susu Gula Aren',
                'description'  => 'Espresso robusta lokal dipadukan susu segar dan gula aren alami.',
                'price'        => 12000,
                'stock'        => 15,
                'is_available' => true,
            ],
            [
                'name'         => 'Mie Goreng Telur Kornet',
                'description'  => 'Indomie goreng double dengan topping telur dadar dan kornet gurih.',
                'price'        => 13000,
                'stock'        => 0, // out of stock demo
                'is_available' => false,
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $prod) {
            $createdProducts[] = Product::updateOrCreate(
                ['merchant_id' => $merchant->id, 'name' => $prod['name']],
                $prod
            );
        }

        // 3. Create sample incoming orders
        if ($customerUser && count($createdProducts) >= 3) {
            // Order 1: Pending order
            $order1 = Order::updateOrCreate(
                ['order_number' => 'KP-20261019-001'],
                [
                    'merchant_id'    => $merchant->id,
                    'customer_id'    => $customerUser->id,
                    'total_amount'   => 35000,
                    'status'         => Order::STATUS_PENDING,
                    'payment_status' => 'paid',
                    'notes'          => 'Tolong jangan terlalu pedas ya kak.',
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order1->id, 'product_id' => $createdProducts[0]->id],
                [
                    'product_name' => $createdProducts[0]->name,
                    'price'        => 15000,
                    'quantity'     => 1,
                    'subtotal'     => 15000,
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order1->id, 'product_id' => $createdProducts[2]->id],
                [
                    'product_name' => $createdProducts[2]->name,
                    'price'        => 5000,
                    'quantity'     => 2,
                    'subtotal'     => 10000,
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order1->id, 'product_id' => $createdProducts[3]->id],
                [
                    'product_name' => $createdProducts[3]->name,
                    'price'        => 10000,
                    'quantity'     => 1,
                    'subtotal'     => 10000,
                ]
            );

            // Order 2: Completed order
            $order2 = Order::updateOrCreate(
                ['order_number' => 'KP-20261019-002'],
                [
                    'merchant_id'    => $merchant->id,
                    'customer_id'    => $customerUser->id,
                    'total_amount'   => 23000,
                    'status'         => Order::STATUS_COMPLETED,
                    'payment_status' => 'paid',
                    'notes'          => null,
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order2->id, 'product_id' => $createdProducts[1]->id],
                [
                    'product_name' => $createdProducts[1]->name,
                    'price'        => 18000,
                    'quantity'     => 1,
                    'subtotal'     => 18000,
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order2->id, 'product_id' => $createdProducts[2]->id],
                [
                    'product_name' => $createdProducts[2]->name,
                    'price'        => 5000,
                    'quantity'     => 1,
                    'subtotal'     => 5000,
                ]
            );
        }
    }
}
