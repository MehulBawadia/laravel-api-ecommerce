<?php

namespace Tests\Feature\Users\Checkout;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PlaceOrderTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $postRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        $this->setUpForCart();

        $this->postRoute = route('v1_user.checkout.placeOrder');
    }

    public function test_user_places_an_order()
    {
        $this->withoutExceptionHandling();

        $product = Product::all()->random();
        $this->user->addProductInCart($product);
        $cart = $this->user->fresh()->cartProducts;
        $this->assertEquals(1, $cart->count());

        $billingAddress = $this->user->billingAddress()->first();
        $this->postJson(route('v1_user.checkout.billingAddress'), [
            'billing_address_id' => $billingAddress->id,
        ]);
        $this->assertNotNull(session('user_checkout_billing'));

        $shippingAddress = $this->user->shippingAddress()->first();
        $this->postJson(route('v1_user.checkout.shippingAddress'), [
            'shipping_address_id' => $shippingAddress->id,
        ]);
        $this->assertNotNull(session('user_checkout_shipping'));

        $response = $this->postJsonPayload($this->postRoute);
        $response->assertStatus(201);
        $this->assertDatabaseCount('order_products', 1);
        $response->assertSeeText(__('response.user.order_placed.success'));
    }

    private function setUpForCart()
    {
        $this->createUser(['is_admin' => true]);
        $this->user = $this->createUser(['email' => 'userone@example.com', 'is_admin' => false]);
        $this->actingAs($this->user);

        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        \App\Models\Product::factory(10)->create(['brand_id' => $brand->id, 'category_id' => $category->id]);
    }
}
