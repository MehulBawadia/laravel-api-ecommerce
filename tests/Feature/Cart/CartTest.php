<?php

namespace Tests\Feature\Admin\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public $admin = null;

    public function setup(): void
    {
        parent::setUp();

        $this->setUpForCart();
    }

    public function test_no_products_are_added_in_the_cart()
    {
        $this->withoutExceptionHandling();

        $this->getJson(route('cart'));

        $this->assertNull(session('product_cart'));
    }

    public function test_single_product_may_be_added_in_the_cart()
    {
        $this->withoutExceptionHandling();

        $product = Product::all()->random();
        $this->postJsonPayload(route('cart.store', $product->id), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->assertNotNull(session('cart'));
        $this->assertCount(1, session('cart.products'));
        $this->assertEquals(3, session('cart.products')[$product->slug]['quantity']);
        $this->assertEquals($product->rate * 3, session('cart.products')[$product->slug]['total']);
    }

    public function test_multiple_products_may_be_added_in_the_cart()
    {
        $this->withoutExceptionHandling();

        $product1 = Product::all()->random();
        $this->postJsonPayload(route('cart.store', $product1->id), [
            'product_id' => $product1->id,
            'quantity' => 3,
        ]);
        $this->assertNotNull(session('cart'));
        $this->assertEquals(3, session('cart.products')[$product1->slug]['quantity']);
        $this->assertEquals($product1->rate * 3, session('cart.products')[$product1->slug]['total']);

        $product2 = Product::where('id', '!=', $product1->id)->get()->random();
        $this->postJsonPayload(route('cart.store', $product2->id), [
            'product_id' => $product2->id,
            'quantity' => 2,
        ]);
        $this->assertEquals(2, session('cart.products')[$product2->slug]['quantity']);
        $this->assertEquals($product2->rate * 2, session('cart.products')[$product2->slug]['total']);

        $this->assertCount(2, session('cart.products'));
    }

    public function test_cannot_add_product_that_does_not_exist()
    {
        $response = $this->postJsonPayload(route('cart.store', 100));
        $response->assertSeeText(__('response.cart.product_not_found'));
        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_user_updates_the_product_quantity_in_cart()
    {
        $this->withoutExceptionHandling();

        $product = Product::all()->random();
        $this->dummyCartData($product);
        $this->assertEquals(1, session("cart.products.$product->slug.quantity"));

        $response = $this->putJsonPayload(route('cart.update', $product->id), [
            'quantity' => 3,
        ]);
        $response->assertSeeText(__('response.cart.product_updated'));

        $product2 = Product::where('id', '!=', $product->id)->get()->random();
        $this->dummyCartData($product2);
        $this->assertEquals(1, session("cart.products.$product2->slug.quantity"));

        $response = $this->putJsonPayload(route('cart.update', $product2->id), [
            'quantity' => 2,
        ]);
        $this->assertEquals(2, session("cart.products.$product2->slug.quantity"));
        $response->assertSeeText(__('response.cart.product_updated'));
    }

    public function test_user_may_remove_a_product_from_cart()
    {
        $this->withoutExceptionHandling();

        $product = Product::all()->random();
        $this->dummyCartData($product);
        $product2 = Product::where('id', '!=', $product->id)->get()->random();
        $this->dummyCartData($product2);
        $this->assertCount(2, session('cart.products'));

        $response = $this->deleteJson(route('cart.delete', $product->id));
        $response->assertSeeText(__('response.cart.product_removed'));
        $this->assertCount(1, session('cart.products'));
    }

    public function test_user_may_empty_the_cart_altogether()
    {
        $this->withoutExceptionHandling();

        $product = Product::all()->random();
        $this->dummyCartData($product);
        $product2 = Product::where('id', '!=', $product->id)->get()->random();
        $this->dummyCartData($product2);
        $this->assertCount(2, session('cart.products'));

        $response = $this->deleteJson(route('cart.empty'));
        $response->assertSeeText(__('response.cart.empty'));
        $this->assertNull(session('cart'));
    }

    protected function dummyCartData($product, $data = [])
    {
        $cart = session('cart') ?? [];

        $cart['products'][$product->slug] = array_merge([
            'id' => $product->id,
            'quantity' => 1,
            'rate' => $product->rate,
            'total' => (float) ($product->rate * (int) 1),
            'name' => $product->name,
        ], $data);

        session(['cart' => $cart]);
    }

    private function setUpForCart()
    {
        $this->createUser(['is_admin' => true]);
        $this->createUser(['email' => 'userone@example.com', 'is_admin' => false]);

        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        \App\Models\Product::factory(10)->create(['brand_id' => $brand->id, 'category_id' => $category->id]);
    }
}
