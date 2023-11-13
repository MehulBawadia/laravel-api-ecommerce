<?php

namespace Tests\Feature\Users\Wishlist;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DeleteProductWishlistTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $deleteRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => false]);
        $product = Product::factory()->create();

        $this->user = $this->signInUser();
        $this->user->productWishlist()->create(['product_id' => $product->id]);

        $this->deleteRoute = route('v1_user.wishlist.destroy', $product->id);
    }

    public function test_user_removes_a_single_product_from_their_wishlist()
    {
        $this->withoutExceptionHandling();

        $this->assertEquals(1, $this->user->productWishlist->count());

        $response = $this->deleteJson($this->deleteRoute);

        $response->assertStatus(200);
        $response->assertSeeText('status');
        $response->assertSeeText(__('response.user.wishlist.success', ['action' => 'removed']));
    }

    public function test_user_cannot_remove_a_product_from_their_wishlist_if_it_not_exists()
    {
        $this->withoutExceptionHandling();

        $response = $this->deleteJson(route('v1_user.wishlist.destroy', 100));

        $response->assertStatus(404);
        $response->assertSeeText('status');
        $response->assertSeeText(__('response.user.wishlist.not_found'));
    }
}
