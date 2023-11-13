<?php

namespace Tests\Feature\Users\Wishlist;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $getRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => false]);

        $this->user = $this->signInUser();

        $this->getRoute = route('v1_user.wishlist');
    }

    public function test_no_products_in_wishlist()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);

        $this->assertEquals(0, $this->user->productWishlist->count());
    }
}
