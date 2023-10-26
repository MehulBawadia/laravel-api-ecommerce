<?php

namespace Tests\Feature\Users\ShippingAddress;

use App\Models\UserAddress;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ShippingAddressTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $getRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => true]);

        $this->user = $this->signInUser();

        $this->getRoute = route('v1_user.shippingAddress');
    }

    public function test_default_shipping_address_added()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);

        $this->assertEquals(1, $this->user->shippingAddress->count());
        $this->assertEquals(UserAddress::SHIPPING, $this->user->shippingAddress->first()->type);
    }
}
