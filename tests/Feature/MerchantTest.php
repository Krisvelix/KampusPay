<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_merchant_can_open_dashboard(): void
    {
        $user = User::factory()->merchant()->create();

        $this->actingAs($user)
            ->get(route('merchant.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Merchant');
    }

    public function test_customer_cannot_access_merchant_dashboard(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)
            ->get(route('merchant.dashboard'))
            ->assertForbidden();
    }

    public function test_merchant_can_register_store(): void
    {
        $user = User::factory()->merchant()->create();

        $this->actingAs($user)
            ->get(route('merchant.store.create'))
            ->assertOk()
            ->assertSee('Formulir Pendaftaran Toko');

        $response = $this->actingAs($user)->post(route('merchant.store.store'), [
            'store_name'    => 'Kantin Test Kampus',
            'store_address' => 'Gedung B Lt 2',
            'phone_number'  => '081234567890',
            'description'   => 'Makanan lezat mahasiswa',
        ]);

        $response->assertRedirect(route('merchant.dashboard'));
        $this->assertDatabaseHas('merchants', [
            'user_id'    => $user->id,
            'store_name' => 'Kantin Test Kampus',
            'status'     => Merchant::STATUS_PENDING,
        ]);
    }

    public function test_admin_can_approve_and_reject_store(): void
    {
        $admin = User::factory()->admin()->create();
        $merchantUser = User::factory()->merchant()->create();
        $merchant = Merchant::create([
            'user_id'       => $merchantUser->id,
            'store_name'    => 'Toko Verifikasi',
            'store_address' => 'Kantin A',
            'status'        => Merchant::STATUS_PENDING,
        ]);

        // Admin view merchants
        $this->actingAs($admin)
            ->get(route('admin.merchants.index'))
            ->assertOk()
            ->assertSee('Toko Verifikasi');

        // Admin approve
        $this->actingAs($admin)
            ->patch(route('admin.merchants.approve', $merchant))
            ->assertSessionHas('success');

        $this->assertEquals(Merchant::STATUS_APPROVED, $merchant->fresh()->status);

        // Admin reject
        $this->actingAs($admin)
            ->patch(route('admin.merchants.reject', $merchant), [
                'rejection_reason' => 'Lokasi toko tidak valid',
            ])
            ->assertSessionHas('success');

        $this->assertEquals(Merchant::STATUS_REJECTED, $merchant->fresh()->status);
        $this->assertEquals('Lokasi toko tidak valid', $merchant->fresh()->rejection_reason);
    }

    public function test_approved_merchant_can_crud_product_and_update_stock(): void
    {
        $merchantUser = User::factory()->merchant()->create();
        $merchant = Merchant::create([
            'user_id'       => $merchantUser->id,
            'store_name'    => 'Toko Sukses',
            'store_address' => 'Kantin A',
            'status'        => Merchant::STATUS_APPROVED,
        ]);

        // Create product
        $this->actingAs($merchantUser)
            ->get(route('merchant.products.create'))
            ->assertOk();

        $createRes = $this->actingAs($merchantUser)->post(route('merchant.products.store'), [
            'name'         => 'Nasi Uduk',
            'description'  => 'Enak dan gurih',
            'price'        => 12000,
            'stock'        => 15,
            'is_available' => '1',
        ]);

        $createRes->assertRedirect(route('merchant.products.index'));
        $this->assertDatabaseHas('products', [
            'merchant_id' => $merchant->id,
            'name'        => 'Nasi Uduk',
            'stock'       => 15,
        ]);

        $product = Product::where('name', 'Nasi Uduk')->first();

        // View product detail
        $this->actingAs($merchantUser)
            ->get(route('merchant.products.show', $product))
            ->assertOk()
            ->assertSee('Nasi Uduk')
            ->assertSee('12.000');

        // Quick update stock
        $this->actingAs($merchantUser)
            ->patch(route('merchant.products.update-stock', $product), ['stock' => 50])
            ->assertSessionHas('success');

        $this->assertEquals(50, $product->fresh()->stock);

        // Edit product
        $this->actingAs($merchantUser)
            ->put(route('merchant.products.update', $product), [
                'name'         => 'Nasi Uduk Spesial',
                'description'  => 'Porsi jumbo',
                'price'        => 15000,
                'stock'        => 45,
                'is_available' => '1',
            ])
            ->assertRedirect(route('merchant.products.index'));

        $this->assertEquals('Nasi Uduk Spesial', $product->fresh()->name);

        // Toggle availability
        $this->actingAs($merchantUser)
            ->patch(route('merchant.products.toggle-available', $product));

        $this->assertFalse($product->fresh()->is_available);

        // Delete product
        $this->actingAs($merchantUser)
            ->delete(route('merchant.products.destroy', $product))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_merchant_cannot_modify_other_merchant_product(): void
    {
        $merchant1 = User::factory()->merchant()->create();
        $store1 = Merchant::create([
            'user_id'       => $merchant1->id,
            'store_name'    => 'Toko 1',
            'status'        => Merchant::STATUS_APPROVED,
        ]);
        $product1 = Product::create([
            'merchant_id' => $store1->id,
            'name'        => 'Produk Toko 1',
            'price'       => 10000,
            'stock'       => 5,
        ]);

        $merchant2 = User::factory()->merchant()->create();
        Merchant::create([
            'user_id'       => $merchant2->id,
            'store_name'    => 'Toko 2',
            'status'        => Merchant::STATUS_APPROVED,
        ]);

        // Merchant 2 trying to edit merchant 1 product
        $this->actingAs($merchant2)
            ->get(route('merchant.products.edit', $product1))
            ->assertForbidden();

        $this->actingAs($merchant2)
            ->delete(route('merchant.products.destroy', $product1))
            ->assertForbidden();
    }

    public function test_merchant_can_view_orders_and_update_status(): void
    {
        $merchantUser = User::factory()->merchant()->create();
        $merchant = Merchant::create([
            'user_id'       => $merchantUser->id,
            'store_name'    => 'Toko Order Test',
            'status'        => Merchant::STATUS_APPROVED,
        ]);

        $customer = User::factory()->create();

        $order = Order::create([
            'order_number'   => 'KP-TEST-001',
            'merchant_id'    => $merchant->id,
            'customer_id'    => $customer->id,
            'total_amount'   => 20000,
            'status'         => Order::STATUS_PENDING,
            'payment_status' => 'paid',
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_name' => 'Menu Uji',
            'price'        => 20000,
            'quantity'     => 1,
            'subtotal'     => 20000,
        ]);

        // View orders list
        $this->actingAs($merchantUser)
            ->get(route('merchant.orders.index'))
            ->assertOk()
            ->assertSee('KP-TEST-001');

        // View order detail
        $this->actingAs($merchantUser)
            ->get(route('merchant.orders.show', $order))
            ->assertOk()
            ->assertSee('KP-TEST-001')
            ->assertSee('Menu Uji');

        // Update status to processing
        $this->actingAs($merchantUser)
            ->patch(route('merchant.orders.update-status', $order), [
                'status' => Order::STATUS_PROCESSING,
            ])
            ->assertSessionHas('success');

        $this->assertEquals(Order::STATUS_PROCESSING, $order->fresh()->status);
    }
}
