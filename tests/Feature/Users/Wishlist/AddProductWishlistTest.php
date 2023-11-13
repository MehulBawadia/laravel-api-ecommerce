<?php

namespace Tests\Feature\Users\Wishlist;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AddProductWishlistTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $postRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => false]);

        $this->user = $this->signInUser();

        $this->postRoute = route('v1_user.wishlist.store');
    }

    public function test_user_adds_a_product_in_their_wishlist()
    {
        $this->withoutExceptionHandling();

        $product = Product::factory()->create();

        $response = $this->postJsonPayload($this->postRoute, [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200);
        $response->assertSeeText('status');
        $response->assertSeeText(__('response.user.wishlist.success', ['action' => 'added']));
    }

    public function test_user_cannot_add_same_product_in_their_wishlist()
    {
        $this->withoutExceptionHandling();
        $product = Product::factory()->create();
        $this->user->productWishlist()->create(['product_id' => $product->id]);

        $response = $this->postJsonPayload($this->postRoute, [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(422);
        $response->assertSeeText('status');
        $response->assertSeeText(__('response.user.wishlist.product_exists'));
    }

    public function test_product_id_is_required()
    {
        $response = $this->postJsonPayload($this->postRoute, [
            'product_id' => '',
        ]);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['product_id'][0], 'The product is required.');
    }

    public function test_product_must_exist()
    {
        $response = $this->postJsonPayload($this->postRoute, [
            'product_id' => 100,
        ]);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['product_id'][0], 'The product must does not exist in our records.');
    }
}
