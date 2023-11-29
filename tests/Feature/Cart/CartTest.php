<?php

namespace Tests\Feature\Cart;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $admin = null;

    public $user = null;

    public function setup(): void
    {
        parent::setUp();

        $this->setUpForCart();
    }

    public function test_no_products_are_added_in_the_cart()
    {
        $this->withoutExceptionHandling();

        $this->getJson(route('v1_user.cart'));

        $this->assertEmpty($this->user->cartProducts);
    }

    public function test_single_product_may_be_added_in_the_cart()
    {
        $this->withoutExceptionHandling();

        $this->assertEmpty($this->user->cartProducts);

        $product = Product::all()->random();
        $response = $this->postJsonPayload(route('v1_user.cart.store'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
        $response->assertCreated();
        $response->assertSeeText(__('response.cart.success', ['actionType' => 'added']));

        $cart = $this->user->fresh()->cartProducts;
        $this->assertNotEmpty($cart);
        $this->assertEquals(3, $cart->first()->quantity);
        $this->assertEquals((float) (3 * $product->rate), (float) ($cart->first()->amount));
    }

    public function test_multiple_products_may_be_added_in_the_cart()
    {
        $this->withoutExceptionHandling();

        $this->assertEmpty($this->user->cartProducts);

        $product1 = Product::all()->random();
        $response = $this->postJsonPayload(route('v1_user.cart.store'), [
            'product_id' => $product1->id,
            'quantity' => 3,
        ]);
        $response->assertCreated();

        $product2 = Product::where('id', '!=', $product1->id)->get()->random();
        $response = $this->postJsonPayload(route('v1_user.cart.store'), [
            'product_id' => $product2->id,
            'quantity' => 2,
        ]);
        $response->assertCreated();

        $cart = $this->user->fresh()->cartProducts;
        $this->assertCount(2, $cart);
        $this->assertEquals((float) (3 * $product1->rate), (float) ($cart->where('product_id', '!=', $product2->id)->first()->amount));
        $this->assertEquals((float) (2 * $product2->rate), (float) ($cart->where('product_id', '!=', $product1->id)->first()->amount));
    }

    public function test_cannot_add_product_that_does_not_exist()
    {
        $response = $this->postJsonPayload(route('v1_user.cart.store'), ['product_id' => 100]);
        $response->assertSeeText(__('response.cart.product_not_found'));
        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_user_updates_the_product_quantity_in_cart()
    {
        $this->withoutExceptionHandling();

        $product = Product::all()->random();
        $this->user->addProductInCart($product);
        $cart = $this->user->fresh()->cartProducts;
        $this->assertEquals(1, $cart->count());

        $response = $this->putJsonPayload(route('v1_user.cart.update', $cart->random()->id), [
            'quantity' => 3,
        ]);
        $response->assertSeeText(__('response.cart.success', ['actionType' => 'updated']));
    }

    public function test_user_may_remove_a_product_from_cart()
    {
        $this->withoutExceptionHandling();

        $product1 = Product::all()->random();
        $this->user->addProductInCart($product1);

        $product2 = Product::where('id', '!=', $product1->id)->get()->random();
        $this->user->addProductInCart($product2);

        $cart = $this->user->fresh()->cartProducts;
        $this->assertEquals(2, $cart->count());

        $response = $this->deleteJson(route('v1_user.cart.delete', $cart->random()->id));
        $response->assertSeeText(__('response.cart.success', ['actionType' => 'removed']));
        $this->assertCount(1, $this->user->fresh()->cartProducts);
    }

    public function test_user_may_empty_the_cart_altogether()
    {
        $this->withoutExceptionHandling();

        $product1 = Product::all()->random();
        $this->user->addProductInCart($product1);

        $product2 = Product::where('id', '!=', $product1->id)->get()->random();
        $this->user->addProductInCart($product2);

        $cart = $this->user->fresh()->cartProducts;
        $this->assertEquals(2, $cart->count());

        $response = $this->deleteJson(route('v1_user.cart.empty'));
        $response->assertSeeText(__('response.cart.empty'));
        $this->assertEmpty($this->user->fresh()->cartProducts);
    }

    private function setUpForCart()
    {
        $this->createUser(['is_admin' => true]);
        $this->user = $this->createUser(['email' => 'userone@example.com', 'is_admin' => false]);
        $this->actingAs($this->user);

        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        \App\Models\Product::factory(10)->create(['brand_id' => $brand->id, 'category_id' => $category->id, 'rate' => 100.00]);
    }
}
