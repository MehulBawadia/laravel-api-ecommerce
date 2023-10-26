<?php

namespace Tests\Feature\Users\BillingAddress;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DeleteBillingAddressTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $destroyRoute = null;

    public $user = null;

    public $billingAddress = null;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => true]);

        $this->user = $this->signInUser();
        $this->billingAddress = $this->user->billingAddress()->first();

        $this->destroyRoute = route('v1_user.billingAddress.destroy', $this->billingAddress->id);
    }

    public function test_billing_address_exists()
    {
        $this->withoutExceptionHandling();

        $this->assertNotNull($this->billingAddress);
    }

    public function test_user_may_delete_the_billing_address()
    {
        $this->withoutExceptionHandling();

        $response = $this->deleteJson($this->destroyRoute);

        $response->assertSeeText(__('response.user.address.success', ['type' => 'Billing', 'action' => 'deleted']));
        $this->assertCount(0, $this->user->billingAddress);
    }

    public function test_user_receives_404_error_if_billing_address_not_found()
    {
        $this->withoutExceptionHandling();

        $response = $this->deleteJson(route('v1_user.billingAddress.destroy', 10));
        $response->assertStatus(404);
        $response->assertNotFound();
    }
}
