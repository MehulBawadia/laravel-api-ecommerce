<?php

namespace Tests\Feature\Users\Checkout;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

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
        $this->postJsonPayload(route('cart.store', $product->id), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
        $this->assertNotNull(session('cart'));

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

        $fakeData = $this->dummyStripeOrderChargeData([
            'amount' => session('cart.total_cart_amount') * 100,
        ]);

        Http::fake([
            'https://api.stripe.com/v1/charges' => Http::response($fakeData, 200),
        ]);

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

    private function dummyStripeOrderChargeData($overrideData = [])
    {
        $data = [
            'id' => 'ch_UKUksejSGbcg',
            'object' => 'charge',
            'amount' => 100,
            'currency' => 'inr',
            'customer' => $this->user->stripe_customer_id,
        ];

        return json_encode(array_merge($data, $overrideData));
    }
}
