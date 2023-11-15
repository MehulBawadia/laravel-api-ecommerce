<?php

namespace Tests\Feature\Users\Checkout;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $getRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => false]);

        $this->user = $this->signInUser();

        $this->getRoute = route('v1_user.checkout.addresses');
    }

    public function test_user_can_see_their_addresses()
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson($this->getRoute);

        $response->assertSeeText('billing_address');
        $response->assertSeeText('shipping_address');
    }
}
