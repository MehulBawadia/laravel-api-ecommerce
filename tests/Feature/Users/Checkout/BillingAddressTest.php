<?php

namespace Tests\Feature\Users\Checkout;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BillingAddressTest extends TestCase
{
    use LazilyRefreshDatabase;

    public $postRoute = null;

    protected $user;

    public function setup(): void
    {
        parent::setUp();

        $this->createUser(['is_admin' => false]);

        $this->user = $this->signInUser();

        $this->postRoute = route('v1_user.checkout.billingAddress');
    }

    public function test_user_selects_a_billing_address()
    {
        $this->withoutExceptionHandling();
        $address = $this->user->billingAddress()->first();

        $this->assertNull(session('user_checkout_billing'));

        $response = $this->postJson($this->postRoute, [
            'billing_address_id' => $address->id,
        ]);

        $this->assertNotNull(session('user_checkout_billing'));
        $this->assertEquals(session('user_checkout_billing'), $address);

        $response->assertStatus(200);
        $response->assertSeeText(__('response.user.checkout_address.success', ['type' => 'Billing']));
    }

    public function test_user_sees_invalid_address_message()
    {
        $response = $this->postJson($this->postRoute, [
            'billing_address_id' => 50,
        ]);

        $response->assertStatus(422);
        $response->assertUnprocessable();

        $errors = $response->json()['errors'];
        $this->assertEquals($errors['billing_address_id'][0], 'Selected billing address not found in our records.');
    }
}
