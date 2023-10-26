<?php

namespace Tests\Feature\Users\BillingAddress;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BillingAddressTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $getRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => true]);

        $this->user = $this->signInUser();

        $this->getRoute = route('v1_user.billingAddress');
    }

    public function test_default_billing_address_added()
    {
        $this->withoutExceptionHandling();

        $this->getJson($this->getRoute);

        $this->assertEquals(1, $this->user->billingAddress->count());
    }
}
