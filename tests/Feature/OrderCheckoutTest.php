<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_adds_approved_products_and_rejects_hidden_products_and_quantities_over_stock(): void
    {
        $farmer = $this->createVerifiedFarmer();
        $available = $this->createMarketplaceProduct($farmer, 3);
        $pending = $this->createMarketplaceProduct($farmer, 3, 'pending');
        $this->post(route('cart.items.store', $available->id))->assertRedirect();

        $this->assertSame([$available->id => 1], session('cart'));

        $this->from(route('marketplace.products.show', $pending->slug))
            ->post(route('cart.items.store', $pending->id))
            ->assertRedirect(route('marketplace.products.show', $pending->slug))
            ->assertSessionHas('error');

        $this->from(route('marketplace.products.show', $available->slug))
            ->post(route('cart.items.store', $available->id), ['qty' => 4])
            ->assertSessionHas('error');

        $this->from(route('cart.index'))
            ->put(route('cart.items.update', $available->id), ['qty' => 4])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertSame([$available->id => 1], session('cart'));
    }

    public function test_checkout_creates_order_snapshots_farmer_and_product_fields_and_reduces_stock(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $farmer = $this->createVerifiedFarmer();
        $product = $this->createMarketplaceProduct($farmer, 8, 'approved', 12500);
        $this->actingAs($customer)->post(route('cart.items.store', $product->id));

        $this->post(route('marketplace.checkout.store'), $this->checkoutData())
            ->assertRedirect();

        $order = Order::query()->with('items')->firstOrFail();
        $this->assertSame($customer->id, $order->customer_id);
        $this->assertSame(12500, $order->total);
        $this->assertSame('transfer', $order->payment_method);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'farmer_id' => $farmer->id,
            'product_name' => $product->name,
            'unit' => $product->unit,
            'price' => 12500,
            'qty' => 1,
        ]);
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertSame([], session('cart', []));
    }

    public function test_preorder_is_shown_through_checkout_and_order_details_and_snapshotted_with_reduced_stock(): void
    {
        $harvestDate = now()->addDays(5);
        $harvestDateString = $harvestDate->toDateString();
        $displayHarvestDate = $harvestDate->format('d/m/Y');
        $customer = User::factory()->create(['role' => 'customer']);
        $farmer = $this->createVerifiedFarmer();
        $product = $this->createMarketplaceProduct($farmer, 8);
        $product->update(['harvest_date' => $harvestDateString]);
        $this->actingAs($customer)->post(route('cart.items.store', $product->id));

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Pra-order')
            ->assertSee('Panen: '.$displayHarvestDate);
        $this->get(route('marketplace.checkout.index'))
            ->assertOk()
            ->assertSee('Pra-order')
            ->assertSee('Panen: '.$displayHarvestDate);

        $this->post(route('marketplace.checkout.store'), $this->checkoutData())
            ->assertRedirect();

        $order = Order::query()->with('items')->firstOrFail();
        $item = $order->items->firstOrFail();
        $this->assertSame(true, $item->is_preorder);
        $this->assertSame($harvestDateString, $item->harvest_date->toDateString());
        $this->assertSame(7, $product->fresh()->stock);
        $this->get(route('customer.orders.show', $order))->assertOk()->assertSee('Panen: '.$displayHarvestDate);

        $this->actingAs($farmer)
            ->get(route('farmer.orders.show', $order))
            ->assertOk()
            ->assertSee('Panen: '.$displayHarvestDate);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Panen: '.$displayHarvestDate);
    }

    public function test_tempo_is_rejected_for_individual_customer_and_allowed_for_business_customer(): void
    {
        $farmer = $this->createVerifiedFarmer();
        $product = $this->createMarketplaceProduct($farmer);
        $individual = User::factory()->create(['role' => 'customer', 'customer_type' => 'individual']);
        $this->actingAs($individual)->post(route('cart.items.store', $product->id));

        $this->from(route('marketplace.checkout.index'))
            ->post(route('marketplace.checkout.store'), [...$this->checkoutData(), 'payment_term' => 'tempo'])
            ->assertSessionHasErrors('payment_term');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $product->fresh()->stock);

        $business = User::factory()->create(['role' => 'customer', 'customer_type' => 'business']);
        $this->actingAs($business)->post(route('cart.items.store', $product->id));
        $this->post(route('marketplace.checkout.store'), [...$this->checkoutData(), 'payment_term' => 'tempo'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_id' => $business->id,
            'payment_term' => 'tempo',
        ]);
    }

    public function test_customer_can_save_buyer_type_from_profile(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'customer_type' => 'individual']);

        $this->actingAs($customer)
            ->patch(route('profile.update'), [
                'name' => $customer->name,
                'email' => $customer->email,
                'customer_type' => 'business',
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'customer_type' => 'business']);
    }

    public function test_two_customers_cannot_checkout_the_same_last_stock_unit(): void
    {
        $farmer = $this->createVerifiedFarmer();
        $product = $this->createMarketplaceProduct($farmer, 1);
        $firstCustomer = User::factory()->create(['role' => 'customer']);
        $secondCustomer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($firstCustomer)->post(route('cart.items.store', $product->id));
        $firstCart = session('cart');
        session()->forget('cart');
        $this->actingAs($secondCustomer)->post(route('cart.items.store', $product->id));
        $secondCart = session('cart');

        session(['cart' => $firstCart]);
        $this->actingAs($firstCustomer)->post(route('marketplace.checkout.store'), $this->checkoutData())->assertRedirect();
        session(['cart' => $secondCart]);
        $this->actingAs($secondCustomer)
            ->post(route('marketplace.checkout.store'), $this->checkoutData())
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(0, $product->fresh()->stock);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->for($owner, 'customer')->create();

        $this->actingAs($otherCustomer)
            ->get(route('customer.orders.show', $order))
            ->assertForbidden();
    }

    public function test_farmer_order_detail_only_contains_their_own_order_items(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $farmerA = $this->createVerifiedFarmer();
        $farmerB = $this->createVerifiedFarmer();
        $productA = $this->createMarketplaceProduct($farmerA);
        $productB = $this->createMarketplaceProduct($farmerB);
        $order = Order::factory()->for($customer, 'customer')->create(['shipping_address' => 'PRIVATE ADDRESS']);
        OrderItem::factory()->for($order)->for($productA)->for($farmerA, 'farmer')->create(['product_name' => 'Produk Petani A']);
        OrderItem::factory()->for($order)->for($productB)->for($farmerB, 'farmer')->create(['product_name' => 'Produk Petani B']);

        $this->actingAs($farmerA)
            ->get(route('farmer.orders.show', $order))
            ->assertOk()
            ->assertSee('Produk Petani A')
            ->assertDontSee('Produk Petani B')
            ->assertDontSee('PRIVATE ADDRESS');
    }

    public function test_guest_is_redirected_to_login_when_opening_checkout(): void
    {
        $this->get(route('marketplace.checkout.index'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_returns_to_checkout_after_logging_in(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->get(route('marketplace.checkout.index'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $customer->email,
            'password' => 'password',
        ])->assertRedirect(route('marketplace.checkout.index'));
    }

    public function test_admin_can_mark_an_order_paid(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['payment_status' => 'unpaid']);

        $this->actingAs($admin)
            ->post(route('admin.orders.mark-paid', $order))
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_status' => 'paid']);
    }

    public function test_product_with_order_items_cannot_be_deleted(): void
    {
        $farmer = $this->createVerifiedFarmer();
        $customer = User::factory()->create(['role' => 'customer']);
        $product = $this->createMarketplaceProduct($farmer);
        $order = Order::factory()->for($customer, 'customer')->create();
        OrderItem::factory()->for($order)->for($product)->for($farmer, 'farmer')->create();

        $this->actingAs($farmer)
            ->delete(route('farmer.products.destroy', $product))
            ->assertRedirect(route('farmer.products.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    private function createVerifiedFarmer(): User
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create(['verified_at' => now()]);

        return $farmer;
    }

    private function createMarketplaceProduct(User $farmer, int $stock = 10, string $status = 'approved', int $price = 12500): Product
    {
        return Product::factory()->for($farmer, 'farmer')->for(Category::factory())->create([
            'status' => $status,
            'show_in_marketplace' => true,
            'stock' => $stock,
            'price_idr' => $price,
        ]);
    }

    /** @return array<string, string> */
    private function checkoutData(): array
    {
        return [
            'recipient_name' => 'Penerima Uji',
            'phone' => '081234567890',
            'address' => 'Jalan Kebun Nomor 1',
            'notes' => '',
            'payment_method' => 'transfer',
            'payment_term' => 'cash',
        ];
    }
}
