<?php

namespace Tests\Feature\Users\ShippingAddress;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DeleteShippingAddressTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $destroyRoute = null;

    public $user = null;

    public $shippingAddress = null;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => true]);

        $this->user = $this->signInUser();
        $this->shippingAddress = $this->user->shippingAddress()->first();

        $this->destroyRoute = route('v1_user.shippingAddress.destroy', $this->shippingAddress->id);
    }

    public function test_shipping_address_exists()
    {
        $this->withoutExceptionHandling();

        $this->assertNotNull($this->shippingAddress);
    }

    public function test_user_may_delete_the_shipping_address()
    {
        $this->withoutExceptionHandling();

        $response = $this->deleteJson($this->destroyRoute);

        $response->assertSeeText(__('response.user.address.success', ['type' => 'Shipping', 'action' => 'deleted']));
        $this->assertCount(0, $this->user->shippingAddress);
    }

    public function test_user_receives_404_error_if_shipping_address_not_found()
    {
        $this->withoutExceptionHandling();

        $response = $this->deleteJson(route('v1_user.shippingAddress.destroy', 10));
        $response->assertStatus(404);
        $response->assertNotFound();
    }
}
